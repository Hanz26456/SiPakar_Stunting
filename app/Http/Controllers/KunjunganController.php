<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\Kunjungan;
use App\Services\KunjunganService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KunjunganController extends Controller
{
    public function __construct(
        private readonly KunjunganService $kunjunganService
    ) {}

    /**
     * Daftar kunjungan bulan ini.
     */
    public function index(): Response
    {
        $kunjungan = Kunjungan::with(['balita', 'kader', 'diagnosis'])
            ->bulanIni()
            ->latest('tanggal_kunjungan')
            ->paginate(15);

        return Inertia::render('Kader/Kunjungan/Index', [
            'kunjungan' => $kunjungan->through(fn($k) => [
                'id'            => $k->id,
                'tanggal'       => $k->tanggal_kunjungan->format('d M Y'),
                'nama_balita'   => $k->balita->nama,
                'balita_id'     => $k->balita_id,
                'usia_bulan'    => $k->usia_bulan,
                'berat_badan'   => $k->berat_badan,
                'tinggi_badan'  => $k->tinggi_badan,
                'zscore_tbu'    => $k->zscore_tbu,
                'status'        => $k->status_zscore_tbu,
                'warna'         => $k->warna_status,
                'sudah_diagnosa'=> $k->diagnosis !== null,
                'kader'         => $k->kader->name,
            ]),
        ]);
    }

    /**
     * Form input kunjungan untuk balita tertentu.
     */
    public function createForBalita(Balita $balita): Response
    {
        // Cek apakah bulan ini sudah ada kunjungan
        $sudahAda = $balita->kunjungan()
            ->bulanIni()
            ->exists();

        $kunjunganTerakhir = $balita->kunjunganTerbaru;

        return Inertia::render('Kader/Kunjungan/Create', [
            'balita' => [
                'id'           => $balita->id,
                'nama'         => $balita->nama,
                'usia_format'  => $balita->usia_format,
                'usia_bulan'   => $balita->usia_bulan,
                'jenis_kelamin'=> $balita->jenis_kelamin,
                'tanggal_lahir'=> $balita->tanggal_lahir->format('d M Y'),
            ],
            'kunjungan_terakhir' => $kunjunganTerakhir ? [
                'tanggal'      => $kunjunganTerakhir->tanggal_kunjungan->format('d M Y'),
                'berat_badan'  => $kunjunganTerakhir->berat_badan,
                'tinggi_badan' => $kunjunganTerakhir->tinggi_badan,
            ] : null,
            'sudah_kunjungan_bulan_ini' => $sudahAda,
            'tanggal_default'           => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Form input kunjungan umum (pilih balita dulu).
     */
    public function create(): Response
    {
        $balita = Balita::aktif()
            ->select('id', 'nama', 'tanggal_lahir', 'jenis_kelamin')
            ->orderBy('nama')
            ->get()
            ->map(fn($b) => [
                'id'          => $b->id,
                'nama'        => $b->nama,
                'usia_format' => $b->usia_format,
            ]);

        return Inertia::render('Kader/Kunjungan/Create', [
            'daftar_balita'  => $balita,
            'tanggal_default'=> now()->format('Y-m-d'),
        ]);
    }

    /**
     * Simpan data kunjungan + hitung z-score otomatis.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'balita_id'         => 'required|exists:balita,id',
            'tanggal_kunjungan' => 'required|date',
            'berat_badan'       => 'required|numeric|min:0.5|max:50',
            'tinggi_badan'      => 'required|numeric|min:30|max:150',
            'lila'              => 'nullable|numeric|min:5|max:30',
            'lingkar_kepala'    => 'nullable|numeric|min:20|max:60',
            'catatan'           => 'nullable|string|max:500',
        ]);

        $balita = Balita::findOrFail($data['balita_id']);

        // Cek duplikat kunjungan bulan yang sama
        $sudahAda = $balita->kunjungan()
            ->whereMonth('tanggal_kunjungan', now()->month)
            ->whereYear('tanggal_kunjungan', now()->year)
            ->exists();

        if ($sudahAda) {
            return back()->withErrors([
                'tanggal_kunjungan' => 'Balita ini sudah memiliki kunjungan di bulan yang sama.'
            ]);
        }

        $kunjungan = $this->kunjunganService->simpan($balita, $data);

        return redirect()
            ->route('kader.diagnosis.create', $kunjungan)
            ->with('success', 'Data kunjungan tersimpan. Silakan lanjutkan diagnosis.');
    }

    /**
     * Detail kunjungan + tren pertumbuhan untuk grafik.
     */
    public function show(Kunjungan $kunjungan): Response
    {
        $kunjungan->load(['balita', 'kader', 'diagnosis.detail.ruleCf.gejala']);
        $balita = $kunjungan->balita;

        $tren = $this->kunjunganService->trenPertumbuhan($balita);

        return Inertia::render('Kader/Kunjungan/Show', [
            'kunjungan' => [
                'id'            => $kunjungan->id,
                'tanggal'       => $kunjungan->tanggal_kunjungan->format('d M Y'),
                'berat_badan'   => $kunjungan->berat_badan,
                'tinggi_badan'  => $kunjungan->tinggi_badan,
                'lila'          => $kunjungan->lila,
                'lingkar_kepala'=> $kunjungan->lingkar_kepala,
                'zscore_tbu'    => $kunjungan->zscore_tbu,
                'zscore_bbu'    => $kunjungan->zscore_bbu,
                'usia_bulan'    => $kunjungan->usia_bulan,
                'status'        => $kunjungan->status_zscore_tbu,
                'kader'         => $kunjungan->kader->name,
            ],
            'balita' => [
                'id'           => $balita->id,
                'nama'         => $balita->nama,
                'usia_format'  => $balita->usia_format,
                'jenis_kelamin'=> $balita->jenis_kelamin_lengkap,
            ],
            'tren'      => $tren,
            'diagnosis' => $kunjungan->diagnosis ? [
                'id'           => $kunjungan->diagnosis->id,
                'cf_kombinasi' => $kunjungan->diagnosis->cf_kombinasi,
                'cf_persen'    => $kunjungan->diagnosis->cf_persen,
                'status_final' => $kunjungan->diagnosis->status_final,
                'label_status' => $kunjungan->diagnosis->label_status,
                'warna_status' => $kunjungan->diagnosis->warna_status,
            ] : null,
        ]);
    }
}
