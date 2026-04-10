<?php

namespace App\Filament\Resources;

use App\Enums\PartnerType;
use App\Filament\Resources\PartnerResource\Pages;
use App\Models\Partner;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|\UnitEnum|null $navigationGroup = 'Commercial';

    protected static ?string $modelLabel = 'Entreprise';

    protected static ?string $pluralModelLabel = 'Entreprises';

    protected static ?string $navigationLabel = 'Entreprises';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();
        if ($user && $user->hasRole('collaborator') && ! $user->hasRole('admin')) {
            if ($user->partner_id) {
                $query->whereKey($user->partner_id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informations')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Nom')->required(),
                        Select::make('type')->label('Type d\'entreprise')
                            ->options(collect(PartnerType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]))
                            ->default(PartnerType::Other->value)
                            ->required(),
                        TextInput::make('contact_name')->label('Nom du contact'),
                        TextInput::make('email')->label('Email')->email(),
                        TextInput::make('phone')->label('Téléphone')->tel(),
                        TextInput::make('website')->label('Site web')->url(),
                        Toggle::make('is_active')->label('Actif')->default(true),
                        FileUpload::make('logo_path')->label('Logo')->image()->directory('partners')->disk('public'),
                    ]),
                Section::make()
                    ->schema([RichEditor::make('description')->label('Description')])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')->label('')->disk('public')->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?background=7c3aed&color=fff&name=P'),
                Tables\Columns\TextColumn::make('name')->label('Entreprise')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Type')->badge()->formatStateUsing(fn (PartnerType $state) => $state->label()),
                Tables\Columns\TextColumn::make('contact_name')->label('Contact')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\IconColumn::make('is_active')->label('Actif')->boolean(),
                Tables\Columns\TextColumn::make('offers_count')->label('Offres')->counts('offers'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('Type')
                    ->options(collect(PartnerType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()])),
                Tables\Filters\TernaryFilter::make('is_active')->label('Actif'),
            ])
            ->actions([Actions\EditAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
