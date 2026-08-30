<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobTracker;
use Illuminate\Support\Facades\Auth;

class JobTrackerController extends Controller
{
    public function checkStatus()
    {
        if (!Auth::check()) {
            return response()->json(['job' => null]);
        }

        // Ambil job terbaru yang belum di-acknowledge (status pending, running, completed, failed)
        $job = JobTracker::where('user_id', Auth::id())
            ->where('status', '!=', 'acknowledged')
            ->orderBy('id', 'desc')
            ->first();

        return response()->json(['job' => $job]);
    }

    public function acknowledge(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false]);
        }

        $job = JobTracker::where('user_id', Auth::id())
            ->where('id', $request->id)
            ->first();

        if ($job) {
            $job->update(['status' => 'acknowledged']);
            // Optionally delete it entirely: $job->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function download($id)
    {
        $job = JobTracker::findOrFail($id);
        
        if ($job->user_id !== Auth::id()) {
            abort(403);
        }

        $path = storage_path('app/public/exports/' . $job->result_url);
        if ($job->result_url && file_exists($path)) {
            // Kita biarkan filenya ada untuk di-download ulang jika perlu, 
            // atau bisa hapus setelah send. Di sini kita hapus agar rapi.
            return response()->download($path)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'File tidak ditemukan atau sudah kadaluarsa.');
    }
}
