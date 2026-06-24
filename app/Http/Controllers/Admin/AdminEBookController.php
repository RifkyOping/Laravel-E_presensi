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
        return view('admin.ebook.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'level'       => 'required|integer|min:1|unique:e_books,level',
            'judul'       => 'required|string|max:255',
            'deskripsi'   => 'nullable|string|max:500',
            'konten_teks' => 'nullable|string',
            'file_pdf'    => 'nullable|file|mimes:pdf|max:20480', // max 20MB
            'aktif'       => 'boolean',
        ], [
            'level.required' => 'Level wajib diisi.',
            'level.unique'   => 'Level ini sudah ada.',
            'level.min'      => 'Level minimal 1.',
            'judul.required' => 'Judul wajib diisi.',
            'file_pdf.mimes' => 'File harus berformat PDF.',
            'file_pdf.max'   => 'Ukuran file maksimal 20MB.',
        ]);

        $data = [
            'level'       => $request->level,
            'judul'       => $request->judul,
            'deskripsi'   => $request->deskripsi,
            'konten_teks' => $request->konten_teks,
            'aktif'       => $request->boolean('aktif', true),
            'file_pdf'    => null,
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
            'level'       => 'required|integer|min:1|unique:e_books,level,' . $ebook->id,
            'judul'       => 'required|string|max:255',
            'deskripsi'   => 'nullable|string|max:500',
            'konten_teks' => 'nullable|string',
            'file_pdf'    => 'nullable|file|mimes:pdf|max:20480',
            'aktif'       => 'boolean',
        ], [
            'level.unique'   => 'Level ini sudah digunakan.',
            'judul.required' => 'Judul wajib diisi.',
            'file_pdf.mimes' => 'File harus berformat PDF.',
            'file_pdf.max'   => 'Ukuran file maksimal 20MB.',
        ]);

        $data = [
            'level'       => $request->level,
            'judul'       => $request->judul,
            'deskripsi'   => $request->deskripsi,
            'konten_teks' => $request->konten_teks,
            'aktif'       => $request->boolean('aktif', true),
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

    protected function generateQuestions(EBook $ebook, $prompt)
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey || empty($prompt)) return false;

        $prompt = "Buat 10 pertanyaan pilihan ganda berdasarkan teks berikut. Teks: \"{$prompt}\".\nFormat JSON murni (array of objects), setiap object punya 'pertanyaan', 'opsi_jawaban' (array 4 string), dan 'kunci_jawaban' (string). Jangan tambahkan format markdown (seperti ```json).";

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
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
}
