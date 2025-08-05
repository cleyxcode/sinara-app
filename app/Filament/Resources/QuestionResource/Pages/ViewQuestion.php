<?php 

// File: app/Filament/Resources/QuestionResource/Pages/ViewQuestion.php
namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;

class ViewQuestion extends ViewRecord
{
    protected static string $resource = QuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Pertanyaan')
                    ->schema([
                        TextEntry::make('order')
                            ->label('Urutan Pertanyaan'),
                        TextEntry::make('category')
                            ->label('Kategori')
                            ->badge(),
                        TextEntry::make('question_text')
                            ->label('Teks Pertanyaan')
                            ->columnSpanFull(),
                        TextEntry::make('is_active')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                '1' => 'success',
                                '0' => 'danger',
                            })
                            ->formatStateUsing(fn (string $state): string => $state ? 'Aktif' : 'Tidak Aktif'),
                    ])->columns(2),

                Section::make('Opsi Jawaban')
                    ->schema([
                        RepeatableEntry::make('options')
                            ->schema([
                                TextEntry::make('text')
                                    ->label('Jawaban'),
                                TextEntry::make('score')
                                    ->label('Skor')
                                    ->badge()
                                    ->color(fn ($state) => $state == 0 ? 'success' : 'danger')
                            ])
                            ->columns(2)
                    ])
            ]);
    }
}