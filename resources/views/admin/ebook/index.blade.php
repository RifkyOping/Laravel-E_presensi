<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Manajemen E-Book Literasi</span>
    </x-slot>

<div class="space-y-6">

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-3 text-sm text-green-700 font-medium flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-800">Manajemen E-Book</h2>
            <p class="text-sm text-slate-400 mt-0.5">Kelola koleksi e-book beserta file PDF untuk literasi siswa.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.ebook.students') }}"
               class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 font-bold px-5 py-2.5 rounded-xl text-sm transition shadow-sm">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Kelola Akses Suara
            </a>
            <a href="{{ route('admin.ebook.create') }}"
               class="inline-flex items-center gap-2 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-5 py-2.5 rounded-xl text-sm transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah E-Book
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="text-left px-5 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider w-12">Lvl</th>
                        <th class="text-left px-5 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">Judul E-Book</th>
                        <th class="text-left px-5 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">Soal Kuis</th>
                        <th class="text-left px-5 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">File PDF</th>
                        <th class="text-left px-5 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-right px-5 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($ebooks as $buku)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <div class="w-8 h-8 rounded-full bg-[#1e3a6e] flex items-center justify-center">
                                <span class="text-white text-xs font-black">{{ $buku->level }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-800">{{ $buku->judul }}</p>
                            @if($buku->deskripsi)
                            <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $buku->deskripsi }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded-full {{ $buku->questions->count() > 0 ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $buku->questions->count() }} Soal
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if($buku->file_pdf)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <a href="{{ asset('storage/' . $buku->file_pdf) }}" target="_blank"
                                   class="text-xs font-semibold text-[#1e3a6e] hover:underline">
                                    Lihat PDF
                                </a>
                            </div>
                            @else
                            <span class="text-xs text-slate-400 italic">Belum ada file</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <form method="POST" action="{{ route('admin.ebook.toggle', $buku->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="text-[.65rem] font-black px-2.5 py-1 rounded-full uppercase tracking-wide cursor-pointer transition
                                               {{ $buku->aktif
                                                   ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                   : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}">
                                    {{ $buku->aktif ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.ebook.edit', $buku->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-xs transition duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.ebook.destroy', $buku->id) }}"
                                      onsubmit="return confirm('Hapus e-book ini? File PDF juga akan dihapus.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-500 hover:text-white font-semibold text-xs transition duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <svg class="w-14 h-14 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <p class="font-bold text-slate-400">Belum ada e-book.</p>
                            <a href="{{ route('admin.ebook.create') }}" class="text-sm text-[#1e3a6e] font-bold hover:underline mt-1 inline-block">
                                Tambah E-Book Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ebooks->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $ebooks->links() }}
        </div>
        @endif
    </div>

</div>
</x-app-layout>
