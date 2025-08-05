<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResponseResource\Pages;
use App\Models\UserResponse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Support\Enums\FontWeight;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;
use Maatwebsite\Excel\Excel;

class UserResponseResource extends Resource
{
    protected static ?string $model = UserResponse::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Hasil Skrining';
    protected static ?string $modelLabel = 'Hasil Skrining User';
    protected static ?string $pluralModelLabel = 'Hasil Skrining Users';
    protected static ?int $navigationSort = 2;
    // protected static ?string $navigationGroup = 'Manajemen Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('info')
                    ->content('Data hasil skrining hanya dapat dilihat, tidak dapat diubah.')
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->size(TextColumn\TextColumnSize::ExtraSmall),

                TextColumn::make('user.name')
                    ->label('Nama User')
                    ->sortable()
                    ->searchable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn (UserResponse $record): string => $record->user->email ?? ''),

                TextColumn::make('user.phone')
                    ->label('No. Telepon')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor telepon disalin!')
                    ->copyMessageDuration(1500),

                TextColumn::make('user.alamat')
                    ->label('Alamat')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    })
                    ->wrap(),

                TextColumn::make('user.umur')
                    ->label('Umur')
                    ->suffix(' tahun')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('total_score')
                    ->label('Skor Total')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state <= 7 => 'success',
                        $state <= 14 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('risk_level')
                    ->label('Tingkat Risiko')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Rendah' => 'success',
                        'Sedang' => 'warning',
                        'Tinggi' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('responses_count')
                    ->label('Jumlah Jawaban')
                    ->getStateUsing(fn ($record) => count($record->responses ?? []))
                    ->badge()
                    ->color('info'),

                TextColumn::make('completed_at')
                    ->label('Waktu Selesai')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn (UserResponse $record): string => 
                        $record->completed_at->diffForHumans()
                    ),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('risk_level')
                    ->label('Tingkat Risiko')
                    ->options([
                        'Rendah' => 'Rendah',
                        'Sedang' => 'Sedang',
                        'Tinggi' => 'Tinggi',
                    ])
                    ->placeholder('Semua Tingkat Risiko'),

                Filter::make('score_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('score_from')
                                    ->label('Skor Dari')
                                    ->numeric()
                                    ->placeholder('0')
                                    ->minValue(0)
                                    ->maxValue(21),
                                Forms\Components\TextInput::make('score_to')
                                    ->label('Skor Sampai')
                                    ->numeric()
                                    ->placeholder('21')
                                    ->minValue(0)
                                    ->maxValue(21),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['score_from'] !== null,
                                fn (Builder $query, $score): Builder => $query->where('total_score', '>=', $data['score_from']),
                            )
                            ->when(
                                $data['score_to'] !== null,
                                fn (Builder $query, $score): Builder => $query->where('total_score', '<=', $data['score_to']),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['score_from'] ?? null) {
                            $indicators[] = 'Skor dari: ' . $data['score_from'];
                        }
                        if ($data['score_to'] ?? null) {
                            $indicators[] = 'Skor sampai: ' . $data['score_to'];
                        }
                        return $indicators;
                    }),

                Filter::make('completed_date')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                DatePicker::make('completed_from')
                                    ->label('Dari Tanggal')
                                    ->placeholder('Pilih tanggal mulai'),
                                DatePicker::make('completed_until')
                                    ->label('Sampai Tanggal')
                                    ->placeholder('Pilih tanggal akhir'),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['completed_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('completed_at', '>=', $date),
                            )
                            ->when(
                                $data['completed_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('completed_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['completed_from'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['completed_from'])->format('d/m/Y');
                        }
                        if ($data['completed_until'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['completed_until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail')
                    ->icon('heroicon-m-eye'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exports([
                            // Export Summary - Ringkasan data
                            ExcelExport::make('summary')
                                ->withFilename(fn () => 'ringkasan-skrining-' . now()->format('Y-m-d-H-i'))
                                ->withColumns([
                                    Column::make('id')->heading('ID'),
                                    Column::make('user.name')->heading('Nama User'),
                                    Column::make('user.email')->heading('Email'),
                                    Column::make('user.phone')->heading('No. Telepon'),
                                    Column::make('user.alamat')->heading('Alamat'),
                                    Column::make('user.umur')
                                        ->heading('Umur')
                                        ->formatStateUsing(fn ($state) => $state . ' tahun'),
                                    Column::make('total_score')->heading('Skor Total'),
                                    Column::make('risk_level')->heading('Tingkat Risiko'),
                                    Column::make('completed_at')
                                        ->heading('Waktu Selesai')
                                        ->formatStateUsing(fn ($state) => $state->format('d/m/Y H:i:s')),
                                    Column::make('session_id')->heading('Session ID'),
                                ]),

                            
                            ExcelExport::make('detailed')
                                ->withFilename(fn () => 'detail-skrining-' . now()->format('Y-m-d-H-i'))
                                ->withColumns([
                                    Column::make('id')->heading('ID'),
                                    Column::make('user.name')->heading('Nama User'),
                                    Column::make('user.email')->heading('Email'),
                                    Column::make('user.phone')->heading('No. Telepon'),
                                    Column::make('user.alamat')->heading('Alamat'),
                                    Column::make('user.umur')
                                        ->heading('Umur')
                                        ->formatStateUsing(fn ($state) => $state . ' tahun'),
                                    Column::make('total_score')->heading('Skor Total'),
                                    Column::make('risk_level')->heading('Tingkat Risiko'),
                                    Column::make('completed_at')
                                        ->heading('Waktu Selesai')
                                        ->formatStateUsing(fn ($state) => $state->format('d/m/Y H:i:s')),
                                    Column::make('responses')
                                        ->heading('Detail Jawaban')
                                        ->formatStateUsing(function ($state) {
                                            if (empty($state)) return '-';
                                            $details = [];
                                            foreach ($state as $index => $response) {
                                                $details[] = sprintf(
                                                    "%d. %s | Jawaban: %s | Skor: %s",
                                                    $index + 1,
                                                    $response['question_text'] ?? '',
                                                    $response['selected_option_text'] ?? '',
                                                    $response['score'] ?? 0
                                                );
                                            }
                                            return implode("\n", $details);
                                        }),
                                    Column::make('recommendations')->heading('Rekomendasi'),
                                ]),

                            // Export untuk Analisis - Format CSV
                            ExcelExport::make('analysis')
                                ->withFilename(fn () => 'analisis-skrining-' . now()->format('Y-m-d-H-i'))
                                ->withWriterType(Excel::CSV)
                                ->withColumns([
                                    Column::make('id')->heading('ID'),
                                    Column::make('user.umur')->heading('Umur'),
                                    Column::make('total_score')->heading('Skor_Total'),
                                    Column::make('risk_level')->heading('Tingkat_Risiko'),
                                    Column::make('completed_at')
                                        ->heading('Tanggal_Selesai')
                                        ->formatStateUsing(fn ($state) => $state->format('Y-m-d')),
                                    // Tambahkan kolom skor per kategori jika ada
                                    Column::make('responses')
                                        ->heading('Skor_Per_Kategori')
                                        ->formatStateUsing(function ($state) {
                                            if (empty($state)) return '';
                                            $categories = [];
                                            foreach ($state as $response) {
                                                $category = $response['category'] ?? 'Unknown';
                                                if (!isset($categories[$category])) {
                                                    $categories[$category] = 0;
                                                }
                                                $categories[$category] += $response['score'] ?? 0;
                                            }
                                            return json_encode($categories);
                                        }),
                                ]),
                        ])
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exports([
                        // Export Laporan Ringkasan
                        ExcelExport::make('summary_report')
                            ->withFilename(fn () => 'laporan-ringkasan-skrining-' . now()->format('Y-m-d-H-i'))
                            ->withColumns([
                                Column::make('user.name')->heading('Nama User'),
                                Column::make('user.email')->heading('Email'),
                                Column::make('user.phone')->heading('No. Telepon'),
                                Column::make('user.umur')
                                    ->heading('Umur')
                                    ->formatStateUsing(fn ($state) => $state . ' tahun'),
                                Column::make('total_score')->heading('Skor Total'),
                                Column::make('risk_level')->heading('Tingkat Risiko'),
                                Column::make('completed_at')
                                    ->heading('Tanggal Selesai')
                                    ->formatStateUsing(fn ($state) => $state->format('d/m/Y H:i')),
                            ]),

                       
                        ExcelExport::make('detailed_report_word')
                            ->withFilename(fn () => 'laporan-detail-skrining-' . now()->format('Y-m-d-H-i'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            ->withColumns([
                                Column::make('user.name')->heading('Nama User'),
                                Column::make('user.email')->heading('Email'),
                                Column::make('user.phone')->heading('No. Telepon'),
                                Column::make('user.alamat')->heading('Alamat'),
                                Column::make('user.umur')
                                    ->heading('Umur')
                                    ->formatStateUsing(fn ($state) => $state . ' tahun'),
                                Column::make('total_score')->heading('Skor Total'),
                                Column::make('risk_level')->heading('Tingkat Risiko'),
                                Column::make('completed_at')
                                    ->heading('Waktu Selesai')
                                    ->formatStateUsing(fn ($state) => $state->format('d/m/Y H:i:s')),
                                Column::make('responses')
                                    ->heading('Ringkasan Jawaban')
                                    ->formatStateUsing(function ($state) {
                                        if (empty($state)) return 'Tidak ada jawaban';
                                        $summary = [];
                                        $categories = [];
                                        
                                        foreach ($state as $response) {
                                            $category = $response['category'] ?? 'Lainnya';
                                            if (!isset($categories[$category])) {
                                                $categories[$category] = ['count' => 0, 'score' => 0];
                                            }
                                            $categories[$category]['count']++;
                                            $categories[$category]['score'] += $response['score'] ?? 0;
                                        }
                                        
                                        foreach ($categories as $category => $data) {
                                            $summary[] = "{$category}: {$data['count']} pertanyaan, skor {$data['score']}";
                                        }
                                        
                                        return implode('; ', $summary);
                                    }),
                                Column::make('recommendations')->heading('Rekomendasi'),
                            ]),

                  
                        ExcelExport::make('statistics')
                            ->withFilename(fn () => 'statistik-skrining-' . now()->format('Y-m-d-H-i'))
                            ->modifyQueryUsing(function ($query) {
                                // Tambahkan statistik dasar
                                return $query->selectRaw('
                                    COUNT(*) as total_responses,
                                    AVG(total_score) as avg_score,
                                    MIN(total_score) as min_score,
                                    MAX(total_score) as max_score,
                                    SUM(CASE WHEN risk_level = "Rendah" THEN 1 ELSE 0 END) as low_risk,
                                    SUM(CASE WHEN risk_level = "Sedang" THEN 1 ELSE 0 END) as medium_risk,
                                    SUM(CASE WHEN risk_level = "Tinggi" THEN 1 ELSE 0 END) as high_risk,
                                    DATE(completed_at) as completion_date
                                ')
                                ->groupBy('completion_date')
                                ->orderBy('completion_date', 'desc');
                            })
                            ->withColumns([
                                Column::make('completion_date')->heading('Tanggal'),
                                Column::make('total_responses')->heading('Total Responden'),
                                Column::make('avg_score')
                                    ->heading('Rata-rata Skor')
                                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                                Column::make('min_score')->heading('Skor Terendah'),
                                Column::make('max_score')->heading('Skor Tertinggi'),
                                Column::make('low_risk')->heading('Risiko Rendah'),
                                Column::make('medium_risk')->heading('Risiko Sedang'),
                                Column::make('high_risk')->heading('Risiko Tinggi'),
                            ]),
                    ])
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down'),
            ])
            ->defaultSort('completed_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi User')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Nama Lengkap')
                            ->weight(FontWeight::SemiBold),
                        TextEntry::make('user.email')
                            ->label('Email')
                            ->copyable()
                            ->copyMessage('Email disalin!')
                            ->copyMessageDuration(1500),
                        TextEntry::make('user.phone')
                            ->label('No. Telepon')
                            ->copyable()
                            ->copyMessage('Nomor telepon disalin!')
                            ->copyMessageDuration(1500),
                        TextEntry::make('user.alamat')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        TextEntry::make('user.umur')
                            ->label('Umur')
                            ->suffix(' tahun'),
                        TextEntry::make('session_id')
                            ->label('Session ID')
                            ->placeholder('Tidak ada session ID'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Hasil Skrining')
                    ->schema([
                        TextEntry::make('total_score')
                            ->label('Skor Total')
                            ->badge()
                            ->color(fn (string $state): string => match (true) {
                                $state <= 7 => 'success',
                                $state <= 14 => 'warning',
                                default => 'danger',
                            })
                            ->size(TextEntry\TextEntrySize::Large),
                        TextEntry::make('risk_level')
                            ->label('Tingkat Risiko')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Rendah' => 'success',
                                'Sedang' => 'warning',
                                'Tinggi' => 'danger',
                                default => 'gray',
                            })
                            ->size(TextEntry\TextEntrySize::Large),
                        TextEntry::make('completed_at')
                            ->label('Waktu Selesai')
                            ->dateTime('d/m/Y H:i:s')
                            ->since(),
                        TextEntry::make('recommendations')
                            ->label('Rekomendasi')
                            ->columnSpanFull()
                            ->prose()
                            ->markdown(),
                    ])
                    ->columns(3),

                Section::make('Detail Jawaban')
                    ->schema([
                        RepeatableEntry::make('responses')
                            ->label('')
                            ->schema([
                                TextEntry::make('category')
                                    ->label('Kategori')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('question_text')
                                    ->label('Pertanyaan')
                                    ->columnSpanFull()
                                    ->weight(FontWeight::Medium),
                                TextEntry::make('selected_option_text')
                                    ->label('Jawaban Dipilih')
                                    ->columnSpan(2),
                                TextEntry::make('score')
                                    ->label('Skor')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        '0' => 'success',
                                        '1' => 'danger',
                                        default => 'gray',
                                    }),
                            ])
                            ->columns(4)
                            ->contained(false)
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserResponses::route('/'),
            'view' => Pages\ViewUserResponse::route('/{record}'),
        ];
    }

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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }
}