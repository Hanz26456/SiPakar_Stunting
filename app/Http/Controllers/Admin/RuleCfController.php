<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use App\Models\RuleCf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


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