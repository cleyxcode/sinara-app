<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ToggleColumn;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationLabel = 'Kelola Pertanyaan';
    protected static ?string $modelLabel = 'Pertanyaan Skrining';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('category')
                    ->label('Kategori')
                    ->options(Question::CATEGORIES)
                    ->required()
                    ->searchable(),

                TextInput::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(50)
                    ->helperText('Urutan pertanyaan 1-21'),

                Textarea::make('question_text')
                    ->label('Teks Pertanyaan')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                Repeater::make('options')
                    ->label('Opsi Jawaban')
                    ->schema([
                        TextInput::make('text')
                            ->label('Teks Jawaban')
                            ->required(),
                        TextInput::make('score')
                            ->label('Skor')
                            ->numeric()
                            ->required()
                            ->helperText('0 = tidak berisiko, 1 = berisiko')
                    ])
                    ->columns(2)
                    ->minItems(2)
                    ->maxItems(3)
                    ->columnSpanFull()
                    ->defaultItems(2),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->helperText('Pertanyaan aktif akan ditampilkan di aplikasi')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('No.')
                    ->sortable()
                    ->width(60),

                BadgeColumn::make('category')
                    ->label('Kategori')
                    ->colors([
                        'primary' => 'Personal Hygiene',
                        'success' => 'Aktivitas Seksual',
                        'warning' => 'Ekonomi',
                        'danger' => 'Gaya Hidup',
                        'secondary' => 'Riwayat Obstetri dan KB',
                        'info' => 'Riwayat Penyakit',
                        'gray' => 'Riwayat Skrining'
                    ]),

                TextColumn::make('question_text')
                    ->label('Pertanyaan')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('options_count')
                    ->label('Opsi')
                    ->getStateUsing(fn ($record) => count($record->options ?? []))
                    ->badge()
                    ->color('success'),

                ToggleColumn::make('is_active')
                    ->label('Status')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(Question::CATEGORIES),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->native(false),
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
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'view' => Pages\ViewQuestion::route('/{record}'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}