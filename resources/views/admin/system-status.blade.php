<x-app-layout>
<div class="min-h-screen bg-slate-50 p-6 font-sans text-slate-800">
    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#24417c]">Status Sistem & Antrean Pekerjaan</h1>
                <p class="text-slate-500 text-sm mt-1">Pantau status antrean pekerjaan (Jobs) dan tangani job yang gagal.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Job Gagal -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col">
                <div class="px-6 py-5 border-b border-slate-100 bg-red-50/50 rounded-t-xl flex justify-between items-center">
                    <h2 class="text-lg font-bold text-red-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Job Gagal ({{ $failedJobs->count() }})
                    </h2>
                    @if($failedJobs->count() > 0)
                    <form action="{{ route('admin.system-status.flush-jobs') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA job yang gagal?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition-colors">
                            Bersihkan Semua
                        </button>
                    </form>
                    @endif
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                                <th class="px-6 py-3 font-semibold">ID</th>
                                <th class="px-6 py-3 font-semibold">Pekerjaan</th>
                                <th class="px-6 py-3 font-semibold">Waktu Gagal</th>
                                <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-slate-700 divide-y divide-slate-100">
                            @forelse($failedJobs as $job)
                                @php 
                                    $payload = json_decode($job->payload, true); 
                                    $displayName = $payload['displayName'] ?? 'Unknown Job';
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-3 whitespace-nowrap">{{ $job->id }}</td>
                                    <td class="px-6 py-3">
                                        <div class="font-medium text-slate-900 truncate max-w-[200px]" title="{{ $displayName }}">
                                            {{ class_basename($displayName) }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 truncate max-w-[200px]" title="{{ $job->exception }}">
                                            {{ Str::limit($job->exception, 50) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-xs">
                                        {{ \Carbon\Carbon::parse($job->failed_at)->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right flex justify-end gap-2">
                                        <form action="{{ route('admin.system-status.retry-job', $job->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold hover:bg-blue-200 transition-colors" title="Coba Lagi">
                                                Coba
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.system-status.forget-job', $job->id) }}" method="POST" onsubmit="return confirm('Hapus job ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold hover:bg-red-200 transition-colors" title="Hapus">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                        Tidak ada job yang gagal.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Job Antrean (Pending) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col">
                <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/50 rounded-t-xl">
                    <h2 class="text-lg font-bold text-amber-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Antrean Pekerjaan ({{ $pendingJobs->count() }})
                    </h2>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                                <th class="px-6 py-3 font-semibold">ID</th>
                                <th class="px-6 py-3 font-semibold">Pekerjaan / Antrean</th>
                                <th class="px-6 py-3 font-semibold">Dibuat Pada</th>
                                <th class="px-6 py-3 font-semibold">Coba</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-slate-700 divide-y divide-slate-100">
                            @forelse($pendingJobs as $job)
                                @php 
                                    $payload = json_decode($job->payload, true); 
                                    $displayName = $payload['displayName'] ?? 'Unknown Job';
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-3 whitespace-nowrap">{{ $job->id }}</td>
                                    <td class="px-6 py-3">
                                        <div class="font-medium text-slate-900 truncate max-w-[200px]" title="{{ $displayName }}">
                                            {{ class_basename($displayName) }}
                                        </div>
                                        <div class="text-[10px] text-slate-500">
                                            Queue: {{ $job->queue }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-xs">
                                        {{ \Carbon\Carbon::createFromTimestamp($job->created_at)->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                            {{ $job->attempts }}x
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                        Tidak ada job dalam antrean.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
</x-app-layout>
