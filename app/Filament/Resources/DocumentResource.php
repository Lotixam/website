<?php

namespace App\Filament\Resources;

use App\Enums\DocumentType;
use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Opérations';

    protected static ?string $modelLabel = 'Document';

    protected static ?string $pluralModelLabel = 'Documents';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Nom du document')->required(),
                        Select::make('type')->label('Type')
                            ->options(collect(DocumentType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]))
                            ->default('other')->required(),
                        Select::make('documentable_type')->label('Lié à')
                            ->options([
                                'App\Models\Operation' => 'Opération',
                                'App\Models\Lot' => 'Lot',
                                'App\Models\Contact' => 'Contact',
                            ])->required()->reactive(),
                        Select::make('documentable_id')->label('Élément')
                            ->options(function (Get $get) {
                                $type = $get('documentable_type');
                                if (! $type) return [];
                                return match ($type) {
                                    'App\Models\Operation' => \App\Models\Operation::pluck('name', 'id'),
                                    'App\Models\Lot' => \App\Models\Lot::with('operation')->get()->mapWithKeys(fn ($l) => [$l->id => "{$l->operation->name} - Lot {$l->lot_number}"]),
                                    'App\Models\Contact' => \App\Models\Contact::get()->mapWithKeys(fn ($c) => [$c->id => "{$c->first_name} {$c->last_name}"]),
                                    default => [],
                                };
                            })->searchable()->required(),
                        FileUpload::make('file_path')->label('Fichier')->directory('documents')->disk('public')->required()->columnSpanFull(),
                        Textarea::make('notes')->label('Notes')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Type')->badge()->formatStateUsing(fn (DocumentType $state) => $state->label()),
                Tables\Columns\TextColumn::make('documentable_type')->label('Lié à')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'App\Models\Operation' => 'Opération',
                        'App\Models\Lot' => 'Lot',
                        'App\Models\Contact' => 'Contact',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('Ajouté le')->dateTime('d/m/Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('Type')
                    ->options(collect(DocumentType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()])),
            ])
            ->actions([Actions\EditAction::make(), Actions\DeleteAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
