<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Indikator Literasi</span>
    </x-slot>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-800">Manajemen Indikator Literasi</h2>
                <p class="text-sm text-slate-400 mt-0.5">Kelola daftar pertanyaan untuk menguji pemahaman membaca murid</p>
            </div>
            <a href="{{ route('guru.indikator.create') }}"
               class="inline-flex items-center gap-2 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-5 py-2.5 rounded-xl text-sm transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Pertanyaan
            </a>
        </div>

        @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <p class="text-sm text-slate-500">
                    Menampilkan <span class="font-bold text-slate-800">{{ count($indikators) }}</span> pertanyaan indikator
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70">
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Pertanyaan</th>
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center w-32">Status</th>
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($indikators as $index => $indikator)
                        <tr class="hover:bg-slate-50/60 transition duration-150">
                            <td class="py-3.5 px-5 text-sm text-slate-400 font-semibold">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-5">
                                <p class="font-semibold text-slate-800 text-sm">{{ $indikator->pertanyaan }}</p>
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                @if($indikator->aktif)
                                    <span class="inline-block px-3 py-1.5 rounded-lg text-[.7rem] font-bold bg-green-50 text-green-700 border border-green-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1.5 rounded-lg text-[.7rem] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('guru.indikator.edit', $indikator->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-xs transition duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('guru.indikator.destroy', $indikator->id) }}"
                                          onsubmit="return confirm('Hapus pertanyaan ini?')">
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
                            <td colspan="4" class="py-12 text-center text-slate-400 text-sm">
                                Belum ada pertanyaan indikator.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
