<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserAppResource\Pages;
use App\Models\UserApp;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class UserAppResource extends Resource
{
    protected static ?string $model = UserApp::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pengguna Terdaftar';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna Terdaftar';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email disalin!')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor telepon disalin!')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('alamat')
                    ->label('Wilayah')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('fasilitas_kesehatan')
                    ->label('Fasilitas Kesehatan')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->limit(25)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 25) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('umur')
                    ->label('Umur')
                    ->suffix(' thn')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                SelectFilter::make('fasilitas_kesehatan')
                    ->label('Fasilitas Kesehatan')
                    ->options(function () {
                        return UserApp::distinct()
                            ->pluck('fasilitas_kesehatan', 'fasilitas_kesehatan')
                            ->filter()
                            ->toArray();
                    })
                    ->placeholder('Semua Fasilitas Kesehatan')
                    ->searchable(),

                SelectFilter::make('email_verified')
                    ->label('Status Email')
                    ->options([
                        '1' => 'Sudah Diverifikasi',
                        '0' => 'Belum Diverifikasi',
                    ])
                    ->placeholder('Semua Status')
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value'] === '1') {
                            return $query->whereNotNull('email_verified_at');
                        } elseif ($data['value'] === '0') {
                            return $query->whereNull('email_verified_at');
                        }
                        return $query;
                    }),

                Filter::make('age_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('age_from')
                                    ->label('Umur Dari')
                                    ->numeric()
                                    ->placeholder('0')
                                    ->minValue(0)
                                    ->maxValue(150),
                                Forms\Components\TextInput::make('age_to')
                                    ->label('Umur Sampai')
                                    ->numeric()
                                    ->placeholder('150')
                                    ->minValue(0)
                                    ->maxValue(150),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['age_from'] !== null,
                                fn (Builder $query, $age): Builder => $query->where('umur', '>=', $data['age_from']),
                            )
                            ->when(
                                $data['age_to'] !== null,
                                fn (Builder $query, $age): Builder => $query->where('umur', '<=', $data['age_to']),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['age_from'] ?? null) {
                            $indicators[] = 'Umur dari: ' . $data['age_from'];
                        }
                        if ($data['age_to'] ?? null) {
                            $indicators[] = 'Umur sampai: ' . $data['age_to'];
                        }
                        return $indicators;
                    }),

                Filter::make('registration_date')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                DatePicker::make('registered_from')
                                    ->label('Terdaftar Dari')
                                    ->placeholder('Pilih tanggal mulai'),
                                DatePicker::make('registered_until')
                                    ->label('Terdaftar Sampai')
                                    ->placeholder('Pilih tanggal akhir'),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['registered_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['registered_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['registered_from'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['registered_from'])->format('d/m/Y');
                        }
                        if ($data['registered_until'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['registered_until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail')
                    ->icon('heroicon-m-eye'),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Data Pengguna')
                    ->modalDescription('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini akan juga menghapus semua data skrining yang terkait. Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Pengguna berhasil dihapus'),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->icon('heroicon-m-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Pengguna Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua pengguna yang dipilih? Tindakan ini akan juga menghapus semua data skrining yang terkait dengan pengguna tersebut. Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua')
                        ->modalCancelActionLabel('Batal')
                        ->successNotificationTitle('Pengguna terpilih berhasil dihapus')
                        ->deselectRecordsAfterCompletion(),


                    Tables\Actions\BulkAction::make('export_selected')
                        ->label('Export Terpilih')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function ($records) {
                            // Export logic bisa ditambahkan di sini
                            // Misalnya menggunakan Excel export
                            return response()->streamDownload(function () use ($records) {
                                echo "ID,Nama,Email,Telepon,Alamat,Fasilitas Kesehatan,Umur,Status Email,Tanggal Registrasi\n";
                                foreach ($records as $record) {
                                    echo sprintf(
                                        "%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                                        $record->id,
                                        '"' . str_replace('"', '""', $record->name) . '"',
                                        $record->email,
                                        $record->phone ?? '',
                                        '"' . str_replace('"', '""', $record->alamat) . '"',
                                        '"' . str_replace('"', '""', $record->fasilitas_kesehatan) . '"',
                                        $record->umur,
                                        $record->email_verified_at ? 'Verified' : 'Not Verified',
                                        $record->created_at->format('Y-m-d H:i:s')
                                    );
                                }
                            }, 'pengguna-terpilih-' . now()->format('Y-m-d-H-i') . '.csv');
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Export Pengguna Terpilih')
                        ->modalDescription('Apakah Anda ingin mengexport data pengguna yang dipilih ke dalam format CSV?')
                        ->modalSubmitActionLabel('Ya, Export')
                        ->modalCancelActionLabel('Batal'),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_all')
                    ->label('Export Semua')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        $users = UserApp::all();
                        return response()->streamDownload(function () use ($users) {
                            echo "ID,Nama,Email,Telepon,Alamat,Fasilitas Kesehatan,Umur,Status Email,Tanggal Registrasi\n";
                            foreach ($users as $user) {
                                echo sprintf(
                                    "%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                                    $user->id,
                                    '"' . str_replace('"', '""', $user->name) . '"',
                                    $user->email,
                                    $user->phone ?? '',
                                    '"' . str_replace('"', '""', $user->alamat) . '"',
                                    '"' . str_replace('"', '""', $user->fasilitas_kesehatan) . '"',
                                    $user->umur,
                                    $user->email_verified_at ? 'Verified' : 'Not Verified',
                                    $user->created_at->format('Y-m-d H:i:s')
                                );
                            }
                        }, 'semua-pengguna-' . now()->format('Y-m-d-H-i') . '.csv');
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Export Semua Pengguna')
                    ->modalDescription('Apakah Anda ingin mengexport semua data pengguna ke dalam format CSV?')
                    ->modalSubmitActionLabel('Ya, Export')
                    ->modalCancelActionLabel('Batal'),
            ])
            ->defaultSort('id', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pengguna')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Nama Lengkap')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('email')
                                    ->label('Alamat Email')
                                    ->copyable()
                                    ->copyMessage('Email disalin!')
                                    ->icon('heroicon-m-envelope'),

                                Infolists\Components\TextEntry::make('phone')
                                    ->label('Nomor Telepon')
                                    ->copyable()
                                    ->copyMessage('Nomor telepon disalin!')
                                    ->icon('heroicon-m-phone'),

                                Infolists\Components\TextEntry::make('umur')
                                    ->label('Umur')
                                    ->suffix(' tahun')
                                    ->badge()
                                    ->color('info'),
                            ]),

                        Infolists\Components\TextEntry::make('alamat')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull()
                            ->icon('heroicon-m-map-pin'),

                        Infolists\Components\TextEntry::make('fasilitas_kesehatan')
                            ->label('Fasilitas Kesehatan')
                            ->columnSpanFull()
                            ->badge()
                            ->size('lg')
                            ->color('success')
                            ->icon('heroicon-m-building-office-2'),
                    ]),

                Infolists\Components\Section::make('Status & Informasi Akun')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\IconEntry::make('email_verified_at')
                                    ->label('Status Email')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-badge')
                                    ->falseIcon('heroicon-o-x-mark')
                                    ->trueColor('success')
                                    ->falseColor('danger'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Tanggal Registrasi')
                                    ->dateTime('d F Y, H:i:s')
                                    ->badge()
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('email_verified_at')
                                    ->label('Email Diverifikasi')
                                    ->dateTime('d F Y, H:i:s')
                                    ->placeholder('Belum diverifikasi')
                                    ->badge()
                                    ->color('warning'),
                            ]),
                    ]),

                // Tambahan section untuk menampilkan data skrining jika ada
                Infolists\Components\Section::make('Riwayat Skrining')
                    ->schema([
                        Infolists\Components\TextEntry::make('responses_count')
                            ->label('Jumlah Skrining')
                            ->getStateUsing(fn (UserApp $record): int => $record->responses()->count())
                            ->badge()
                            ->color('info'),

                        Infolists\Components\TextEntry::make('last_screening')
                            ->label('Skrining Terakhir')
                            ->getStateUsing(function (UserApp $record): string {
                                $lastResponse = $record->responses()->latest()->first();
                                return $lastResponse ? $lastResponse->completed_at->format('d/m/Y H:i') : 'Belum pernah skrining';
                            })
                            ->badge()
                            ->color('warning'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserApps::route('/'),
            'view' => Pages\ViewUserApp::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'phone', 'fasilitas_kesehatan'];
    }

    // Disable create dan edit karena data dari API
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    // Mengaktifkan fungsi delete
    public static function canDelete($record): bool
    {
        return true;
    }

    public static function canDeleteAny(): bool
    {
        return true;
    }

}
