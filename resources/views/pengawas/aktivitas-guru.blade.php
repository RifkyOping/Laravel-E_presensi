@php use Carbon\Carbon; @endphp
<x-pengawas-layout pageTitle="Aktivitas Mengajar" pageSubtitle="Monitoring jurnal mengajar guru">

<div class="space-y-6">

    {{-- Filter --}}
    <form method="GET" action="{{ route('pengawas.aktivitas-guru') }}"
          class="pw-card p-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="pw-label">Tanggal</label>
            <input type="date" name="tanggal" class="pw-input" value="{{ request('tanggal') }}">
        </div>
        <div>
            <label class="pw-label">Filter Guru</label>
            <select name="guru_id" class="pw-input">
                <option value="">— Semua Guru —</option>
                @foreach($semuaGuru as $g)
                <option value="{{ $g->id }}" {{ request('guru_id')==$g->id?'selected':'' }}>{{ $g->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-3">
            <button type="submit" class="px-5 py-2 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold text-sm rounded-lg transition">
                Terapkan
            </button>
            <a href="{{ route('pengawas.aktivitas-guru') }}"
               class="px-5 py-2 border border-slate-200 hover:border-slate-400 text-slate-600 font-semibold text-sm rounded-lg transition">
                Reset
            </a>
        </div>
    </form>

    {{-- Tabel --}}
    <div class="pw-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Jurnal Aktivitas Mengajar</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $aktivitas->total() }} sesi ditemukan</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full pw-tbl">
                <thead><tr>
                    <th>Tanggal</th><th>Guru</th><th>Mata Pelajaran</th>
                    <th>Kelas</th><th class="text-center">Jam ke-</th>
                    <th>Waktu</th><th>Materi</th><th>Metode</th>
                    <th class="text-center">Siswa Hadir</th>
                </tr></thead>
                <tbody>
                    @forelse($aktivitas as $item)
                    <tr>
                        <td class="font-semibold whitespace-nowrap">{{ Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td class="font-semibold text-slate-800">{{ $item->user->name }}</td>
                        <td>{{ $item->mata_pelajaran }}</td>
                        <td><span class="pw-badge b-blue">{{ $item->kelas }}</span></td>
                        <td class="text-center">
                            <span class="inline-flex w-7 h-7 rounded-full bg-[#1e3a6e] text-white items-center justify-center font-bold text-xs">
                                {{ $item->jam_ke }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap">
                            {{ Carbon::parse($item->jam_mulai)->format('H:i') }}
                            @if($item->jam_selesai) – {{ Carbon::parse($item->jam_selesai)->format('H:i') }} @endif
                        </td>
                        <td class="max-w-xs">
                            <span class="line-clamp-2 block text-slate-600">{{ $item->materi }}</span>
                        </td>
                        <td>
                            @php $mc = match($item->metode) {
                                'daring'=>'b-purple','diskusi'=>'b-blue','praktik'=>'b-blue','ceramah'=>'b-blue',default=>'b-slate'
                            }; @endphp
                            <span class="pw-badge {{ $mc }} capitalize">{{ $item->metode }}</span>
                        </td>
                        <td class="text-center font-bold text-slate-800">{{ $item->jumlah_siswa_hadir }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-10 text-slate-400">Tidak ada data aktivitas mengajar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($aktivitas->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $aktivitas->links() }}</div>
        @endif
    </div>

</div>
</x-pengawas-layout>
