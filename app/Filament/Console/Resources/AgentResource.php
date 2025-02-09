<?php

namespace App\Filament\Console\Resources;

use App\Enums\ChatOptions;
use App\Filament\Console\Resources\AgentResource\Pages\CreateAgent;
use App\Filament\Console\Resources\AgentResource\Pages\EditAgent;
use App\Filament\Console\Resources\AgentResource\Pages\ListAgents;
use App\Filament\Console\Resources\AgentResource\RelationManagers\DocumentsRelationManager;
use App\Models\Agent;
use App\Providers\PromptServiceProvider;
use App\Services\AI\GeminiService;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class AgentResource extends Resource
{
    protected static ?string $model = Agent::class;

    protected static ?string $navigationIcon = 'heroicon-c-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                MarkdownEditor::make('prompt')
                    ->required()
                    ->columnSpanFull()
                    ->helperText(new HtmlString('<code>%REQUEST%</code> will contain the user request<br><code>%CONTEXT%</code> will contain the context')),
                Section::make('Chat Options')
                    ->schema([
                        Fieldset::make()
                            ->columns(3)
                            ->schema([
                                Toggle::make('isPasswordProtected')
                                    ->live()
                                    ->columnSpan(1)
                                ,
                                TextInput::make(ChatOptions::PASSWORD_HASH->value)
                                    ->label('New Password')
                                    ->inlineLabel()
                                    ->password()
                                    ->revealable()
                                    ->hidden(fn(Forms\Get $get) => ! $get('isPasswordProtected'))
                                    ->columnSpan(2)
                                ,
                            ]),
                        TextInput::make(ChatOptions::SEARCH_SUBTITLE->value),
                        TextInput::make(ChatOptions::SEARCH_PLACEHOLDER->value),
                        Select::make(ChatOptions::PROMPT_MODEL->value)
                            ->label('GPT Model')
                            ->options(
                                PromptServiceProvider::getActivatedServicesAsOptions(),
                            )
                            ->default(GeminiService::MODEL_NAME)
                        ,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('chat')
                    ->url(fn(Agent $record): string => route('agent.prompt', $record))
                    ->openUrlInNewTab(),
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

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAgents::route('/'),
            'create' => CreateAgent::route('/create'),
            'edit' => EditAgent::route('/{record}/edit'),
        ];
    }
}
