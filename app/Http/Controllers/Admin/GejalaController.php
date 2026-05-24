<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use App\Models\RuleCf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

// ===================================================
// GejalaController
// ===================================================
class GejalaController extends Controller
{
    public function index(): Response
    {
        $gejala = Gejala::withCount('rules')
            ->orderBy('kode')
            ->get()
            ->map(fn($g) => [
                'id'              => $g->id,
                'kode'            => $g->kode,
                'nama_gejala'     => $g->nama_gejala,
                'kategori'        => $g->kategori,
                'sumber'          => $g->sumber,
                'kolom_sumber'    => $g->kolom_sumber,
                'operator'        => $g->operator,
                'nilai_threshold' => $g->nilai_threshold,
                'is_active'       => $g->is_active,
                'jumlah_rule'     => $g->rules_count,
            ]);

        return Inertia::render('Admin/Gejala/Index', [
            'gejala' => $gejala,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode'            => 'required|string|max:10|unique:gejala',
            'nama_gejala'     => 'required|string|max:255',
            'kategori'        => 'required|in:antropometri,pertumbuhan,klinis,riwayat_gizi,riwayat_penyakit',
            'deskripsi'       => 'nullable|string',
            'sumber'          => 'required|in:manual,otomatis',
            'kolom_sumber'    => 'nullable|string|max:50',
            'operator'        => 'nullable|string|max:20',
            'nilai_threshold' => 'nullable|numeric',
        ]);

        Gejala::create(array_merge($data, ['is_active' => true]));

        return back()->with('success', 'Gejala berhasil ditambahkan.');
    }

    public function update(Request $request, Gejala $gejala)
    {
        $data = $request->validate([
            'nama_gejala'     => 'required|string|max:255',
            'kategori'        => 'required|in:antropometri,pertumbuhan,klinis,riwayat_gizi,riwayat_penyakit',
            'deskripsi'       => 'nullable|string',
            'nilai_threshold' => 'nullable|numeric',
        ]);

        $gejala->update($data);
        return back()->with('success', 'Gejala berhasil diperbarui.');
    }

    public function destroy(Gejala $gejala)
    {
        // Jangan hapus kalau masih ada rule yang pakai
        abort_if(
            $gejala->rules()->exists(),
            422,
            'Gejala ini masih digunakan oleh rule CF. Nonaktifkan terlebih dahulu.'
        );

        $gejala->delete();
        return back()->with('success', 'Gejala berhasil dihapus.');
    }
}

