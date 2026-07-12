<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\RiwayatBalita;
use App\Services\KunjunganService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BalitaController extends Controller
{
    public function __construct(
        private readonly KunjunganService $kunjunganService
    ) {}

    public function index(Request $request): Response
    {
        $balita = Balita::aktif()
            ->with(['kunjunganTerbaru.diagnosis'])
            ->when($request->search, fn($q) =>
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('nama_ibu', 'like', "%{$request->search}%")
            )
            ->when($request->status, function ($q) use ($request) {
            $q->whereHas('kunjunganTerbaru.diagnosis', function ($d) use ($request) {
                $d->where('status_stunting', $request->status);
            });
        })
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Kader/Balita/Index', [
            'balita'  => $balita->through(fn($b) => [
                'id'            => $b->id,
                'nama'          => $b->nama,
                'usia_format'   => $b->usia_format,
                'jenis_kelamin' => $b->jenis_kelamin_lengkap,
                'nama_ibu'      => $b->nama_ibu,
                'desa'          => $b->desa,
                'status_terbaru'=> $b->status_terbaru,
                'kunjungan_terakhir' => $b->kunjunganTerbaru
                    ?->tanggal_kunjungan->format('d M Y'),
            ]),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Kader/Balita/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'              => 'required|string|max:100',
            'tanggal_lahir'     => 'required|date|before:today',
            'jenis_kelamin'     => 'required|in:L,P',
            'nama_ibu'          => 'required|string|max:100',
            'nama_ayah'         => 'nullable|string|max:100',
            'no_hp_ortu'        => 'nullable|string|max:20',
            'alamat'            => 'nullable|string',
            'rt_rw'             => 'nullable|string|max:10',
            'desa'              => 'nullable|string|max:100',
            'no_kk'             => 'nullable|string|max:20',
            // Riwayat
            'asi_eksklusif'     => 'boolean',
            'mulai_mpasi'       => 'nullable|date',
            'mpasi_sesuai_usia' => 'boolean',
            'infeksi_berulang'  => 'boolean',
            'detail_penyakit'   => 'nullable|string',
            'berat_lahir'       => 'nullable|numeric|min:0.5|max:10',
            'panjang_lahir'     => 'nullable|numeric|min:20|max:70',
            'jenis_persalinan'  => 'nullable|in:normal,caesar,lainnya',
        ]);

        $balita = Balita::create([
            'nama'          => $data['nama'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'nama_ibu'      => $data['nama_ibu'],
            'nama_ayah'     => $data['nama_ayah'] ?? null,
            'no_hp_ortu'    => $data['no_hp_ortu'] ?? null,
            'alamat'        => $data['alamat'] ?? null,
            'rt_rw'         => $data['rt_rw'] ?? null,
            'desa'          => $data['desa'] ?? null,
            'no_kk'         => $data['no_kk'] ?? null,
            'kader_id'      => auth()->id(),
        ]);

        // Simpan riwayat balita
        RiwayatBalita::create([
            'balita_id'         => $balita->id,
            'asi_eksklusif'     => $data['asi_eksklusif'] ?? false,
            'mulai_mpasi'       => $data['mulai_mpasi'] ?? null,
            'mpasi_sesuai_usia' => $data['mpasi_sesuai_usia'] ?? false,
            'infeksi_berulang'  => $data['infeksi_berulang'] ?? false,
            'detail_penyakit'   => $data['detail_penyakit'] ?? null,
            'berat_lahir'       => $data['berat_lahir'] ?? null,
            'panjang_lahir'     => $data['panjang_lahir'] ?? null,
            'jenis_persalinan'  => $data['jenis_persalinan'] ?? null,
        ]);

        return redirect()
            ->route('kader.balita.show', $balita)
            ->with('success', 'Data balita berhasil disimpan.');
    }

    public function show(Balita $balita): Response
    {
        $balita->load(['riwayat', 'kunjungan.diagnosis', 'kader']);
        $tren = $this->kunjunganService->trenPertumbuhan($balita);

        return Inertia::render('Kader/Balita/Show', [
            'balita' => [
                'id'            => $balita->id,
                'nama'          => $balita->nama,
                'tanggal_lahir' => $balita->tanggal_lahir->format('d M Y'),
                'usia_format'   => $balita->usia_format,
                'usia_bulan'    => $balita->usia_bulan,
                'jenis_kelamin' => $balita->jenis_kelamin_lengkap,
                'nama_ibu'      => $balita->nama_ibu,
                'nama_ayah'     => $balita->nama_ayah,
                'no_hp_ortu'    => $balita->no_hp_ortu,
                'alamat'        => $balita->alamat,
                'desa'          => $balita->desa,
            ],
            'riwayat' => $balita->riwayat ? [
                'asi_eksklusif'     => $balita->riwayat->asi_eksklusif,
                'mulai_mpasi'       => $balita->riwayat->mulai_mpasi?->format('M Y'),
                'mpasi_sesuai_usia' => $balita->riwayat->mpasi_sesuai_usia,
                'infeksi_berulang'  => $balita->riwayat->infeksi_berulang,
                'berat_lahir'       => $balita->riwayat->berat_lahir,
                'panjang_lahir'     => $balita->riwayat->panjang_lahir,
            ] : null,
            'riwayat_kunjungan' => $balita->kunjungan->map(fn($k) => [
                'id'            => $k->id,
                'tanggal'       => $k->tanggal_kunjungan->format('d M Y'),
                'usia_bulan'    => $k->usia_bulan,
                'berat_badan'   => $k->berat_badan,
                'tinggi_badan'  => $k->tinggi_badan,
                'lila'          => $k->lila,
                'zscore_tbu'    => $k->zscore_tbu,
                'status'        => $k->status_zscore_tbu,
                'warna'         => $k->warna_status,
                'sudah_diagnosa'=> $k->diagnosis !== null,
                'diagnosis_id'  => $k->diagnosis?->id,
                'cf'            => $k->diagnosis?->cf_kombinasi,
                'status_cf'     => $k->diagnosis?->label_status,
            ]),
            'tren' => $tren,
        ]);
    }

    public function edit(Balita $balita): Response
    {
        $balita->load('riwayat');
        return Inertia::render('Kader/Balita/Edit', [
            'balita'  => $balita,
            'riwayat' => $balita->riwayat,
        ]);
    }

    public function update(Request $request, Balita $balita)
    {
        $data = $request->validate([
            'nama'          => 'required|string|max:100',
            'nama_ibu'      => 'required|string|max:100',
            'no_hp_ortu'    => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'desa'          => 'nullable|string|max:100',
        ]);

        $balita->update($data);

        return redirect()
            ->route('kader.balita.show', $balita)
            ->with('success', 'Data balita berhasil diperbarui.');
    }
}
