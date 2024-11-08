<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use App\Models\ModalDisposisi;
use App\Models\PengajuanSurat;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PengajuanSuratController extends Controller
{
    // Tampilkan halaman pengajuan surat
    public function index()
    {
        // Ambil semua proposal yang belum dihapus (soft delete)
        $proposals = Proposal::with(['pemohon', 'modalDisposisi'])
            ->where('pemohon_id', auth()->id())
            ->whereNull('deleted_at')
            ->paginate(5);

        return view('pemohon.proposals.index', compact('proposals'));
    }

    // Tampilkan form untuk membuat surat
    public function create()
    {
        return view('pemohon.proposals.create');
    }

    // Simpan pengajuan surat
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_surat' => 'required|date',
            'asal_surat' => 'required|string',
            'hal' => 'required|string',
            'soft_file' => 'nullable|mimes:pdf|max:2048',
        ]);

        // Cek apakah ada file yang di-upload
        if ($request->hasFile('soft_file')) {
            $filePath = $request->file('soft_file')->store('proposals', 'public'); // menyimpan ke folder 'storage/app/public/proposals'
        } else {
            $filePath = null; // jika tidak ada file diunggah
        }

        // Ambil tahun dan bulan dari tanggal saat ini
        $tahun = now()->year;  // Ambil tahun saat ini
        $bulan = now()->month; // Ambil bulan saat ini

        // Ambil nomor urut terakhir yang digunakan untuk kode_pengajuan
        $lastProposal = Proposal::withTrashed() // Ambil semua proposal, termasuk yang dihapus
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->latest()
            ->first();
        $increment = 1;

        if ($lastProposal) {
            // Jika ada proposal sebelumnya, ambil nomor urut terakhir dan tambahkan 1
            $lastKode = substr($lastProposal->kode_pengajuan, -4);
            $increment = (int)$lastKode + 1;
        }

        // Format kode_pengajuan (misal: P2024211001)
        $kodePengajuan = 'P' . $tahun . $bulan . str_pad($increment, 4, '0', STR_PAD_LEFT);

        $proposal = Proposal::create([
            'pemohon_id' => auth()->id(),
            'tanggal_surat' => $request->tanggal_surat,
            'asal_surat' => $request->asal_surat,
            'hal' => $request->hal,
            'kode_pengajuan' => $kodePengajuan,  // Simpan kode_pengajuan
            'jenis_proposal' => $request->jenis_proposal,
            'soft_file' => $filePath,
        ]);

        ModalDisposisi::create([
            'proposal_id' => $proposal->id,
            'tujuan' => 'Staff TU',
            'status' => 'Diproses',
            'tanggal_diterima' => null,
            'tanggal_proses' => null,
            'diverifikasi_oleh' => null,
            'keterangan' => null,
        ]);

        return redirect()->route('pemohon.proposals.index')->with('success', 'Pengajuan surat berhasil ditambahkan.');
    }

    // Hapus pengajuan surat
    public function destroy($id)
    {
        $proposal = Proposal::findOrFail($id);

        // Cek status proposal
        if ($proposal->status_disposisi === 'Selesai') {
            // Hapus proposal dengan soft delete
            $proposal->delete();
            return redirect()->route('pemohon.proposals.index')->with('success', 'Pengajuan surat berhasil di-soft delete.');
        } else {
            // Hapus proposal secara permanen
            $proposal->forceDelete();
            return redirect()->route('pemohon.proposals.index')->with('success', 'Pengajuan surat berhasil dihapus secara permanen.');
        }
    }

    // Tampilkan detail surat dalam pop-up
    public function show($id)
    {
        $proposals = Proposal::findOrFail($id);
        // Mengambil modal disposisi yang terkait dengan proposal ini berdasarkan proposal_id
        $modal = ModalDisposisi::where('proposal_id', $id)->with('proposal')->get();

        return view('pemohon.proposals.detail', compact('proposals', 'modal'));
    }
}
