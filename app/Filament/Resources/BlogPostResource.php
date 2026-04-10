<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static string|\UnitEnum|null $navigationGroup = 'Vitrine';

    protected static ?string $navigationLabel = 'Blog';

    protected static ?string $modelLabel = 'Article';

    protected static ?string $pluralModelLabel = 'Articles de blog';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informations de l’article')
                    ->description('Titre, URL, extrait, image de couverture et paramètres de publication.')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(fn (mixed $livewire): bool => $livewire instanceof EditRecord)
                    ->schema([
                        TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, callable $get): void {
                                if (filled($get('slug'))) {
                                    return;
                                }
                                if (filled($state)) {
                                    $set('slug', Str::slug((string) $state));
                                }
                            })
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Identifiant dans l’URL, ex. mon-article')
                            ->columnSpanFull(),
                        Textarea::make('excerpt')
                            ->label('Extrait / chapô')
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('cover_image_path')
                            ->label('Image de couverture')
                            ->image()
                            ->disk('public')
                            ->directory('blog/covers')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->helperText('Affichée en grand sur l’accueil du blog (hero) et en tête d’article. Format paysage recommandé.')
                            ->columnSpanFull(),
                        DateTimePicker::make('published_at')
                            ->label('Date de publication')
                            ->seconds(false)
                            ->nullable()
                            ->helperText('Laisser vide pour un brouillon'),
                        TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1),
                        Toggle::make('is_visible')
                            ->label('Visible sur le site')
                            ->default(true)
                            ->required(),
                    ]),
                Section::make('Corps de l’article')
                    ->description(new HtmlString(
                        '<p class="text-sm text-gray-600 dark:text-gray-400 space-y-2">'
                        .'<strong>Images dans le texte (mode actuel)</strong> : dans la barre d’outils, utilisez <strong>Joindre des fichiers</strong> '
                        .'(icône trombone / « attachFiles »). Les fichiers sont enregistrés sous <code class="text-xs">storage/app/public/blog/content/</code> '
                        .'et référencés dans le JSON de l’article. Vous voyez l’<strong>image en direct dans l’éditeur</strong> (aperçu WYSIWYG), '
                        .'pas seulement un lien du type « image ici ».</p>'
                        .'<p class="text-sm text-gray-600 dark:text-gray-400 mt-2">'
                        .'<strong>Autres approches possibles</strong> (non activées ici, à discuter si besoin) : '
                        .'placeholders ou blocs « à illustrer plus tard » ; galerie séparée (plusieurs fichiers liés à l’article) affichée en dessous du texte ; '
                        .'import automatique depuis un dossier nommé comme le slug.</p>'
                    ))
                    ->schema([
                        RichEditor::make('content')
                            ->label('Texte et médias')
                            ->json()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('blog/content')
                            ->fileAttachmentsVisibility('public')
                            ->resizableImages()
                            ->columnSpanFull()
                            ->helperText('Rédigez ici tout le contenu affiché sur le site. Les images insérées sont stockées avec l’article (dossier blog/content).')
                            ->default([
                                'type' => 'doc',
                                'content' => [
                                    [
                                        'type' => 'paragraph',
                                        'content' => [],
                                    ],
                                ],
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image_path')
                    ->label('Couv.')
                    ->disk('public')
                    ->square()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publication')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
