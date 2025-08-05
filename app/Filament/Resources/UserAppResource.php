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
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('alamat')
                    ->label('wilayah'),
                    
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('id', 'desc');
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
        return ['name', 'email', 'phone'];
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

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    
}