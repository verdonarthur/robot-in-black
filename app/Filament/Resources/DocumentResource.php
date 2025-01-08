<?php

namespace App\Filament\Resources;

use App\Filament\Imports\DocumentCsvImporter;
use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\Pages\CreateDocument;
use App\Filament\Resources\DocumentResource\Pages\EditDocument;
use App\Filament\Resources\DocumentResource\Pages\ListDocuments;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Document;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-m-document-duplicate';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('id_agent')
                    ->relationship(
                        name: 'agent',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->where('id_user', auth()->id()),
                    )
                    ->required()
                    ->preload(),
                MarkdownEditor::make('content')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('agent.name')
                    ->label('Agent')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('agent')
                    ->relationship('agent', 'name'),
            ])
            ->headerActions([
                Action::make('Bulk Import')
                    ->color('gray')
                    ->form([
                        Select::make('id_agent')
                            ->relationship(
                                name: 'agent',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->where('id_user', auth()->id()),
                            )
                            ->required()
                            ->preload(),
                        FileUpload::make('documents')
                            ->multiple()
                            ->storeFiles(false)
                            ->maxSize(1024)
                            ->maxFiles(10)
                    ])->action(function (array $data): void {
                        /** @var TemporaryUploadedFile $document */
                        foreach ($data['documents'] as $document) {
                            if(! collect(['text/plain', 'application/json', 'application/xml'])->contains($document->getMimeType())) {
                                continue;
                            }

                            $newDocument = new Document();
                            $newDocument->title = $document->getClientOriginalName();
                            $newDocument->content = $document->getContent();
                            $newDocument->id_user = auth()->id();
                            $newDocument->id_agent = $data['id_agent'];

                            $newDocument->save();
                        }
                    }),
                ImportAction::make()
                    ->label('CSV import')
                    ->color('gray')
                    ->importer(DocumentCsvImporter::class),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->where('id_user', auth()->id()));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }
}
