<?php

namespace App\Filament\Console\Imports;

use App\Models\Agent;
use App\Models\Document;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class DocumentCsvImporter extends Importer
{
    protected static ?string $model = Document::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('content')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('agent')
                ->requiredMapping()
                ->rules(['required'])
                ->relationship(resolveUsing: function (string $state): ?Agent {
                    return Agent::query()
                        ->where('name', $state)
                        ->where('id_user', auth()->id())
                        ->first();
                })
            ,
        ];
    }

    public function resolveRecord(): ?Document
    {
        $document = new Document();
        $document->id_user = auth()->id();
        return $document;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your document import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
