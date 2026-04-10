<?php

namespace App\Filament\Resources\ContactSubmissionResource\RelationManagers;

use App\Models\ContactSubmissionAttachment;
use Filament\Actions;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Pièces jointes';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('original_name')->label('Fichier')->searchable(),
                Tables\Columns\TextColumn::make('mime_type')->label('Type')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('size_bytes')
                    ->label('Taille')
                    ->formatStateUsing(fn (?int $state): string => $state === null ? '—' : self::formatBytes($state)),
            ])
            ->defaultSort('id')
            ->actions([
                Actions\Action::make('télécharger')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (ContactSubmissionAttachment $record) {
                        abort_unless(auth()->user()?->can('view', $record->contactSubmission), 403);

                        return Storage::disk($record->disk)->download($record->path, $record->original_name);
                    }),
            ])
            ->bulkActions([]);
    }

    private static function formatBytes(int $bytes): string
    {
        $units = ['o', 'Ko', 'Mo', 'Go'];
        $i = 0;
        $v = (float) $bytes;
        while ($v >= 1024 && $i < count($units) - 1) {
            $v /= 1024;
            $i++;
        }

        return ($i === 0 ? (string) (int) $v : number_format($v, $i > 1 ? 1 : 0, ',', ' ')).' '.$units[$i];
    }
}
