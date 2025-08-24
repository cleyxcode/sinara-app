<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacilityIvaResource\Pages;
use App\Models\FacilityIva;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\TagsColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FacilityIvaResource extends Resource
{
    protected static ?string $model = FacilityIva::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Fasilitas IVA';
    protected static ?string $modelLabel = 'Fasilitas IVA';
    protected static ?string $pluralModelLabel = 'Fasilitas IVA';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Dasar')
                    ->description('Data dasar fasilitas kesehatan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('code')
                                    ->label('Kode Fasilitas')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(20)
                                    ->helperText('Kode unik fasilitas kesehatan'),

                                TextInput::make('name')
                                    ->label('Nama Fasilitas')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Nama puskesmas atau fasilitas kesehatan'),
                            ]),

                        Select::make('location')
                            ->label('Lokasi (Kabupaten/Kota)')
                            ->options([
                                'Kab. Kepulauan Tanimbar' => 'Kab. Kepulauan Tanimbar',
                                'Kab. Maluku Tenggara' => 'Kab. Maluku Tenggara',
                                'Kab. Maluku Tengah' => 'Kab. Maluku Tengah',
                                'Kab. Buru' => 'Kab. Buru',
                                'Kab. Buru Selatan' => 'Kab. Buru Selatan',
                                'Kab. Maluku Barat Daya' => 'Kab. Maluku Barat Daya',
                                'Kab. Seram Bagian Barat' => 'Kab. Seram Bagian Barat',
                                'Kab. Seram Bagian Timur' => 'Kab. Seram Bagian Timur',
                                'Kab. Kepulauan Aru' => 'Kab. Kepulauan Aru',
                                'Kota Ambon' => 'Kota Ambon',
                                'Kota Tual' => 'Kota Tual',
                            ])
                            ->required()
                            ->searchable(),

                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Alamat lengkap fasilitas kesehatan (opsional)'),
                    ]),

                Section::make('Koordinat dan Kontak')
                    ->description('Informasi koordinat dan kontak fasilitas')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->step(0.00000001)
                                    ->helperText('Koordinat latitude (-90 s/d 90)'),

                                TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->step(0.00000001)
                                    ->helperText('Koordinat longitude (-180 s/d 180)'),

                                TextInput::make('phone')
                                    ->label('No. Telepon')
                                    ->tel()
                                    ->maxLength(20)
                                    ->helperText('Nomor telepon fasilitas'),
                            ]),
                    ]),

                Section::make('Pelatihan dan Status')
                    ->description('Informasi pelatihan IVA dan status fasilitas')
                    ->schema([
                        TagsInput::make('iva_training_years')
                            ->label('Tahun Pelatihan IVA')
                            ->placeholder('Masukkan tahun pelatihan (contoh: 2019, 2022)')
                            ->helperText('Tahun-tahun ketika fasilitas mengikuti pelatihan IVA'),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->helperText('Fasilitas aktif akan ditampilkan di aplikasi'),

                                Textarea::make('notes')
                                    ->label('Catatan Tambahan')
                                    ->rows(2)
                                    ->helperText('Informasi tambahan tentang fasilitas'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode disalin!')
                    ->width(100),

                TextColumn::make('name')
                    ->label('Nama Fasilitas')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                BadgeColumn::make('location')
                    ->label('Lokasi')
                    ->colors([
                        'primary' => 'Kab. Kepulauan Tanimbar',
                        'success' => 'Kab. Maluku Tenggara',
                        'warning' => 'Kab. Maluku Tengah',
                        'danger' => 'Kab. Buru',
                        'secondary' => 'Kab. Buru Selatan',
                        'info' => 'Kab. Maluku Barat Daya',
                        'gray' => fn ($state): bool => !in_array($state, [
                            'Kab. Kepulauan Tanimbar',
                            'Kab. Maluku Tenggara',
                            'Kab. Maluku Tengah',
                            'Kab. Buru',
                            'Kab. Buru Selatan',
                            'Kab. Maluku Barat Daya'
                        ]),
                    ])
                    ->searchable()
                    ->sortable(),

                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(40)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (empty($state) || strlen($state) <= 40) {
                            return $state ?: 'Alamat belum diisi';
                        }
                        return $state;
                    })
                    ->placeholder('Belum diisi')
                    ->color(fn ($state): string => empty($state) ? 'warning' : 'gray'),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->placeholder('Belum diisi')
                    ->copyable()
                    ->copyMessage('Nomor telepon disalin!')
                    ->color(fn ($state): string => empty($state) ? 'warning' : 'gray'),

                TagsColumn::make('iva_training_years')
                    ->label('Tahun Pelatihan')
                    ->separator(',')
                    ->placeholder('Belum ada pelatihan')
                    ->getStateUsing(function ($record) {
                        return $record->iva_training_years ? array_map('strval', $record->iva_training_years) : [];
                    }),

                TextColumn::make('coordinates')
                    ->label('Koordinat')
                    ->getStateUsing(function ($record): string {
                        if ($record->latitude && $record->longitude) {
                            return "✓ Ada";
                        }
                        return "✗ Tidak ada";
                    })
                    ->badge()
                    ->color(fn ($state): string => $state === '✓ Ada' ? 'success' : 'warning'),

                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('location')
                    ->label('Lokasi')
                    ->options(FacilityIva::AVAILABLE_LOCATIONS)
                    ->searchable()
                    ->multiple(),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('has_coordinates')
                    ->label('Koordinat')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('latitude')->whereNotNull('longitude'),
                        false: fn ($query) => $query->whereNull('latitude')->orWhereNull('longitude'),
                    )
                    ->trueLabel('Ada Koordinat')
                    ->falseLabel('Tanpa Koordinat')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('has_training')
                    ->label('Pelatihan IVA')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('iva_training_years'),
                        false: fn ($query) => $query->whereNull('iva_training_years'),
                    )
                    ->trueLabel('Sudah Pelatihan')
                    ->falseLabel('Belum Pelatihan')
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
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => true])))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-m-x-circle')
                        ->color('warning')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => false])))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('location')
            ->striped();
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
            'index' => Pages\ListFacilityIvas::route('/'),
            'create' => Pages\CreateFacilityIva::route('/create'),
            'view' => Pages\ViewFacilityIva::route('/{record}'),
            'edit' => Pages\EditFacilityIva::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code', 'location', 'address'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Kode' => $record->code,
            'Lokasi' => $record->location,
            'Status' => $record->is_active ? 'Aktif' : 'Tidak Aktif',
        ];
    }
}