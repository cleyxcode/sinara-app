<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'Artikel';

    protected static ?string $pluralModelLabel = 'Artikel';

    protected static ?string $modelLabel = 'Artikel';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Judul Artikel')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->label('Gambar Artikel')
                    ->image()
                    ->directory('articles')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageCropAspectRatio('16:9')
                    ->imageResizeTargetWidth('800')
                    ->imageResizeTargetHeight('450')
                    ->columnSpanFull(),

                RichEditor::make('content')
                    ->label('Konten Artikel')
                    ->required()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'bulletList',
                        'orderedList',
                        'h2',
                        'h3',
                        'link',
                        'undo',
                        'redo',
                    ])
                    ->columnSpanFull(),

                Toggle::make('is_published')
                    ->label('Publikasikan')
                    ->default(true)
                    ->helperText('Artikel akan tampil di aplikasi mobile jika dipublikasikan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->width(80)
                    ->height(60)
                    ->defaultImageUrl(url('/images/placeholder.png')),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('excerpt')
                    ->label('Ringkasan')
                    ->limit(100)
                    ->wrap(),

                BooleanColumn::make('is_published')
                    ->label('Status')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Publikasi')
                    ->placeholder('Semua')
                    ->trueLabel('Dipublikasikan')
                    ->falseLabel('Draft'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
     public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'view' => Pages\ViewArticle::route('/{record}'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
