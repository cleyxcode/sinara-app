<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Aktivitas Seksual Lebih Berisiko Menularkan Kanker Serviks',
                'content' => 'Hai SINARA lovers, tahukah Anda bahwa kanker serviks lebih sering ditularkan melalui hubungan seksual. Hasil penelitian menyatakan bahwa aktivitas seksual pada usia kurang dari 20 tahun meningkatkan risiko kanker serviks pada wanita. Wanita yang melakukan hubungan seksual pertama kali dalam waktu 5 tahun dari usia pertama kali menstruasi, meningkatkan risiko terkena kanker serviks karena serviks atau leher rahim yang belum berfungsi dengan baik.

Selain itu, usia saat menikah pertama kali tahun juga berkaitan dengan aktivitas seksual yang dapat meningkatkan risiko kanker serviks. Usia menikah pertama kali di bawah 20 tahun meningkatkan risiko kanker serviks, terutama menikah dibawah usia 16 tahun.

Selain usia, wanita dengan banyak pasangan seksual juga meningkatkan risiko kanker serviks, karena virus penyebab kanker serviks, yaitu HPV dapat ditularkan melalui kontak seksual dari pasangan yang telah terinfeksi HPV pada genitalianya. Risiko ini akan lebih meningkat pada wanita yang aktif melakukan hubungan seksual dengan sering berganti pasangan seksual.

Nah SINARA lovers, sudah tahu kan kalau aktivitas seksual di usia muda dan tidak aman itu lebih berisiko terkena kanker serviks, jadi lebih baik dihindari ya!!!',
                'image' => null,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Kanker Serviks Bisa Dicegah',
                'content' => 'Hai SINARA Lovers, tahukah kamu kalau kanker serviks itu bisa dicegah? Penasaran kan? Yuk kita bahas. Ada beberapa jenis pencegahan kanker serviks.

**Pencegahan Primer dengan Vaksinasi HPV**
Vaksin ini direkomendasikan untuk anak-anak dan orang dewasa berusia 9 hingga 26 tahun. Vaksinasi HPV secara rutin direkomendasikan pada usia 11 atau 12 tahun; vaksinasi dapat dimulai pada usia 9 tahun. Vaksinasi HPV direkomendasikan untuk semua orang hingga usia 26 tahun yang tidak divaksinasi sebelumnya secara memadai. Vaksinasi HPV tidak dianjurkan untuk semua orang dewasa berusia 27 hingga 45 tahun, karena pada rentang usia ini memberikan manfaat yang lebih kecil dimana lebih banyak orang yang telah terpapar virus tersebut.

**Pencegahan Sekunder dengan Skrining**
Pencegahan kedua merupakan pencegahan sekunder dengan melakukan skrining kanker serviks dengan beberapa metode, yaitu inspeksi visual melalui asam asetat (IVA), papsmear dan tes HPV DNA. Di Indonesia, yang paling sering ditemuai adalah pemeriksaan IVA dan papsmear, sedangkan yang telah dijadikan program nasional adalah pemeriksaan IVA.

**Persiapan Sebelum Pemeriksaan IVA/Papsmear:**
1. Tidak sedang menstruasi. Pemeriksaan sebaiknya dilakukan dua minggu setelah menstruasi dimulai dan sebelum menstruasi berikutnya
2. Tidak melakukan hubungan seksual minimal 24 jam sebelum melakukan pemeriksaan
3. Tidak menggunakan pembersih wanita minimal 24 jam sebelum pemeriksaan
4. Tidak menggunakan obat-obatan yang dimasukkan dalam vagina minimal 48 jam sebelum pemeriksaan

Skrining kanker serviks yang dilakukan secara dini mampu mendeteksi lebih awal adanya sel kanker atau tidak, sehingga penanganan yang diberikan lebih optimal. Mencegah lebih baik daripada mengobat. Nah, tunggu apalagi, skrining kanker serviks sekarang yuuk...',
                'image' => null,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Kasus Kanker Serviks Masih Tinggi',
                'content' => 'Hai SINARA lovers, tahukah Anda ternyata Kanker Serviks masih menjadi salah satu kanker terbanyak yang dialami wanita di dunia loh. Menurut data International Agency for Research on Cancer, pada tahun 2020 terdapat 604.127 (3,1%) kasus kanker serviks dengan 341.831 (3,3%) jumlah kematian. Angka ini mengalami peningkatan dari tahun 2018, dimana sekitar 569.847 kasus baru didiagnosa dan 311.365 jumlah kematian terjadi karena kanker serviks.

**Situasi di Indonesia**
Bukan hanya di dunia, Indonesia juga memiliki angka kanker serviks cukup tinggi hingga menempati urutan kedua kanker terbanyak pada wanita. Eitss, rangkingnya bukan prestasi loh, tapi penyakit. Ngeriii yaa.... Tahun 2020 tercatat sebanyak 36.633 (17.2%) kasus baru yang terdiagnosa. Angka ini juga mengalami peningkatan dari tahun 2018, yaitu sekitar 32.469 kasus baru terdiagnosa.

**Situasi di Maluku**
Bagaimana dengan Maluku??? Sayangnya, belum ada data yang valid terkait jumlah kanker serviks di Maluku. Namun, dari data Surveilans Dinas Kesehatan Kota Ambon, di tahun 2023 terdapat 3 kasus kanker serviks. Ini hanya kasus yang terdata, bisa saja masih banyak kasus di luar sana yang belum terdata, karena banyak wanita yang tidak melakukan pengobatan dan akhirnya meninggal tanpa diagnosa medis yang pasti.',
                'image' => null,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Skrining Kanker Serviks Tersedia dan Murah di Puskesmas',
                'content' => 'SINARA Lovers, ternyata ada loh skrining kanker serviks yang murah dan tersedia di puskemas. Namanya pemeriksaan IVA. Pemeriksaannya murah, mudah dan cepat. Pemeriksaan IVA dilakukan dengan mata telanjang menggunakan larutab asam asetat 3-5%, diusapkan pada serviks atau leher rahim menggunakan lidi kapas, dan hasilnya dapat diketahui setelah satu menit. Apabila hasilnya negatif, epitel skuamosa serviks akan tetap berwarna merah muda dan hasil positif ditandai dengan perubahan warna menjadi putih atau terdapat luka dan bercak darah.

**Efektivitas Pemeriksaan IVA**
Hasil penelitian menunjukkan bahwa sensitivitas dan spesifisitas IVA yaitu 77% (95% CI: 65-85) dan 82% (95% CI: 67-91), dibandingkan sensitivitas dan spesifitas papsmear yaitu 84% (95% CI: 76-90 dan 88% (95% CI: 79-93%). Hasil penelitian juga menyatakan bahwa, wanita yang melakukan pemeriksaan IVA, kecil kemungkinan (25%) untuk menderita kanker serviks dikemudian hari dan kecil kemungkinan (35%) untuk meninggal akibat kanker serviks, dibandingkan yang tidak melakukan pemeriksaan.

**Keunggulan IVA**
Pemeriksaan IVA cocok dilakukan di wilayah berpenghasilan rendah, karena prosedur yang mudah dan dapat dilakukan oleh tenaga terlatih. Selain itu, IVA merupakan metode non-invasive, relatif murah dan hasilnya dapat diketahui segera.

**Siapa yang Perlu Diperiksa?**
Wanita yang telah melakukan hubungan seksual. Bagi wanita usia 25-49 tahun dengan hasil pemeriksaan IVA negatif dianjurkan melakukan pemeriksaan 3-5 tahun sekali, sedangkan bagi wanita usia di bawah 25 tahun, skrining dilakukan hanya jika berisiko tinggi terinfeksi HPV.

**Syarat Sebelum Pemeriksaan IVA:**
- Tidak sedang menstruasi
- Tidak sedang hamil
- Tidak melakukan hubungan seksual minimal 24 jam sebelum pemeriksaan

AYO, PERIKSA IVA SEKARANG JUGA!!!',
                'image' => null,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Efektivitas Fisioterapi Active Cycle of Breathing Technique (ACBT) pada Pasien Anak dengan Efusi Pleura',
                'content' => 'Efusi pleura adalah kondisi di mana terjadi penumpukan cairan berlebih di rongga pleura, yaitu ruang antara pleura parietal dan viseral yang membungkus paru-paru. Kondisi ini dapat disebabkan oleh berbagai faktor, termasuk infeksi, keganasan, gagal jantung, atau penyakit sistemik lainnya.

**Active Cycle of Breathing Technique (ACBT)**
ACBT adalah teknik pernafasan aktif untuk membersihkan jalan napas bagi individu dengan penyakit paru yang ditandai dengan produksi sputum yang berlebih sehingga menyebabkan retensi sputum dan obstruksi jalan napas. ACBT merupakan salah satu latihan pernafasan untuk mengontrol pernafasan agar menghasilkan pola pernafasan yang tenang dan ritmis sehingga menjaga kinerja otot-otot pernafasan dan merangsang keluarnya sputum untuk membuka jalan napas.

**Komponen ACBT:**
1. **Breathing Control (BC)** - Pernafasan volume tidal untuk mengurangi sesak napas
2. **Thoracic Expansion Exercise (TEE)** - Latihan ekspansi thorax untuk meningkatkan aliran udara
3. **Forced Expiration Technique (FET)** - Teknik ekspirasi paksa untuk membantu mengeluarkan sputum

**Manfaat ACBT:**
- Mengembalikan dan memelihara fungsi otot-otot pernafasan
- Meningkatkan efisiensi pernafasan dan ekspansi paru
- Membantu pasien bernapas dengan bebas
- Mengeluarkan sekret dari saluran pernafasan

Penelitian menunjukkan bahwa ACBT efektif dalam meningkatkan ekspansi dada, menurunkan sesak napas, mengurangi dahak, dan meminimalkan eksaserbasi pada pasien dengan efusi pleura.',
                'image' => null,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('articles')->insert($articles);
    }
}