<?php

namespace App\Http\Controllers;

use App\Models\EBook;
use App\Models\ProgresEbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EBookController extends Controller
{
    /**
     * Daftar koleksi e-book dengan info progres user.
     */
    public function index()
    {
        $user   = Auth::user();

        $cacheKey = 'siswa_ebook_index_' . $user->id;
        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 43200, function() use ($user) {
            $ebooks = EBook::aktif()->orderBy('level')->get();

            // Ambil semua progres user ini
            $progresData = ProgresEbook::where('user_id', $user->id)
                ->get()->keyBy('e_book_id');

            // Tentukan e-book mana yang terbuka (unlocked)
            $ebooks = $ebooks->map(function ($ebook) use ($progresData) {
                $progres = $progresData[$ebook->id] ?? null;
                $ebook->sudah_selesai = $progres ? $progres->selesai : false;
                $ebook->lulus_kuis = $progres ? $progres->lulus_kuis : false;
                $ebook->lulus_suara = $progres ? $progres->lulus_suara : false;

                // Level 1 selalu terbuka; level > 1 terbuka jika level sebelumnya selesai
                if ($ebook->level === 1) {
                    $ebook->terbuka = true;
                } else {
                    $prevBook = EBook::where('level', $ebook->level - 1)->first();
                    $ebook->terbuka = $prevBook
                        ? ($progresData[$prevBook->id]->selesai ?? false)
                        : false;
                }
                return $ebook;
            });
            return compact('ebooks');
        });
        extract($data);

        return view('siswa.ebook.index', compact('ebooks'));
    }

    /**
     * Halaman baca e-book.
     */
    public function read(EBook $ebook)
    {
        $user = Auth::user();

        // Cek apakah e-book ini terbuka untuk user
        if ($ebook->level > 1) {
            $prevBook = EBook::where('level', $ebook->level - 1)->first();
            $prevDone = $prevBook
                ? ProgresEbook::where('user_id', $user->id)
                    ->where('e_book_id', $prevBook->id)
                    ->where('selesai', true)
                    ->exists()
                : false;

            if (!$prevDone) {
                return redirect()->route('ebook.index')
                    ->with('error', 'Selesaikan e-book sebelumnya terlebih dahulu!');
            }
        }

        $progres = ProgresEbook::firstOrCreate(
            ['user_id' => $user->id, 'e_book_id' => $ebook->id],
            ['selesai' => false]
        );

        if ($progres->wasRecentlyCreated) {
            \Illuminate\Support\Facades\Cache::forget('siswa_ebook_index_' . $user->id);
        }

        if ($progres->lulus_suara && !$progres->selesai) {
            if ($progres->lulus_kuis) {
                return redirect()->route('ebook.indikator.show', ['jenis' => 'digital', 'id' => $ebook->id]);
            } elseif ($ebook->questions()->count() > 0) {
                return redirect()->route('ebook.quiz.page', $ebook->id);
            }
        }

        $catatan = \App\Models\CatatanMembaca::where('user_id', $user->id)
            ->where('jenis_buku', 'digital')
            ->where('buku_id', $ebook->id)
            ->first();

        $jawabanSiswa = \App\Models\JawabanIndikator::where('user_id', $user->id)
            ->where('jenis_buku', 'digital')
            ->where('buku_id', $ebook->id)
            ->whereNotNull('nilai_guru')
            ->get();
            
        $rataNilai = null;
        if ($jawabanSiswa->count() > 0) {
            $rataNilai = round($jawabanSiswa->avg('nilai_guru'), 1);
        }

        return view('siswa.ebook.read', compact('ebook', 'progres', 'catatan', 'rataNilai'));
    }

    /**
     * Halaman kuis e-book.
     */
    public function quizPage(EBook $ebook)
    {
        $user = Auth::user();
        
        $progres = ProgresEbook::where('user_id', $user->id)
            ->where('e_book_id', $ebook->id)
            ->first();

        if (!$progres || !$progres->lulus_suara) {
            return redirect()->route('ebook.read', $ebook->id)
                ->with('error', 'Silakan selesaikan tahap membaca dan verifikasi suara terlebih dahulu.');
        }

        if ($progres->selesai) {
            return redirect()->route('ebook.index')
                ->with('success', 'Anda sudah menyelesaikan e-book ini.');
        }

        if ($progres->lulus_kuis) {
            return redirect()->route('ebook.indikator.show', ['jenis' => 'digital', 'id' => $ebook->id]);
        }

        return view('siswa.ebook.quiz', compact('ebook', 'progres'));
    }

    /**
     * Simpan teks suara secara bertahap (Auto-save)
     */
    public function saveVoiceProgress(Request $request, EBook $ebook)
    {
        $request->validate([
            'teks_suara' => 'required|string',
        ]);

        $progres = ProgresEbook::where('user_id', Auth::id())
            ->where('e_book_id', $ebook->id)
            ->first();

        if ($progres) {
            $progres->update(['akumulasi_teks' => $request->teks_suara]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Terima suara dari browser, bandingkan dengan konten teks e-book.
     * Kembalikan skor kesamaan (Jaccard / simple word overlap).
     */
    public function checkVoice(Request $request, EBook $ebook)
    {
        $request->validate([
            'teks_suara' => 'required|string|min:3',
        ]);

        $progres  = ProgresEbook::where('user_id', Auth::id())
                      ->where('e_book_id', $ebook->id)
                      ->first();

        // 1. Perbaiki teks dengan Gemini API (Algoritma Perbaikan Kata/Kalimat)
        $teksBaru = trim($request->teks_suara);
        $apiKey = env('GEMINI_API_KEY');
        if ($apiKey && !empty($teksBaru)) {
            $prompt = "Perbaiki typo atau salah eja dari hasil transkripsi suara berikut menjadi bahasa Indonesia baku tanpa mengubah maksud asli. JANGAN beri basa-basi atau kalimat pembuka, HANYA kembalikan teks hasil perbaikan secara langsung. Teks: \"{$teksBaru}\"";
            
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->withoutVerifying()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]);
                
                if ($response->successful()) {
                    $result = $response->json();
                    $corrected = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    if (!empty(trim($corrected))) {
                        $teksBaru = trim(str_replace(["\r", "\n", '"'], '', $corrected));
                    }
                }
            } catch (\Exception $e) {
                // Abaikan error API agar tidak menghalangi murid (fallback ke teks mentah)
            }
        }

        // 2. Akumulasi Teks Baru ke dalam Database
        $akumulasi = $teksBaru;
        if ($progres) {
            if (!empty($progres->akumulasi_teks)) {
                $akumulasi = $progres->akumulasi_teks . ' ' . $teksBaru;
            }
            $progres->update(['akumulasi_teks' => $akumulasi]);
        }
        \Illuminate\Support\Facades\Cache::forget('siswa_ebook_index_' . Auth::id());

        $referensi = strtolower($ebook->konten_teks ?? '');
        $suara     = strtolower($akumulasi);

        $wordsRef   = array_filter(str_word_count($referensi, 1));
        $wordsSuara = array_filter(str_word_count($suara, 1));

        if (empty($wordsRef)) {
            $skor = 0;
        } else {
            $intersection = array_intersect($wordsSuara, $wordsRef);
            $union        = array_unique(array_merge($wordsRef, $wordsSuara));
            $skor         = count($union) > 0
                ? round((count($intersection) / count($union)) * 100, 2)
                : 0;
        }

        $lulus    = $skor >= 60; // threshold 60%

        if ($progres) {
            $updateData = ['skor_suara' => $skor];
            if ($lulus) {
                $updateData['lulus_suara'] = true;
                // Jika ebook ini tidak punya soal, kita anggap lulus kuis = true
                if ($ebook->questions()->count() === 0) {
                    $updateData['lulus_kuis'] = true;
                    // Selesai akan diupdate setelah mengisi indikator
                }
            }
            $progres->update($updateData);
        }

        return response()->json([
            'skor'   => $skor,
            'lulus'  => $lulus,
            'has_quiz' => $ebook->questions()->count() > 0,
            'teks_diperbaiki' => $teksBaru,
            'pesan'  => $lulus
                ? 'Selamat! Kesamaan total bacaan Anda mencapai ' . $skor . '%. Lanjut ke tahap kuis.'
                : 'Tahap disimpan. Progres kesamaan: ' . $skor . '% (Target: 60%). Lanjutkan membaca!',
        ]);
    }

    /**
     * Melewati proses verifikasi suara bagi siswa yang memiliki akses (skip_voice_verification = true).
     */
    public function skipVoiceVerification(EBook $ebook)
    {
        $user = Auth::user();
        if (!$user->skip_voice_verification) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $progres = ProgresEbook::where('user_id', $user->id)
            ->where('e_book_id', $ebook->id)
            ->first();

        if ($progres) {
            $updateData = ['lulus_suara' => true, 'skor_suara' => 100];
            if ($ebook->questions()->count() === 0) {
                $updateData['lulus_kuis'] = true;
                // Selesai akan diupdate setelah mengisi indikator
            }
            $progres->update($updateData);
            \Illuminate\Support\Facades\Cache::forget('siswa_ebook_index_' . $user->id);
        }

        return response()->json([
            'lulus' => true,
            'pesan' => 'Verifikasi suara berhasil dilewati. Melanjutkan...'
        ]);
    }

    public function getKuis(EBook $ebook)
    {
        $progres = ProgresEbook::where('user_id', Auth::id())
                      ->where('e_book_id', $ebook->id)
                      ->first();

        if (!$progres || !$progres->lulus_suara) {
            return response()->json(['error' => 'Belum lulus verifikasi suara.'], 403);
        }

        if ($progres->lulus_kuis) {
            return response()->json(['lulus' => true, 'pesan' => 'Anda sudah lulus kuis ini.']);
        }

        // Ambil 3 soal acak jika belum punya, atau pakai yang sudah ada di jawaban_kuis
        $jawabanKuis = $progres->jawaban_kuis ?? [];
        if (empty($jawabanKuis)) {
            $questions = $ebook->questions()->inRandomOrder()->limit(3)->get();
            foreach ($questions as $q) {
                $jawabanKuis[$q->id] = null; // null = belum dijawab
            }
            $progres->update(['jawaban_kuis' => $jawabanKuis]);
        } else {
            $questionIds = array_keys($jawabanKuis);
            $questions = $ebook->questions()->whereIn('id', $questionIds)->get();
        }

        $formattedQuestions = $questions->map(function($q) {
            $opsi = $q->opsi_jawaban;
            shuffle($opsi); // Acak opsi
            return [
                'id' => $q->id,
                'pertanyaan' => $q->pertanyaan,
                'opsi' => $opsi
            ];
        });

        return response()->json([
            'questions' => $formattedQuestions
        ]);
    }

    public function submitKuis(Request $request, EBook $ebook)
    {
        $request->validate([
            'jawaban' => 'required|array'
        ]);

        $progres = ProgresEbook::where('user_id', Auth::id())
                      ->where('e_book_id', $ebook->id)
                      ->first();

        if (!$progres || !$progres->lulus_suara) {
            return response()->json(['error' => 'Belum lulus verifikasi suara.'], 403);
        }

        $jawabanSiswa = $request->jawaban;
        $questionIds = array_keys($jawabanSiswa);
        $questions = $ebook->questions()->whereIn('id', $questionIds)->get()->keyBy('id');

        $benar = 0;
        $total = count($jawabanSiswa);

        $hasilJawaban = [];
        foreach ($jawabanSiswa as $qId => $jawaban) {
            $q = $questions[$qId] ?? null;
            $isBenar = false;
            if ($q && strtolower(trim($q->kunci_jawaban)) == strtolower(trim($jawaban))) {
                $isBenar = true;
                $benar++;
            }
            $hasilJawaban[$qId] = [
                'jawaban' => $jawaban,
                'benar' => $isBenar,
                'kunci' => $q ? $q->kunci_jawaban : ''
            ];
        }

        if ($benar == 1) {
            $skor = 30;
        } elseif ($benar == 2) {
            $skor = 60;
        } elseif ($benar >= 3) {
            $skor = 100;
        } else {
            $skor = 0;
        }

        $lulus = $skor >= 60; // KKM Kuis 60 (Benar 2 atau lebih)

        $updateData = [
            'jawaban_kuis' => $hasilJawaban,
            'skor_kuis' => $skor,
        ];

        if ($lulus) {
            $updateData['lulus_kuis'] = true;
            // Selesai akan diupdate setelah mengisi indikator
        } else {
            // Jika tidak lulus, reset agar bisa diulang dengan soal baru
            $updateData['jawaban_kuis'] = null;
        }

        $progres->update($updateData);
        \Illuminate\Support\Facades\Cache::forget('siswa_ebook_index_' . Auth::id());

        return response()->json([
            'skor' => $skor,
            'lulus' => $lulus,
            'benar' => $benar,
            'total' => $total,
            'pesan' => $lulus 
                ? "Selamat! Anda lulus kuis dengan skor {$skor}."
                : "Skor Anda {$skor}. Anda harus mengulang kuis."
        ]);
    }

    public function streamPdf(EBook $ebook)
    {
        if (!$ebook->file_pdf) {
            abort(404, 'File PDF belum diunggah.');
        }

        $path = storage_path('app/public/' . $ebook->file_pdf);

        if (!file_exists($path)) {
            abort(404, 'File PDF tidak ditemukan di server (Path: ' . $path . ').');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
        ]);
    }
}
