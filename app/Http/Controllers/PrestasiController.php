<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestasi;

class PrestasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Prestasi::query();

        // Pencarian berdasarkan input "search"
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('jenis_prestasi', 'like', "%{$search}%")
                  ->orWhere('kategori_prestasi', 'like', "%{$search}%")
                  ->orWhere('poin', 'like', "%{$search}%");
            });
        }

        // Pagination berdasarkan jumlah "entries" yang dipilih
        $entries = $request->input('entries', 10); // Default 10
        $achievements = $query->paginate($entries)->appends($request->query());

        // Mengarahkan ke view kelola-prestasi/index.blade.php
        return view('kelola-prestasi.index', compact('achievements'));
    }

    public function laporan(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        // Ambil data prestasi berdasarkan tanggal dengan pagination
        $prestasi = Prestasi::whereDate('created_at', $tanggal)->paginate(10);

        return view('kelola-prestasi.laporan-prestasi', compact('prestasi', 'tanggal'));
    }

    public function create()
    {
        // Mengarahkan ke view kelola-prestasi/create.blade.php
        return view('kelola-prestasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_prestasi' => 'required|string',
            'kategori_prestasi' => 'required|string',
            'poin' => 'required|integer',
        ]);

        Prestasi::create($request->all());
        return redirect()->route('prestasi.kelola')->with('success', 'Data prestasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $prestasi = Prestasi::findOrFail($id);
        // Mengarahkan ke view kelola-prestasi/edit.blade.php
        return view('kelola-prestasi.edit', compact('prestasi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_prestasi' => 'required|string',
            'kategori_prestasi' => 'required|string',
            'poin' => 'required|integer',
        ]);

        $prestasi = Prestasi::findOrFail($id);

        // Update data hanya untuk kolom yang relevan
        $data = $request->only(['jenis_prestasi', 'kategori_prestasi', 'poin']);
        $prestasi->update($data);

        return redirect()->route('prestasi.kelola')->with('success', 'Data prestasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $prestasi = Prestasi::findOrFail($id);
        $prestasi->delete();
        return redirect()->route('prestasi.kelola')->with('success', 'Data prestasi berhasil dihapus.');
    }

    public function updateStatus($id, $status)
    {
        // Validasi status yang diperbolehkan
        $allowedStatuses = ['menunggu', 'proses', 'selesai', 'tidak-valid'];
        if (!in_array($status, $allowedStatuses)) {
            return redirect()->route('prestasi.laporan')->with('error', 'Status tidak valid.');
        }

        $prestasi = Prestasi::findOrFail($id);
        $prestasi->status = $status;
        $prestasi->save();

        return redirect()->route('prestasi.laporan')->with('success', 'Status prestasi berhasil diperbarui.');
    }
}
