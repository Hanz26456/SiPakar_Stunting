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

// ===================================================
// RuleCfController
// ===================================================
class RuleCfController extends Controller
{
    public function index(): Response
    {
        $rules = RuleCf::with('gejala')
            ->orderBy('kode_rule')
            ->get()
            ->map(fn($r) => [
                'id'              => $r->id,
                'kode_rule'       => $r->kode_rule,
                'nama_gejala'     => $r->gejala->nama_gejala,
                'kode_gejala'     => $r->gejala->kode,
                'kategori'        => $r->gejala->kategori,
                'status_diagnosa' => $r->status_diagnosa,
                'mb'              => $r->mb,
                'md'              => $r->md,
                'cf_pakar'        => $r->getCfPakarAttribute(),
                'is_active'       => $r->is_active,
                'kondisi'         => $r->kondisi,
            ]);

        $gejala = Gejala::aktif()
            ->orderBy('kode')
            ->get(['id', 'kode', 'nama_gejala']);

        return Inertia::render('Admin/RuleCf/Index', [
            'rules'  => $rules,
            'gejala' => $gejala,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'gejala_id'       => 'required|exists:gejala,id',
            'kode_rule'       => 'required|string|max:10|unique:rule_cf',
            'status_diagnosa' => 'required|in:normal,berisiko,stunting,stunting_berat',
            'mb'              => 'required|numeric|min:0|max:1',
            'md'              => 'required|numeric|min:0|max:1',
            'kondisi'         => 'nullable|string',
        ]);

        // Validasi MB + MD tidak boleh lebih dari 1
        if ($data['mb'] + $data['md'] > 1.001) {
            return back()->withErrors([
                'mb' => 'Total MB + MD tidak boleh lebih dari 1.0'
            ]);
        }

        RuleCf::create(array_merge($data, ['is_active' => true]));

        return back()->with('success', 'Rule CF berhasil ditambahkan.');
    }

    public function update(Request $request, RuleCf $ruleCf)
    {
        $data = $request->validate([
            'mb'       => 'required|numeric|min:0|max:1',
            'md'       => 'required|numeric|min:0|max:1',
            'kondisi'  => 'nullable|string',
        ]);

        if ($data['mb'] + $data['md'] > 1.001) {
            return back()->withErrors([
                'mb' => 'Total MB + MD tidak boleh lebih dari 1.0'
            ]);
        }

        $ruleCf->update($data);
        return back()->with('success', 'Rule CF berhasil diperbarui.');
    }

    public function toggle(RuleCf $ruleCf)
    {
        $ruleCf->update(['is_active' => !$ruleCf->is_active]);
        $status = $ruleCf->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Rule {$ruleCf->kode_rule} berhasil {$status}.");
    }
}
