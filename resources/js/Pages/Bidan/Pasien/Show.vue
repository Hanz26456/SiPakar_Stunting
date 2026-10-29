<template>
  <BidanLayout title="Detail Pasien">
    <div class="max-w-5xl mx-auto space-y-5">

      <!-- Header balita -->
      <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-13 h-13 rounded-2xl bg-purple-500/10 text-orbit-primary flex items-center justify-center flex-shrink-0">
              <Baby class="w-7 h-7" />
            </div>
            <div>
              <div class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ balita.nama }}</div>
              <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                {{ balita.usia_format }} · {{ balita.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }} · Desa {{ balita.desa }}
              </div>
              <div class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
                <span>Nama Ibu: <strong class="text-slate-600 dark:text-slate-300 font-medium">{{ balita.nama_ibu }}</strong></span>
              </div>
            </div>
          </div>
          <div v-if="riwayat_kunjungan[0]?.diagnosis" class="sm:text-right flex sm:flex-col items-center sm:items-end justify-between border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100 dark:border-orbit-border">
            <StatusBadge
              :status="riwayat_kunjungan[0].diagnosis.status_final"
              :label="riwayat_kunjungan[0].diagnosis.label_status"
            />
            <div class="text-xs text-slate-400 mt-1">Status Diagnosis Terakhir</div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Kolom Kiri: Tren & Riwayat -->
        <div class="lg:col-span-2 space-y-5">
          <!-- Tren pertumbuhan -->
          <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
              <TrendingUp class="w-4 h-4 text-orbit-primary" />
              <span>Tren Pertumbuhan Z-Score</span>
            </div>
            <div class="space-y-3.5">
              <div v-for="t in tren" :key="t.bulan" class="flex items-center gap-3">
                <div class="text-xs text-slate-500 dark:text-slate-400 w-16 flex-shrink-0 font-medium">{{ t.bulan }}</div>
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <div class="flex-1 h-2 bg-slate-100 dark:bg-orbit-surface2 rounded-full overflow-hidden">
                      <div
                        class="h-full rounded-full transition-all"
                        :class="zBarColor(t.zscore_tbu)"
                        :style="{ width: Math.min(100, Math.max(5, (t.zscore_tbu + 3) / 6 * 100)) + '%' }"
                      ></div>
                    </div>
                    <span class="text-xs font-mono font-semibold w-16 text-right flex-shrink-0" :class="zColor(t.zscore_tbu)">
                      {{ t.zscore_tbu != null ? t.zscore_tbu.toFixed(1) : '-' }} SD
                    </span>
                  </div>
                  <div class="flex gap-4 text-xs text-slate-500 dark:text-slate-400">
                    <span>BB: <strong class="text-slate-700 dark:text-slate-200">{{ t.berat_badan }} kg</strong></span>
                    <span>TB: <strong class="text-slate-700 dark:text-slate-200">{{ t.tinggi_badan }} cm</strong></span>
                    <span v-if="t.lila">LiLA: <strong class="text-slate-700 dark:text-slate-200">{{ t.lila }} cm</strong></span>
                  </div>
                </div>
                <div class="text-xs flex-shrink-0">
                  <span
                    class="px-2.5 py-1 rounded-full text-[11px] font-semibold"
                    :class="{
                      'bg-rose-500/10 text-rose-500 border border-rose-500/20': t.status === 'Stunting berat' || t.status === 'Stunting',
                      'bg-amber-500/10 text-amber-500 border border-amber-500/20': t.status === 'Berisiko',
                      'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20': t.status === 'Normal',
                    }"
                  >{{ t.status }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Timeline riwayat -->
          <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
              <Calendar class="w-4 h-4 text-orbit-primary" />
              <span>Riwayat Kunjungan & Diagnosis</span>
            </div>
            <div class="space-y-4">
              <div v-for="k in riwayat_kunjungan" :key="k.id" class="flex gap-3">
                <div class="flex flex-col items-center">
                  <div
                    class="w-3.5 h-3.5 rounded-full border-2 flex-shrink-0 mt-1"
                    :class="{
                      'bg-rose-500/20 border-rose-500': k.warna === 'red',
                      'bg-amber-500/20 border-amber-500': k.warna === 'amber',
                      'bg-emerald-500/20 border-emerald-500': k.warna === 'green',
                    }"
                  ></div>
                  <div class="flex-1 w-px bg-slate-200 dark:bg-orbit-border mt-1"></div>
                </div>
                <div class="flex-1 pb-4">
                  <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ k.tanggal }}</span>
                    <span class="text-xs text-slate-400">Usia {{ k.usia_bulan }} bulan</span>
                    <span v-if="k.diagnosis?.verified" class="inline-flex items-center gap-1 text-[11px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 px-2 py-0.5 rounded-full border border-emerald-500/20">
                      <CheckCircle2 class="w-3 h-3" /> Terverifikasi
                    </span>
                  </div>
                  <div class="text-xs text-slate-500 dark:text-slate-400 mb-2 font-mono">
                    BB: {{ k.berat_badan }} kg · TB: {{ k.tinggi_badan }} cm · Z-score: {{ k.zscore_tbu?.toFixed(2) }} SD
                  </div>
                  <div v-if="k.diagnosis" class="flex flex-wrap items-center gap-2">
                    <StatusBadge :status="k.diagnosis.status_final" :label="k.diagnosis.label_status" />
                    <span class="text-xs font-mono font-medium text-slate-500 dark:text-slate-400">CF: {{ k.diagnosis.cf_persen }}</span>
                    <span v-if="k.diagnosis.tindak_lanjut" class="text-xs text-slate-400">
                      · {{ tindakLanjulLabel(k.diagnosis.tindak_lanjut) }}
                    </span>
                  </div>
                  <div v-else class="text-xs text-slate-400 italic">Belum ada diagnosis</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Kolom kanan: Riwayat medis & Tindakan -->
        <div class="space-y-5">
          <!-- Riwayat Medis -->
          <div v-if="riwayat" class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
              <ClipboardList class="w-4 h-4 text-orbit-primary" />
              <span>Riwayat Medis Balita</span>
            </div>
            <div class="space-y-3 text-xs sm:text-sm">
              <div class="flex items-center justify-between py-1 border-b border-slate-100 dark:border-orbit-border/60">
                <span class="text-slate-500 dark:text-slate-400">ASI Eksklusif</span>
                <span v-if="riwayat.asi_eksklusif" class="inline-flex items-center gap-1 font-semibold text-emerald-600 dark:text-emerald-400">
                  <Check class="w-3.5 h-3.5" /> Ya
                </span>
                <span v-else class="inline-flex items-center gap-1 font-semibold text-rose-500">
                  <X class="w-3.5 h-3.5" /> Tidak
                </span>
              </div>
              <div class="flex items-center justify-between py-1 border-b border-slate-100 dark:border-orbit-border/60">
                <span class="text-slate-500 dark:text-slate-400">MPASI Sesuai Usia</span>
                <span v-if="riwayat.mpasi_sesuai_usia" class="inline-flex items-center gap-1 font-semibold text-emerald-600 dark:text-emerald-400">
                  <Check class="w-3.5 h-3.5" /> Ya
                </span>
                <span v-else class="inline-flex items-center gap-1 font-semibold text-rose-500">
                  <X class="w-3.5 h-3.5" /> Tidak
                </span>
              </div>
              <div class="flex items-center justify-between py-1 border-b border-slate-100 dark:border-orbit-border/60">
                <span class="text-slate-500 dark:text-slate-400">Infeksi Berulang</span>
                <span v-if="riwayat.infeksi_berulang" class="inline-flex items-center gap-1 font-semibold text-amber-500">
                  <AlertTriangle class="w-3.5 h-3.5" /> Ya
                </span>
                <span v-else class="inline-flex items-center gap-1 font-semibold text-emerald-600 dark:text-emerald-400">
                  <Check class="w-3.5 h-3.5" /> Tidak
                </span>
              </div>
              <div class="flex items-center justify-between py-1 border-b border-slate-100 dark:border-orbit-border/60" v-if="riwayat.berat_lahir">
                <span class="text-slate-500 dark:text-slate-400">Berat Lahir</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ riwayat.berat_lahir }} kg</span>
              </div>
              <div class="flex items-center justify-between py-1" v-if="riwayat.mulai_mpasi">
                <span class="text-slate-500 dark:text-slate-400">Mulai MPASI</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ riwayat.mulai_mpasi }}</span>
              </div>
            </div>
          </div>

          <!-- Kontak orang tua -->
          <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200 mb-3">
              <Phone class="w-4 h-4 text-orbit-primary" />
              <span>Kontak Orang Tua</span>
            </div>
            <div class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ balita.nama_ibu }}</div>
            <div v-if="balita.no_hp_ortu" class="text-xs sm:text-sm text-orbit-primary font-mono mt-1">
              {{ balita.no_hp_ortu }}
            </div>
            <div v-else class="text-xs text-slate-400 mt-1 italic">Nomor HP belum tersimpan</div>
          </div>

          <!-- Aksi cepat -->
          <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs space-y-3">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">
              <Sparkles class="w-4 h-4 text-orbit-primary" />
              <span>Aksi Cepat</span>
            </div>
            <Link
              v-if="riwayat_kunjungan[0]?.diagnosis && !riwayat_kunjungan[0].diagnosis.verified"
              :href="route('bidan.verifikasi.show', riwayat_kunjungan[0].diagnosis.id)"
              class="w-full py-2.5 px-4 text-xs sm:text-sm font-semibold bg-orbit-primary hover:bg-orbit-primary/90 text-white rounded-xl shadow-md shadow-purple-600/20 transition-all flex items-center justify-center gap-2"
            >
              <span>Verifikasi Diagnosis Terbaru</span>
              <ArrowRight class="w-4 h-4" />
            </Link>
            <button
              @click="cetakLaporan"
              class="w-full py-2.5 px-4 text-xs sm:text-sm font-medium border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface2 hover:bg-slate-50 dark:hover:bg-white/5 text-slate-700 dark:text-slate-200 rounded-xl transition-all flex items-center justify-center gap-2"
            >
              <Printer class="w-4 h-4 text-slate-400" />
              <span>Cetak Riwayat Pasien</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </BidanLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import {
  Baby,
  TrendingUp,
  Calendar,
  ClipboardList,
  Phone,
  Sparkles,
  Printer,
  Check,
  X,
  AlertTriangle,
  CheckCircle2,
  ArrowRight,
} from 'lucide-vue-next'
import BidanLayout from '@/Layouts/BidanLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

defineProps({
  balita: Object,
  riwayat: Object,
  riwayat_kunjungan: Array,
  tren: Array,
})

const tindakLanjulLabel = (t) => ({
  pantau: 'Pantau',
  edukasi_gizi: 'Edukasi Gizi',
  pmt: 'PMT Pemulihan',
  rujuk_puskesmas: 'Rujuk Puskesmas',
  rujuk_rsud: 'Rujuk RSUD',
}[t] ?? '-')

const cetakLaporan = () => window.print()

const zColor = (z) => {
  if (z == null) return 'text-slate-400'
  if (z < -2) return 'text-rose-500'
  if (z < -1) return 'text-amber-500'
  return 'text-emerald-500'
}

const zBarColor = (z) => {
  if (z == null) return 'bg-slate-300 dark:bg-slate-700'
  if (z < -2) return 'bg-rose-500'
  if (z < -1) return 'bg-amber-500'
  return 'bg-emerald-500'
}
</script>
