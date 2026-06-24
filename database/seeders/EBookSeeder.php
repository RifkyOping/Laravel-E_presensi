<?php

namespace Database\Seeders;

use App\Models\EBook;
use Illuminate\Database\Seeder;

class EBookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'level'       => 1,
                'judul'       => 'Pengenalan Literasi Digital',
                'kategori'    => 'Dasar',
                'deskripsi'   => 'Modul pengantar untuk memahami konsep dasar literasi digital di era modern.',
                'konten_teks' => 'Literasi digital adalah kemampuan untuk menggunakan teknologi informasi dan komunikasi secara efektif dan efisien. Kemampuan ini mencakup mencari informasi yang tepat, mengevaluasi kredibilitas sumber, dan menggunakan perangkat digital secara aman dan bertanggung jawab. Di era modern, literasi digital menjadi keterampilan yang sangat penting bagi setiap individu.',
                'file_pdf'    => null,
                'aktif'       => true,
            ],
            [
                'level'       => 2,
                'judul'       => 'Membaca Kritis di Era Digital',
                'kategori'    => 'Menengah',
                'deskripsi'   => 'Panduan membaca dan menganalisis informasi secara kritis dari berbagai sumber digital.',
                'konten_teks' => 'Membaca kritis adalah kemampuan untuk menganalisis dan mengevaluasi teks secara mendalam. Pembaca yang kritis tidak hanya memahami isi teks, tetapi juga mampu mengidentifikasi tujuan penulis, membedakan fakta dari opini, dan menilai kredibilitas sumber informasi. Keterampilan ini sangat penting untuk menghindari hoaks dan disinformasi yang beredar di media sosial.',
                'file_pdf'    => null,
                'aktif'       => true,
            ],
            [
                'level'       => 3,
                'judul'       => 'Strategi Belajar Mandiri dengan Teknologi',
                'kategori'    => 'Lanjutan',
                'deskripsi'   => 'Strategi dan teknik belajar mandiri yang efektif menggunakan platform digital.',
                'konten_teks' => 'Belajar mandiri dengan teknologi membutuhkan kedisiplinan dan strategi yang tepat. Manfaatkan berbagai platform pembelajaran daring seperti video edukasi, e-book, dan forum diskusi online. Buat jadwal belajar yang konsisten, tetapkan target yang jelas, dan selalu evaluasi kemajuan belajar Anda. Kolaborasi dengan teman secara virtual juga dapat meningkatkan motivasi dan pemahaman materi.',
                'file_pdf'    => null,
                'aktif'       => true,
            ],
        ];

        foreach ($books as $book) {
            EBook::firstOrCreate(['level' => $book['level']], $book);
        }
    }
}
