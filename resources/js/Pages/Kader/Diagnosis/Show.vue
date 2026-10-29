<template>
  <KaderLayout title="Hasil Diagnosis Stunting">
    <div class="max-w-4xl mx-auto space-y-5">

      <!-- Header hasil diagnosis -->
      <div
        class="rounded-2xl p-5 border shadow-2xs transition-all"
        :class="{
          'bg-rose-500/10 border-rose-500/30 text-rose-950 dark:text-rose-100': diagnosis.status_final === 'stunting_berat' || diagnosis.status_final === 'stunting',
          'bg-amber-500/10 border-amber-500/30 text-amber-950 dark:text-amber-100': diagnosis.status_final === 'berisiko',
          'bg-emerald-500/10 border-emerald-500/30 text-emerald-950 dark:text-emerald-100': diagnosis.status_final === 'normal',
        }"
      >
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <div class="text-lg sm:text-xl font-bold mb-1.5">
              {{ balita.nama }} — {{ kunjungan.tanggal }}
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
              <StatusBadge :status="diagnosis.status_final" :label="diagnosis.label_status" />
              <span class="text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                Tingkat Keyakinan (CF): <strong class="font-mono">{{ diagnosis.cf_persen }}</strong>
              </span>
              <span v-if="diagnosis.sudah_diverifikasi" class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 dark:text-blue-400 bg-blue-500/10 border border-blue-500/20 px-2.5 py-0.5 rounded-full">
                <CheckCircle2 class="w-3.5 h-3.5" />
                <span>Diverifikasi oleh {{ diagnosis.verifikator }}</span>
              </span>
              <span v-else class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2.5 py-0.5 rounded-full">
                <Clock class="w-3.5 h-3.5" />
                <span>Menunggu Verifikasi Bidan</span>
              </span>
            </div>
          </div>
          <div class="text-4xl sm:text-5xl font-mono font-extrabold" :class="cfColor(diagnosis.cf_kombinasi)">
            {{ diagnosis.cf_persen }}
          </div>
        </div>

        <!-- Rekomendasi -->
        <div class="mt-4 p-3.5 bg-white/70 dark:bg-orbit-surface/80 rounded-xl border border-slate-200/50 dark:border-orbit-border backdrop-blur-xs">
          <div class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
            Rekomendasi Penanganan
          </div>
          <div class="text-xs sm:text-sm text-slate-700 dark:text-slate-200 leading-relaxed font-medium">
            {{ diagnosis.rekomendasi }}
          </div>
        </div>

        <!-- Tindak lanjut (kalau sudah diverifikasi) -->
        <div v-if="diagnosis.tindak_lanjut" class="mt-3 flex flex-wrap items-center gap-3 text-xs sm:text-sm font-medium text-slate-700 dark:text-slate-300">
          <span class="inline-flex items-center gap-1.5">
            <ClipboardList class="w-4 h-4 text-orbit-primary" />
            <span>Tindak Lanjut Medis: <strong>{{ tindakLanjutLabel }}</strong></span>
          </span>
          <span v-if="diagnosis.jadwal_kontrol" class="inline-flex items-center gap-1 text-slate-500 dark:text-slate-400">
            · Jadwal Kontrol: <strong class="text-slate-700 dark:text-slate-200">{{ diagnosis.jadwal_kontrol }}</strong>
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Data pengukuran antropometri -->
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
          <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200 mb-3.5">
            <Ruler class="w-4 h-4 text-orbit-primary" />
            <span>Data Pengukuran Antropometri</span>
          </div>
          <div class="space-y-2.5 text-xs sm:text-sm">
            <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
              <span class="text-slate-400">Berat Badan</span>
              <span class="font-mono font-semibold text-slate-700 dark:text-slate-200">{{ kunjungan.berat_badan }} kg</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
              <span class="text-slate-400">Tinggi / Panjang Badan</span>
              <span class="font-mono font-semibold text-slate-700 dark:text-slate-200">{{ kunjungan.tinggi_badan }} cm</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
              <span class="text-slate-400">Lingkar Lengan Atas (LiLA)</span>
              <span class="font-mono font-semibold text-slate-700 dark:text-slate-200">{{ kunjungan.lila ?? '-' }} cm</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
              <span class="text-slate-400">Z-score TB/U (WHO)</span>
              <span class="font-mono font-bold" :class="zscoreColor(kunjungan.zscore_tbu)">
                {{ kunjungan.zscore_tbu != null ? kunjungan.zscore_tbu.toFixed(2) : '-' }} SD
              </span>
            </div>
            <div class="flex justify-between items-center py-1">
              <span class="text-slate-400">Z-score BB/U (WHO)</span>
              <span class="font-mono font-semibold text-slate-700 dark:text-slate-200">
                {{ kunjungan.zscore_bbu != null ? kunjungan.zscore_bbu.toFixed(2) : '-' }} SD
              </span>
            </div>
          </div>
        </div>

        <!-- Catatan bidan (kalau ada) -->
        <div v-if="diagnosis.catatan_bidan" class="bg-blue-500/10 rounded-2xl border border-blue-500/20 p-5 shadow-2xs">
          <div class="flex items-center gap-2 text-sm font-bold text-blue-600 dark:text-blue-400 mb-2">
            <Stethoscope class="w-4 h-4" />
            <span>Catatan Klinis Bidan</span>
          </div>
          <div class="text-xs sm:text-sm text-slate-700 dark:text-slate-200 leading-relaxed">
            {{ diagnosis.catatan_bidan }}
          </div>
          <div v-if="diagnosis.alasan_override" class="mt-3 pt-3 border-t border-blue-500/20 text-xs text-blue-600 dark:text-blue-300 font-medium">
            Alasan Koreksi Status: {{ diagnosis.alasan_override }}
          </div>
        </div>
        <div v-else class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs flex flex-col justify-center items-center text-center">
          <Clock class="w-8 h-8 text-slate-300 dark:text-slate-600 mb-2" />
          <div class="text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300">Menunggu Review Bidan</div>
          <div class="text-xs text-slate-400 max-w-xs mt-1">Catatan medis dan instruksi rujukan bidan akan tampil setelah diverifikasi.</div>
        </div>

        <!-- Detail CF -->
        <div class="md:col-span-2 bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
          <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200 mb-3.5">
            <Cpu class="w-4 h-4 text-orbit-primary" />
            <span>Detail Perhitungan Certainty Factor (CF)</span>
          </div>
          <div class="space-y-2">
            <div
              v-for="d in detail_gejala" :key="d.kode_rule"
              class="flex items-center justify-between gap-3 p-2.5 rounded-xl border border-slate-100 dark:border-orbit-border/60 transition-colors"
              :class="d.gejala_aktif ? 'bg-slate-50/70 dark:bg-orbit-surface2/50 opacity-100' : 'opacity-40'"
            >
              <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-10 text-xs font-mono font-bold text-slate-400 flex-shrink-0">{{ d.kode_rule }}</span>
                <span class="text-xs sm:text-sm text-slate-700 dark:text-slate-200 truncate">{{ d.nama_gejala }}</span>
              </div>
              <div class="flex items-center gap-2 sm:gap-3 text-xs flex-shrink-0 font-mono">
                <span class="text-[11px] px-2 py-0.5 rounded-md bg-slate-200/60 dark:bg-orbit-border text-slate-600 dark:text-slate-300 font-sans">
                  {{ d.sumber }}
                </span>
                <span class="text-blue-500 font-medium hidden sm:inline">MB: {{ d.mb }}</span>
                <span class="text-amber-500 font-medium hidden sm:inline">MD: {{ d.md }}</span>
                <span class="font-bold w-16 text-right" :class="d.gejala_aktif ? 'text-rose-500' : 'text-slate-400'">
                  CF: {{ d.cf_pakar.toFixed(2) }}
                </span>
                <div class="w-6 flex items-center justify-center">
                  <Check v-if="d.gejala_aktif" class="w-4 h-4 text-emerald-500 stroke-[2.5]" />
                  <X v-else class="w-4 h-4 text-slate-400 stroke-[2]" />
                </div>
              </div>
            </div>
          </div>
          <div class="mt-4 pt-3.5 border-t border-slate-200 dark:border-orbit-border flex justify-between items-center">
            <div class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-300">Nilai Kombinasi Akhir (CF Total)</div>
            <div class="text-lg sm:text-xl font-mono font-extrabold" :class="cfColor(diagnosis.cf_kombinasi)">
              {{ diagnosis.cf_persen }}
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
        <Link
          :href="route('kader.balita.show', balita.id)"
          class="px-4 py-2.5 text-xs sm:text-sm font-medium border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface hover:bg-slate-50 dark:hover:bg-white/5 text-slate-700 dark:text-slate-200 rounded-xl transition-all flex items-center justify-center gap-1.5"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Lihat Profil Balita</span>
        </Link>
        <div class="flex items-center gap-2.5">
          <button
            @click="cetak"
            type="button"
            class="px-4 py-2.5 text-xs sm:text-sm font-medium border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface hover:bg-slate-50 dark:hover:bg-white/5 text-slate-700 dark:text-slate-200 rounded-xl transition-all flex items-center justify-center gap-2"
          >
            <Printer class="w-4 h-4 text-slate-400" />
            <span>Cetak Hasil</span>
          </button>
          <Link
            :href="route('kader.kunjungan.create')"
            class="px-5 py-2.5 text-xs sm:text-sm font-semibold bg-orbit-primary hover:bg-orbit-primary/90 text-white rounded-xl shadow-md shadow-purple-600/20 transition-all flex items-center justify-center gap-2 active:scale-[0.98]"
          >
            <Plus class="w-4 h-4" />
            <span>Input Kunjungan Baru</span>
          </Link>
        </div>
      </div>
    </div>
  </KaderLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
  CheckCircle2,
  Clock,
  ClipboardList,
  Ruler,
  Stethoscope,
  Cpu,
  Check,
  X,
  ArrowLeft,
  Printer,
  Plus
} from 'lucide-vue-next'
import KaderLayout from '@/Layouts/KaderLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

const props = defineProps({
  diagnosis: Object,
  detail_gejala: Array,
  balita: Object,
  kunjungan: Object,
})

const tindakLanjutLabel = computed(() => ({
  pantau:           'Pantau Ketat',
  edukasi_gizi:     'Edukasi Gizi',
  pmt:              'Pemberian PMT Pemulihan',
  rujuk_puskesmas:  'Rujuk Puskesmas',
  rujuk_rsud:       'Rujuk RSUD',
}[props.diagnosis.tindak_lanjut] ?? '-'))

const cfColor = (cf) => {
  if (cf >= 0.7) return 'text-rose-500'
  if (cf >= 0.4) return 'text-amber-500'
  return 'text-emerald-500'
}

const zscoreColor = (z) => {
  if (z == null) return 'text-slate-400'
  if (z < -2) return 'text-rose-500'
  if (z < -1) return 'text-amber-500'
  return 'text-emerald-500'
}

const cetak = () => {
  window.print()
}
</script>
