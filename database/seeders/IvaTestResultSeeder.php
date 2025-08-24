<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IvaTestResult;
use App\Models\UserApp;
use Carbon\Carbon;

class IvaTestResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada user di database
        $users = UserApp::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('Tidak ada user ditemukan. Silakan buat user terlebih dahulu.');
            return;
        }

        $examinationTypes = [
            'IVA Test',
            'Pap Smear',
            'HPV Test',
            'Kolposkopi',
            'Biopsi',
            'Pemeriksaan Lanjutan'
        ];

        $results = ['positif', 'negatif'];

        $doctorNames = [
            'Dr. Sarah Permata',
            'Dr. Ahmad Santoso',
            'Dr. Rina Sari',
            'Dr. Budi Prakoso',
            'Dr. Maya Indira',
            'Dr. Dedi Kurniawan',
            'Dr. Siti Nurhaliza',
            'Dr. Agus Salim'
        ];

        $sampleNotes = [
            // Notes untuk hasil negatif
            'Pemeriksaan berjalan normal, tidak ada kelainan yang ditemukan.',
            'Hasil pemeriksaan dalam batas normal.',
            'Tidak ditemukan sel abnormal.',
            'Kondisi serviks dalam keadaan sehat.',
            'Pemeriksaan rutin, hasil normal.',
            
            // Notes untuk hasil positif
            'Ditemukan sel abnormal, perlu pemeriksaan lanjutan.',
            'Terdeteksi perubahan sel, disarankan kontrol ulang.',
            'Hasil menunjukkan kelainan ringan, perlu monitoring.',
            'Ditemukan lesi, rujuk ke spesialis.',
            'Hasil positif, perlu biopsi lanjutan.',
        ];

        $this->command->info('Membuat data test IVA...');

        foreach ($users->take(10) as $user) { // Batasi hanya 10 user pertama
            // Setiap user akan memiliki 1-4 hasil test
            $testCount = rand(1, 4);
            
            for ($i = 0; $i < $testCount; $i++) {
                $examinationDate = Carbon::now()
                    ->subDays(rand(1, 365)) // Random tanggal dalam 1 tahun terakhir
                    ->subHours(rand(0, 23))
                    ->subMinutes(rand(0, 59));

                $result = $results[array_rand($results)];
                
                // 80% kemungkinan hasil negatif, 20% positif (lebih realistis)
                if (rand(1, 100) <= 80) {
                    $result = 'negatif';
                }

                $notes = $sampleNotes[array_rand($sampleNotes)];
                
                // Pilih notes yang sesuai dengan hasil
                if ($result === 'positif') {
                    $positiveNotes = array_slice($sampleNotes, 5); // Ambil notes untuk positif
                    $notes = $positiveNotes[array_rand($positiveNotes)];
                } else {
                    $negativeNotes = array_slice($sampleNotes, 0, 5); // Ambil notes untuk negatif
                    $notes = $negativeNotes[array_rand($negativeNotes)];
                }

                IvaTestResult::create([
                    'user_id' => $user->id,
                    'examination_date' => $examinationDate->toDateString(),
                    'examination_type' => $examinationTypes[array_rand($examinationTypes)],
                    'result' => $result,
                    'notes' => $notes,
                    'examined_by' => $doctorNames[array_rand($doctorNames)],
                    'created_at' => $examinationDate->addHours(rand(1, 24)), // Input beberapa jam setelah pemeriksaan
                    'updated_at' => $examinationDate->addHours(rand(1, 24)),
                ]);
            }
        }

        $total = IvaTestResult::count();
        $positive = IvaTestResult::where('result', 'positif')->count();
        $negative = IvaTestResult::where('result', 'negatif')->count();

        $this->command->info("✅ Berhasil membuat {$total} data test IVA:");
        $this->command->info("   - Hasil Positif: {$positive}");
        $this->command->info("   - Hasil Negatif: {$negative}");
        
        // Tampilkan beberapa contoh data yang dibuat
        $this->command->info("\n📋 Contoh data yang dibuat:");
        $sampleData = IvaTestResult::with('user')->latest()->take(3)->get();
        
        foreach ($sampleData as $test) {
            $this->command->line("   • {$test->user->name} - {$test->examination_type} - {$test->result} ({$test->examination_date->format('d/m/Y')})");
        }
    }
}