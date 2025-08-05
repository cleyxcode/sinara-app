<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            [
                'category' => 'Personal Hygiene',
                'order' => 1,
                'question_text' => 'Memiliki kebiasaan membersihkan area kewanitaan setelah hubungan seksual',
                'options' => [
                    ['text' => 'Ya', 'score' => 0],
                    ['text' => 'Tidak', 'score' => 1]
                ]
            ],
            [
                'category' => 'Personal Hygiene',
                'order' => 2,
                'question_text' => 'Kebiasaan mengganti pembalut saat menstruasi',
                'options' => [
                    ['text' => '≥4 kali/hari', 'score' => 0],
                    ['text' => '<4 kali/hari', 'score' => 1]
                ]
            ],
            [
                'category' => 'Personal Hygiene',
                'order' => 3,
                'question_text' => 'Memiliki kebiasaan membersihkan vagina dengan sabun atau cairan pembersih',
                'options' => [
                    ['text' => 'Tidak', 'score' => 0],
                    ['text' => 'Ya', 'score' => 1]
                ]
            ],
            [
                'category' => 'Aktivitas Seksual',
                'order' => 4,
                'question_text' => 'Memiliki jumlah pasangan seksual',
                'options' => [
                    ['text' => '1', 'score' => 0],
                    ['text' => '>1', 'score' => 1]
                ]
            ],
            [
                'category' => 'Aktivitas Seksual',
                'order' => 5,
                'question_text' => 'Usia saat melakukan hubungan seksual pertama kali',
                'options' => [
                    ['text' => '>16 tahun', 'score' => 0],
                    ['text' => '≤16 tahun', 'score' => 1]
                ]
            ],
            [
                'category' => 'Aktivitas Seksual',
                'order' => 6,
                'question_text' => 'Pernah mengalami perdarahan setelah berhubungan seksual',
                'options' => [
                    ['text' => 'Tidak', 'score' => 0],
                    ['text' => 'Ya', 'score' => 1]
                ]
            ],
            [
                'category' => 'Aktivitas Seksual',
                'order' => 7,
                'question_text' => 'Pernah melakukan hubungan seksual saat menstruasi',
                'options' => [
                    ['text' => 'Tidak', 'score' => 0],
                    ['text' => 'Ya', 'score' => 1]
                ]
            ],
            [
                'category' => 'Ekonomi',
                'order' => 8,
                'question_text' => 'Jumlah penghasilan suami per bulan',
                'options' => [
                    ['text' => '≥ UMR', 'score' => 0],
                    ['text' => '< UMR', 'score' => 1]
                ]
            ],
            [
                'category' => 'Ekonomi',
                'order' => 9,
                'question_text' => 'Jumlah penghasilan istri per bulan',
                'options' => [
                    ['text' => '≥ UMR', 'score' => 0],
                    ['text' => '< UMR', 'score' => 1]
                ]
            ],
            [
                'category' => 'Gaya Hidup',
                'order' => 10,
                'question_text' => 'Memiliki kebiasaan merokok',
                'options' => [
                    ['text' => 'Tidak', 'score' => 0],
                    ['text' => 'Ya', 'score' => 1]
                ]
            ],
            [
                'category' => 'Gaya Hidup',
                'order' => 11,
                'question_text' => 'Memiliki riwayat terpapar asap rokok di lingkungan tempat tinggal atau tempat kerja',
                'options' => [
                    ['text' => 'Tidak', 'score' => 0],
                    ['text' => 'Ya', 'score' => 1]
                ]
            ],
            [
                'category' => 'Gaya Hidup',
                'order' => 12,
                'question_text' => 'Suami memiliki kebiasaan merokok',
                'options' => [
                    ['text' => 'Tidak', 'score' => 0],
                    ['text' => 'Ya', 'score' => 1]
                ]
            ],
            [
                'category' => 'Riwayat Obstetri dan KB',
                'order' => 13,
                'question_text' => 'Jumlah persalinan',
                'options' => [
                    ['text' => '≤2 kali', 'score' => 0],
                    ['text' => '>2 kali', 'score' => 1]
                ]
            ],
            [
                'category' => 'Riwayat Obstetri dan KB',
                'order' => 14,
                'question_text' => 'Pernah mengalami keluhan selama menggunakan KB',
                'options' => [
                    ['text' => 'Tidak', 'score' => 0],
                    ['text' => 'Ya', 'score' => 1]
                ]
            ],
            [
                'category' => 'Riwayat Penyakit',
                'order' => 15,
                'question_text' => 'Memiliki anggota keluarga yang pernah didiagnosa kanker',
                'options' => [
                    ['text' => 'Tidak', 'score' => 0],
                    ['text' => 'Ya', 'score' => 1]
                ]
            ],
            [
                'category' => 'Riwayat Penyakit',
                'order' => 16,
                'question_text' => 'Memiliki anggota keluarga perempuan yang pernah terkena kanker serviks',
                'options' => [
                    ['text' => 'Tidak', 'score' => 0],
                    ['text' => 'Ya', 'score' => 1]
                ]
            ],
            [
                'category' => 'Riwayat Penyakit',
                'order' => 17,
                'question_text' => 'Suami memiliki riwayat penyakit kelamin seperti gonorrhea, sifilis atau penyakit HIV',
                'options' => [
                    ['text' => 'Tidak', 'score' => 0],
                    ['text' => 'Ya', 'score' => 1]
                ]
            ],
            [
                'category' => 'Riwayat Penyakit',
                'order' => 18,
                'question_text' => 'Pernah mengalami keputihan berlebihan',
                'options' => [
                    ['text' => 'Tidak', 'score' => 0],
                    ['text' => 'Ya', 'score' => 1]
                ]
            ],
            [
                'category' => 'Riwayat Penyakit',
                'order' => 19,
                'question_text' => 'Pernah mengalami keputihan berbau dan gatal',
                'options' => [
                    ['text' => 'Tidak', 'score' => 0],
                    ['text' => 'Ya', 'score' => 1]
                ]
            ],
            [
                'category' => 'Riwayat Penyakit',
                'order' => 20,
                'question_text' => 'Pernah mengalami perdarahan di luar periode menstruasi',
                'options' => [
                    ['text' => 'Tidak', 'score' => 0],
                    ['text' => 'Ya', 'score' => 1]
                ]
            ],
            [
                'category' => 'Riwayat Skrining',
                'order' => 21,
                'question_text' => 'Pernah mengikuti pemeriksaan IVA atau pap smear',
                'options' => [
                    ['text' => 'Ya', 'score' => 0],
                    ['text' => 'Tidak', 'score' => 1]
                ]
            ]
        ];

        foreach ($questions as $question) {
            Question::create($question);
        }
    }
}