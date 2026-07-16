<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Position;
use Illuminate\Support\Facades\File;

class VoteController extends Controller
{
    // 1. Tampilan Display Proyektor
    public function display() {
        $positions = Position::with('candidates')->get();
        return view('vote.display', compact('positions'));
    }

    // 2. Tampilan Admin Counter
    public function admin() {
        $positions = Position::with('candidates')->get();
        return view('vote.admin', compact('positions'));
    }

    // 3. Tampilan Manajemen Kandidat & Jabatan (CRUD Page)
    public function manage() {
        $candidates = Candidate::with('position')->get();
        $positions = Position::all();
        return view('vote.manage', compact('candidates', 'positions'));
    }

    // 4. CRUD JABATAN: Simpan Jabatan Baru
    public function storePosition(Request $request) {
    $request->validate(['name' => 'required|string|unique:positions,name|max:255']);
    Position::create(['name' => $request->name]);
    
    // Pastikan melakukan redirect kembali ke halaman utama manajemen
    return redirect()->route('vote.manage')->with('success', 'Jabatan baru berhasil ditambahkan!');
    }
    // 5. CRUD JABATAN: Hapus Jabatan
    public function destroyPosition($id) {
        Position::destroy($id);
        return redirect()->route('vote.manage')->with('success', 'Jabatan berhasil dihapus!');
    }

    // 6. KANDIDAT: Simpan Kandidat Baru
    public function storeCandidate(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'position_id' => 'required|exists:positions,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->move(public_path('images'), $imageName);
        }

        Candidate::create([
            'name' => $request->name,
            'position_id' => $request->position_id,
            'image' => 'images/' . $imageName
        ]);

        return redirect()->route('vote.manage')->with('success', 'Kandidat berhasil ditambahkan!');
    }

    // Destroy Kandidat
    public function destroyCandidate($id) {
    $candidate = Candidate::findOrFail($id);

    // Hapus file foto dari folder public/images agar tidak menumpuk di storage laptop
    if ($candidate->image && File::exists(public_path($candidate->image))) {
        File::delete(public_path($candidate->image));
    }

    // Hapus data dari database lokal
    $candidate->delete();

    return redirect()->route('vote.manage')->with('success', 'Kandidat berhasil dihapus! Pastikan untuk mereset suaranya juga di Firebase jika acara sudah berjalan.');
}
}