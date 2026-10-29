<template>
  <BidanLayout title="Laporan & Rekap Medis">
    <div class="space-y-5">

      <!-- Filter Periode Bar -->
      <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-4 sm:p-5 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 shadow-2xs">
        <div class="flex items-center gap-3">
          <Calendar class="w-4 h-4 text-purple-600 dark:text-purple-400" />
          <span class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">Periode Pemeriksaan:</span>
          <select
            v-model="filterBulan"
            class="bg-slate-50 dark:bg-orbit-surface2 border border-slate-200 dark:border-orbit-border rounded-xl px-3.5 py-2 text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all"
          >
            <option v-for="b in bulanOptions" :key="b.value" :value="b.value">{{ b.label }}</option>
          </select>
          <button
            @click="loadLaporan"
            type="button"
            class="px-4 py-2 text-xs sm:text-sm font-semibold bg-orbit-primary hover:bg-orbit-primary/90 text-white rounded-xl shadow-md shadow-purple-600/20 transition-all active:scale-[0.98]"
          >
            Tampilkan
          </button>
        </div>

        <div>
          <a
            :href="route('bidan.laporan.export', { bulan: filterBulan, tahun: filterTahun })"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-white/5 rounded-xl shadow-2xs transition-colors"
          >
            <Download class="w-4 h-4 text-purple-600 dark:text-purple-400" />
            <span>Ekspor PDF Resmi</span>
          </a>
        </div>
      </div>

      <!-- Ringkasan Statistik -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-4 text-center shadow-2xs">
          <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ ringkasan.total }}</div>
          <div class="text-xs text-slate-400 mt-1">Total Pemeriksaan</div>
        </div>
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-4 text-center shadow-2xs">
          <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ ringkasan.normal }}</div>
          <div class="text-xs text-slate-400 mt-1">Gizi Normal</div>
        </div>
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-4 text-center shadow-2xs">
          <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ ringkasan.berisiko }}</div>
          <div class="text-xs text-slate-400 mt-1">Berisiko Stunting</div>
        </div>
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-4 text-center shadow-2xs">
          <div class="text-2xl sm:text-3xl font-extrabold text-rose-600 dark:text-rose-400">{{ ringkasan.stunting }}</div>
          <div class="text-xs text-slate-400 mt-1">Terdiagnosis Stunting</div>
        </div>
      </div>

      <!-- Tabel laporan -->
      <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead>
              <tr class="border-b border-slate-100 dark:border-orbit-border/80 bg-slate-50/80 dark:bg-orbit-surface2/50 text-slate-500 dark:text-slate-400 text-[11px] uppercase tracking-wider font-semibold">
                <th class="py-3 px-4">Nama Balita</th>
                <th class="py-3 px-4">Usia</th>
                <th class="py-3 px-4">Tanggal Periksa</th>
                <th class="py-3 px-4">Hasil Sistem</th>
                <th class="py-3 px-4">Keyakinan (CF)</th>
                <th class="py-3 px-4">Status Akhir</th>
                <th class="py-3 px-4">Verifikasi Bidan</th>
                <th class="py-3 px-4">Intervensi Medis</th>
                <th class="py-3 px-4">Kontrol Ulang</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-orbit-border/60">
              <tr v-if="laporan.length === 0">
                <td colspan="9" class="py-14 text-center text-slate-500 dark:text-slate-400">
                  Belum ada rekap data diagnosis untuk periode bulan ini.
                </td>
              </tr>
              <tr
                v-for="d in laporan" :key="d.id"
                class="hover:bg-slate-50/80 dark:hover:bg-white/[0.02] transition-colors"
              >
                <td class="py-3.5 px-4 font-semibold text-slate-900 dark:text-white">{{ d.nama_balita }}</td>
                <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 text-xs">{{ d.usia_format }}</td>
                <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 text-xs">{{ d.tanggal }}</td>
                <td class="py-3.5 px-4">
                  <StatusBadge :status="d.status_sistem" :label="d.label_status" />
                </td>
                <td class="py-3.5 px-4 font-mono font-bold text-xs" :class="cfColor(d.cf_kombinasi)">
                  {{ d.cf_persen }}
                </td>
                <td class="py-3.5 px-4">
                  <StatusBadge :status="d.status_final" :label="d.label_status" />
                </td>
                <td class="py-3.5 px-4">
                  <span
                    v-if="d.sudah_diverifikasi"
                    class="text-xs px-2 py-0.5 rounded-full font-semibold inline-flex items-center gap-1 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/40"
                  >
                    <CheckCircle2 class="w-3 h-3 text-blue-600 dark:text-blue-400" />
                    <span>{{ d.verifikator }}</span>
                  </span>
                  <span
                    v-else
                    class="text-xs px-2 py-0.5 rounded-full font-semibold inline-flex items-center gap-1 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/40"
                  >
                    <Clock class="w-3 h-3 text-amber-600 dark:text-amber-400" />
                    <span>Menunggu</span>
                  </span>
                </td>
                <td class="py-3.5 px-4 text-xs text-slate-600 dark:text-slate-300">{{ tindakLabel(d.tindak_lanjut) }}</td>
                <td class="py-3.5 px-4 text-xs text-slate-500 dark:text-slate-400">{{ d.jadwal_kontrol ?? '—' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </BidanLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Calendar, Download, CheckCircle2, Clock } from 'lucide-vue-next'
import BidanLayout from '@/Layouts/BidanLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

const props = defineProps({
  laporan: Array,
  ringkasan: Object,
  filter: Object,
})

const filterBulan = ref(props.filter?.bulan ?? new Date().getMonth() + 1)
const filterTahun = ref(props.filter?.tahun ?? new Date().getFullYear())

const bulanOptions = computed(() => {
  const options = []
  for (let i = 0; i < 12; i++) {
    const d = new Date()
    d.setMonth(d.getMonth() - i)
    options.push({
      value: d.getMonth() + 1,
      label: d.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }),
    })
  }
  return options
})

const loadLaporan = () => {
  router.get(route('bidan.laporan'), {
    bulan: filterBulan.value,
    tahun: filterTahun.value,
  }, { preserveState: true })
}

const cfColor = (cf) => {
  if (cf >= 0.7) return 'text-rose-600 dark:text-rose-400'
  if (cf >= 0.4) return 'text-amber-600 dark:text-amber-400'
  return 'text-emerald-600 dark:text-emerald-400'
}

const tindakLabel = (t) => ({
  pantau: 'Pantau di Posyandu',
  edukasi_gizi: 'Edukasi Gizi',
  pmt: 'Pemberian PMT',
  rujuk_puskesmas: 'Rujuk Puskesmas',
  rujuk_rsud: 'Rujuk RSUD',
}[t] ?? '—')
</script>
