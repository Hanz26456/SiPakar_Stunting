<template>
  <OrtuLayout title="Tumbuh Kembang">
    <div class="px-4 py-4 space-y-4 max-w-xl mx-auto">

      <!-- Header -->
      <div class="flex items-center gap-3">
        <Link
          :href="route('ortu.dashboard')"
          class="w-9 h-9 rounded-xl border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface flex items-center justify-center text-slate-500 hover:text-orbit-primary transition-colors shadow-2xs"
        >
          <ArrowLeft class="w-4 h-4" />
        </Link>
        <div>
          <div class="font-bold text-slate-800 dark:text-slate-100 text-base">{{ balita.nama }}</div>
          <div class="text-xs text-slate-400">{{ balita.usia_format }} · {{ balita.jenis_kelamin }}</div>
        </div>
      </div>

      <!-- Status terbaru -->
      <div
        v-if="status"
        class="rounded-2xl p-4.5 border shadow-2xs transition-all"
        :class="{
          'bg-rose-500/10 border-rose-500/30 text-rose-950 dark:text-rose-100': ['stunting','stunting_berat'].includes(status.status_stunting ?? ''),
          'bg-amber-500/10 border-amber-500/30 text-amber-950 dark:text-amber-100': status.label_status === 'Berisiko',
          'bg-emerald-500/10 border-emerald-500/30 text-emerald-950 dark:text-emerald-100': status.label_status === 'Normal',
        }"
      >
        <div class="flex flex-wrap items-center gap-2 mb-1.5">
          <span
            class="text-base sm:text-lg font-bold"
            :class="{
              'text-rose-500':   ['Stunting','Stunting Berat'].includes(status.label_status),
              'text-amber-500': status.label_status === 'Berisiko',
              'text-emerald-500': status.label_status === 'Normal',
            }"
          >{{ status.label_status }}</span>
          <span class="text-xs font-mono text-slate-500 dark:text-slate-400">CF: {{ status.cf_persen }}</span>
          <span v-if="status.verified" class="inline-flex items-center gap-1 text-[11px] font-semibold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 px-2 py-0.5 rounded-full">
            <CheckCircle2 class="w-3 h-3" />
            <span>Terverifikasi Bidan</span>
          </span>
        </div>
        <div v-if="status.rekomendasi" class="text-xs sm:text-sm text-slate-700 dark:text-slate-200 mt-2 bg-white/70 dark:bg-orbit-surface/80 p-3 rounded-xl border border-slate-200/40 dark:border-orbit-border leading-relaxed font-medium">
          {{ status.rekomendasi }}
        </div>
        <div v-if="status.jadwal_kontrol" class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-300 mt-2.5 font-medium">
          <Calendar class="w-3.5 h-3.5 text-orbit-primary" />
          <span>Jadwal Kontrol Berikutnya: <strong class="text-slate-800 dark:text-slate-100">{{ status.jadwal_kontrol }}</strong></span>
        </div>
      </div>

      <!-- Pengukuran terbaru -->
      <div v-if="pengukuran_terbaru" class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-4.5 shadow-2xs">
        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
          Pengukuran Terakhir — {{ pengukuran_terbaru.tanggal }}
        </div>
        <div class="grid grid-cols-3 gap-3">
          <div class="text-center p-2.5 rounded-xl bg-slate-50 dark:bg-orbit-surface2/50 border border-slate-100 dark:border-orbit-border/50">
            <div class="text-lg sm:text-xl font-mono font-bold text-slate-800 dark:text-slate-100">{{ pengukuran_terbaru.berat_badan }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Berat Badan (kg)</div>
          </div>
          <div class="text-center p-2.5 rounded-xl bg-slate-50 dark:bg-orbit-surface2/50 border border-slate-100 dark:border-orbit-border/50">
            <div class="text-lg sm:text-xl font-mono font-bold text-slate-800 dark:text-slate-100">{{ pengukuran_terbaru.tinggi_badan }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Tinggi Badan (cm)</div>
          </div>
          <div class="text-center p-2.5 rounded-xl bg-slate-50 dark:bg-orbit-surface2/50 border border-slate-100 dark:border-orbit-border/50">
            <div
              class="text-lg sm:text-xl font-mono font-bold"
              :class="pengukuran_terbaru.zscore_tbu < -2 ? 'text-rose-500' : pengukuran_terbaru.zscore_tbu < -1 ? 'text-amber-500' : 'text-emerald-500'"
            >
              {{ pengukuran_terbaru.zscore_tbu != null ? pengukuran_terbaru.zscore_tbu.toFixed(1) : '-' }}
            </div>
            <div class="text-[11px] text-slate-400 mt-0.5">Z-Score TB/U</div>
          </div>
        </div>
      </div>

      <!-- Tren pertumbuhan -->
      <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-4.5 shadow-2xs">
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400 mb-3.5">
          <TrendingUp class="w-4 h-4 text-orbit-primary" />
          <span>Riwayat Tren Pertumbuhan</span>
        </div>
        <div class="space-y-3">
          <div v-for="t in tren" :key="t.bulan" class="flex items-center gap-3">
            <div class="text-xs text-slate-400 w-14 flex-shrink-0 font-medium">{{ t.bulan.split(' ')[0] }}</div>
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <div class="flex-1 h-2 bg-slate-100 dark:bg-orbit-surface2 rounded-full overflow-hidden">
                  <div
                    class="h-full rounded-full transition-all"
                    :class="t.zscore_tbu < -2 ? 'bg-rose-500' : t.zscore_tbu < -1 ? 'bg-amber-500' : 'bg-emerald-500'"
                    :style="{ width: Math.min(100, Math.max(5, (t.zscore_tbu + 3) / 6 * 100)) + '%' }"
                  ></div>
                </div>
                <span
                  class="text-xs font-mono font-semibold w-14 text-right flex-shrink-0"
                  :class="t.zscore_tbu < -2 ? 'text-rose-500' : t.zscore_tbu < -1 ? 'text-amber-500' : 'text-emerald-500'"
                >
                  {{ t.zscore_tbu != null ? t.zscore_tbu.toFixed(1) : '-' }} SD
                </span>
              </div>
              <div class="text-xs text-slate-400 font-mono">{{ t.berat_badan }} kg · {{ t.tinggi_badan }} cm</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panduan Gizi -->
      <Link
        :href="route('ortu.panduan')"
        class="block bg-orbit-primary hover:bg-orbit-primary/90 text-white rounded-2xl p-4.5 text-center shadow-lg shadow-purple-600/20 transition-all active:scale-[0.99]"
      >
        <div class="flex items-center justify-center gap-2 text-sm sm:text-base font-bold mb-0.5">
          <Lightbulb class="w-4 h-4 text-purple-200" />
          <span>Panduan Gizi & Pola Asuh Balita</span>
        </div>
        <div class="text-xs text-purple-200">Tips asupan nutrisi, pola makan seimbang, dan stimulasi tumbuh kembang</div>
      </Link>
    </div>
  </OrtuLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import {
  ArrowLeft,
  CheckCircle2,
  Calendar,
  TrendingUp,
  Lightbulb
} from 'lucide-vue-next'
import OrtuLayout from '@/Layouts/OrtuLayout.vue'

defineProps({
  balita: Object,
  status: Object,
  pengukuran_terbaru: Object,
  tren: Array,
})
</script>
