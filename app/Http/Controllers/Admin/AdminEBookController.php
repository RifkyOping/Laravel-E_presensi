<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class AdminEBookController extends Controller
{
    public function index()
    {
        $ebooks = EBook::orderBy('level')->paginate(15);
        return view('admin.ebook.index', compact('ebooks'));
    }

    public function create()
    {
        $maxLevel = EBook::max('level');
        $nextLevel = $maxLevel ? $maxLevel + 1 : 1;
        return view('admin.ebook.create', compact('nextLevel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'level' => 'required|integer|min:1|unique:e_books,level',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'konten_teks' => 'nullable|string',
            'file_pdf' => 'nullable|file|mimes:pdf|max:20480', // max 20MB
            'aktif' => 'boolean',
        ], [
            'level.required' => 'Level wajib diisi.',
            'level.unique' => 'Level ini sudah ada.',
            'level.min' => 'Level minimal 1.',
            'judul.required' => 'Judul wajib diisi.',
            'file_pdf.mimes' => 'File harus berformat PDF.',
            'file_pdf.max' => 'Ukuran file maksimal 20MB.',
        ]);

        $data = [
            'level' => $request->level,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'konten_teks' => $request->konten_teks,
            'aktif' => $request->boolean('aktif', true),
            'file_pdf' => null,
        ];

        if ($request->hasFile('file_pdf')) {
            $data['file_pdf'] = $request->file('file_pdf')
                ->store('ebooks', 'public');
        }

        $ebook = EBook::create($data);

        if ($request->konten_teks) {
            $this->generateQuestions($ebook, $request->konten_teks);
        }

        return redirect()->route('admin.ebook.index')
            ->with('success', "E-Book \"{$request->judul}\" berhasil ditambahkan.");
    }

    public function edit(EBook $ebook)
    {
        return view('admin.ebook.edit', compact('ebook'));
    }

    public function update(Request $request, EBook $ebook)
    {
        $request->validate([
            'level' => 'required|integer|min:1|unique:e_books,level,' . $ebook->id,
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'konten_teks' => 'nullable|string',
            'file_pdf' => 'nullable|file|mimes:pdf|max:20480',
            'aktif' => 'boolean',
        ], [
            'level.unique' => 'Level ini sudah digunakan.',
            'judul.required' => 'Judul wajib diisi.',
            'file_pdf.mimes' => 'File harus berformat PDF.',
            'file_pdf.max' => 'Ukuran file maksimal 20MB.',
        ]);

        $data = [
            'level' => $request->level,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'konten_teks' => $request->konten_teks,
            'aktif' => $request->boolean('aktif', true),
        ];

        if ($request->hasFile('file_pdf')) {
            // Hapus file lama jika ada
            if ($ebook->file_pdf) {
                Storage::disk('public')->delete($ebook->file_pdf);
            }
            $data['file_pdf'] = $request->file('file_pdf')
                ->store('ebooks', 'public');
        }

        // Hapus file jika user centang "hapus pdf"
        if ($request->boolean('hapus_pdf') && $ebook->file_pdf) {
            Storage::disk('public')->delete($ebook->file_pdf);
            $data['file_pdf'] = null;
        }

        $originalKonten = $ebook->konten_teks;
        $ebook->update($data);

        // Generate soal jika konten berubah atau belum punya soal sama sekali
        if ($request->konten_teks && ($request->konten_teks !== $originalKonten || $ebook->questions()->count() == 0)) {
            $this->generateQuestions($ebook, $request->konten_teks);
        }

        return redirect()->route('admin.ebook.index')
            ->with('success', "E-Book \"{$ebook->judul}\" berhasil diperbarui.");
    }

    public function destroy(EBook $ebook)
    {
        if ($ebook->file_pdf) {
            Storage::disk('public')->delete($ebook->file_pdf);
        }
        $judul = $ebook->judul;
        $ebook->delete();

        return redirect()->route('admin.ebook.index')
            ->with('success', "E-Book \"{$judul}\" berhasil dihapus.");
    }

    public function toggleAktif(EBook $ebook)
    {
        $ebook->update(['aktif' => !$ebook->aktif]);
        $status = $ebook->aktif ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "E-Book \"{$ebook->judul}\" berhasil {$status}.");
    }

    public function cleanText(Request $request)
    {
        $request->validate([
            'text' => 'required|string'
        ]);

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'API Key Gemini tidak ditemukan.'], 500);
        }

        $prompt = "Bersihkan teks berikut dari elemen yang tidak perlu (seperti nomor halaman, header, daftar isi, atau karakter aneh hasil copy-paste PDF). Kembalikan HANYA teks konten bacaan yang rapi dan siap dibaca tanpa tambahan kalimat pengantar. Teks:\n\n" . $request->text;

        try {
            $response = Http::timeout(120)->withoutVerifying()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $cleanedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

                return response()->json(['cleaned_text' => trim($cleanedText)]);
            }

            return response()->json(['error' => 'API Gemini Error: ' . $response->body()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    protected function generateQuestions(EBook $ebook, $prompt)
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey || empty($prompt))
            return false;

        $prompt = "Buat 10 pertanyaan pilihan ganda berdasarkan teks berikut. Teks: \"{$prompt}\".\nFormat JSON murni (array of objects), setiap object punya 'pertanyaan', 'opsi_jawaban' (array 4 string), dan 'kunci_jawaban' (string). Jangan tambahkan format markdown (seperti ```json).";

        try {
            $response = Http::timeout(120)->withoutVerifying()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $text = str_replace(['```json', '```'], '', $text);
                $questions = json_decode(trim($text), true);

                if (is_array($questions)) {
                    $ebook->questions()->delete();
                    foreach ($questions as $q) {
                        \App\Models\EBookQuestion::create([
                            'e_book_id' => $ebook->id,
                            'pertanyaan' => $q['pertanyaan'],
                            'opsi_jawaban' => $q['opsi_jawaban'],
                            'kunci_jawaban' => $q['kunci_jawaban']
                        ]);
                    }
                    return true;
                }
            } else {
                // Log API error so we know what went wrong
                Log::error('Gemini API Error: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Gemini Request Exception: ' . $e->getMessage());
            return false;
        }
        return false;
    }

    public function studentsVoiceAccess(\Illuminate\Http\Request $request)
    {
        $tab = $request->query('tab', 'semua');
        $search = $request->query('search');

        $query = \App\Models\User::where('role', 'murid')
            ->with('siswaProfile');

        if ($tab === 'wajib') {
            $query->whereHas('siswaProfile', fn($q) => $q->where('skip_voice_verification', false))
                ->orWhereDoesntHave('siswaProfile');
        } elseif ($tab === 'bypass') {
            $query->whereHas('siswaProfile', fn($q) => $q->where('skip_voice_verification', true));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(20)->withQueryString();
        return view('admin.ebook.students', compact('students', 'tab'));
    }

    public function toggleVoiceAccess(\App\Models\User $user)
    {
        if ($user->role !== 'murid') {
            abort(403);
        }

        $profile = $user->siswaProfile;
        if (!$profile) {
            $profile = $user->siswaProfile()->create(['skip_voice_verification' => true]);
            $status = 'dimatikan';
        } else {
            $newStatus = !$profile->skip_voice_verification;
            $profile->update(['skip_voice_verification' => $newStatus]);
            $status = $newStatus ? 'dimatikan' : 'diaktifkan kembali';
        }

        return back()->with('success', "Verifikasi suara untuk {$user->name} berhasil {$status}.");
    }
}
