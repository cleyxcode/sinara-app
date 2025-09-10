<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IvaTestResultResource\Pages;
use App\Models\IvaTestResult;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Colors\Color;

class IvaTestResultResource extends Resource
{
    protected static ?string $model = IvaTestResult::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Hasil Tes IVA';

    protected static ?string $modelLabel = 'Hasil Tes IVA';

    protected static ?string $pluralModelLabel = 'Hasil Tes IVA';



    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pemeriksaan')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Pasien')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required(),
                                Forms\Components\TextInput::make('fasilitas_kesehatan')
                                    ->label('Fasilitas Kesehatan'),
                            ]),

                        Forms\Components\DatePicker::make('examination_date')
                            ->label('Tanggal Pemeriksaan')
                            ->required()
                            ->maxDate(now())
                            ->displayFormat('d/m/Y'),

                        Forms\Components\Select::make('examination_type')
                            ->label('Jenis Pemeriksaan')
                            ->options([
                                'IVA Test' => 'IVA Test',
                                'Pap Smear' => 'Pap Smear',
                                'HPV Test' => 'HPV Test',
                                'Kolposkopi' => 'Kolposkopi',
                                'Biopsi' => 'Biopsi',
                                'Pemeriksaan Lanjutan' => 'Pemeriksaan Lanjutan',
                            ])
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('result')
                            ->label('Hasil Pemeriksaan')
                            ->options([
                                'positif' => 'Positif',
                                'negatif' => 'Negatif',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('examined_by')
                            ->label('Diperiksa Oleh')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Catatan Tambahan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->maxLength(1000)
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.fasilitas_kesehatan')
                    ->label('Fasilitas Kesehatan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('examination_date')
                    ->label('Tanggal Pemeriksaan')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('examination_type')
                    ->label('Jenis Pemeriksaan')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('result')
                    ->label('Hasil')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'positif' => 'danger',
                        'negatif' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'positif' => 'Positif',
                        'negatif' => 'Negatif',
                    }),

                Tables\Columns\TextColumn::make('examined_by')
                    ->label('Pemeriksa')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Input')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diupdate')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('result')
                    ->label('Hasil Pemeriksaan')
                    ->options([
                        'positif' => 'Positif',
                        'negatif' => 'Negatif',
                    ]),

                Tables\Filters\SelectFilter::make('examination_type')
                    ->label('Jenis Pemeriksaan')
                    ->options([
                        'IVA Test' => 'IVA Test',
                        'Pap Smear' => 'Pap Smear',
                        'HPV Test' => 'HPV Test',
                        'Kolposkopi' => 'Kolposkopi',
                        'Biopsi' => 'Biopsi',
                        'Pemeriksaan Lanjutan' => 'Pemeriksaan Lanjutan',
                    ]),

                Tables\Filters\Filter::make('examination_date')
                    ->form([
                        Forms\Components\DatePicker::make('examination_from')
                            ->label('Tanggal Pemeriksaan Dari'),
                        Forms\Components\DatePicker::make('examination_until')
                            ->label('Tanggal Pemeriksaan Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['examination_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('examination_date', '>=', $date),
                            )
                            ->when(
                                $data['examination_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('examination_date', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('user.fasilitas_kesehatan')
                    ->label('Fasilitas Kesehatan')
                    ->relationship('user', 'fasilitas_kesehatan')
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (IvaTestResult $record): bool =>
                        $record->created_at->diffInHours(now()) <= 24
                    ),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (IvaTestResult $record): bool =>
                        $record->created_at->diffInHours(now()) <= 1
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('examination_date', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pasien')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Nama Pasien'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('user.fasilitas_kesehatan')
                            ->label('Fasilitas Kesehatan'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Detail Pemeriksaan')
                    ->schema([
                        Infolists\Components\TextEntry::make('examination_date')
                            ->label('Tanggal Pemeriksaan')
                            ->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('examination_type')
                            ->label('Jenis Pemeriksaan')
                            ->badge()
                            ->color('gray'),
                        Infolists\Components\TextEntry::make('result')
                            ->label('Hasil Pemeriksaan')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'positif' => 'danger',
                                'negatif' => 'success',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'positif' => 'Positif',
                                'negatif' => 'Negatif',
                            }),
                        Infolists\Components\TextEntry::make('examined_by')
                            ->label('Diperiksa Oleh'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Catatan')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Catatan Pemeriksaan')
                            ->placeholder('Tidak ada catatan'),
                    ])
                    ->visible(fn (IvaTestResult $record): bool => !empty($record->notes)),

                Infolists\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Input')
                            ->dateTime('d/m/Y H:i:s'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->dateTime('d/m/Y H:i:s'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIvaTestResults::route('/'),
            'create' => Pages\CreateIvaTestResult::route('/create'),
            'view' => Pages\ViewIvaTestResult::route('/{record}'),
            'edit' => Pages\EditIvaTestResult::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $positiveCount = static::getModel()::where('result', 'positif')->count();
        $totalCount = static::getModel()::count();

        if ($totalCount === 0) {
            return 'gray';
        }

        $positivePercentage = ($positiveCount / $totalCount) * 100;

        if ($positivePercentage > 20) {
            return 'danger';
        } elseif ($positivePercentage > 10) {
            return 'warning';
        } else {
            return 'success';
        }
    }

}
