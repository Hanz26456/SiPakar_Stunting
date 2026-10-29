<template>
  <BidanLayout title="Monitoring Pasien Balita" :pending-count="belum_verifikasi.length">
    <div class="space-y-5">

      <!-- Tabs Navigation -->
      <div class="flex items-center gap-2 bg-slate-100 dark:bg-orbit-surface2 p-1.5 rounded-2xl w-fit border border-slate-200/80 dark:border-orbit-border shadow-2xs">
        <button
          v-for="tab in tabs" :key="tab.key"
          type="button"
          @click="activeTab = tab.key"
          :class="[
            'px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all inline-flex items-center gap-2',
            activeTab === tab.key
              ? 'bg-orbit-primary text-white shadow-md shadow-purple-600/20'
              : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
          ]"
        >
          <span>{{ tab.label }}</span>
          <span
            v-if="tab.key === 'belum' && belum_verifikasi.length > 0"
            class="px-2 py-0.5 rounded-full text-xs font-bold"
            :class="activeTab === 'belum' ? 'bg-white/20 text-white' : 'bg-rose-500 text-white'"
          >
            {{ belum_verifikasi.length }}
          </span>
        </button>
      </div>

      <!-- Tab Belum Verifikasi -->
      <div v-if="activeTab === 'belum'">
        <div v-if="belum_verifikasi.length === 0" class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-12 text-center shadow-2xs">
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-3">
            <CheckCircle2 class="w-6 h-6" />
          </div>
          <div class="text-sm font-bold text-slate-800 dark:text-slate-200">Semua Pemeriksaan Telah Divalidasi</div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
            Tidak ada antrean hasil diagnosis stunting yang menunggu verifikasi klinis bidan saat ini.
          </p>
        </div>

        <div v-else class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border shadow-2xs overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
              <thead>
                <tr class="border-b border-slate-100 dark:border-orbit-border/80 bg-slate-50/80 dark:bg-orbit-surface2/50 text-slate-500 dark:text-slate-400 text-[11px] uppercase tracking-wider font-semibold">
                  <th class="py-3 px-4">Nama Balita</th>
                  <th class="py-3 px-4">Tanggal Kunjungan</th>
                  <th class="py-3 px-4">Nilai CF</th>
                  <th class="py-3 px-4">Diagnosis Sistem</th>
                  <th class="py-3 px-4">Kader Penginput</th>
                  <th class="py-3 px-4 text-right">Tindakan</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-orbit-border/60">
                <tr
                  v-for="d in belum_verifikasi" :key="d.id"
                  class="hover:bg-slate-50/80 dark:hover:bg-white/[0.02] transition-colors"
                >
                  <td class="py-3.5 px-4">
                    <div class="font-semibold text-slate-900 dark:text-white">{{ d.nama_balita }}</div>
                    <div class="text-xs text-slate-400 mt-0.5">{{ d.usia_format }}</div>
                  </td>
                  <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 text-xs">{{ d.tanggal }}</td>
                  <td class="py-3.5 px-4 font-mono font-bold" :class="cfColor(d.cf_kombinasi)">
                    {{ d.cf_persen }}
                  </td>
                  <td class="py-3.5 px-4">
                    <StatusBadge :status="d.status" :label="d.label_status" />
                  </td>
                  <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300 text-xs font-medium">{{ d.kader }}</td>
                  <td class="py-3.5 px-4 text-right">
                    <Link
                      :href="route('bidan.verifikasi.show', d.id)"
                      class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-orbit-primary hover:bg-orbit-primary/90 text-white shadow-sm shadow-purple-600/20 transition-all active:scale-[0.98]"
                    >
                      <span>Validasi Medis</span>
                      <ArrowRight class="w-3.5 h-3.5" />
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Tab Sudah Verifikasi -->
      <div v-if="activeTab === 'sudah'">
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border shadow-2xs overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
              <thead>
                <tr class="border-b border-slate-100 dark:border-orbit-border/80 bg-slate-50/80 dark:bg-orbit-surface2/50 text-slate-500 dark:text-slate-400 text-[11px] uppercase tracking-wider font-semibold">
                  <th class="py-3 px-4">Nama Balita</th>
                  <th class="py-3 px-4">Tanggal Kunjungan</th>
                  <th class="py-3 px-4">Nilai CF</th>
                  <th class="py-3 px-4">Status Akhir</th>
                  <th class="py-3 px-4">Bidan Verifikator</th>
                  <th class="py-3 px-4">Waktu Verifikasi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-orbit-border/60">
                <tr v-if="sudah_verifikasi.length === 0">
                  <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                    Belum ada data pemeriksaan yang telah diverifikasi.
                  </td>
                </tr>
                <tr
                  v-for="d in sudah_verifikasi" :key="d.id"
                  class="hover:bg-slate-50/80 dark:hover:bg-white/[0.02] transition-colors"
                >
                  <td class="py-3.5 px-4 font-semibold text-slate-900 dark:text-white">{{ d.nama_balita }}</td>
                  <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 text-xs">{{ d.tanggal }}</td>
                  <td class="py-3.5 px-4 font-mono font-bold" :class="cfColor(d.cf_kombinasi)">{{ d.cf_persen }}</td>
                  <td class="py-3.5 px-4">
                    <StatusBadge :status="d.status_final" :label="d.label_status" />
                  </td>
                  <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300 text-xs">{{ d.verifikator }}</td>
                  <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 text-xs">{{ d.verified_at }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </BidanLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { CheckCircle2, ArrowRight } from 'lucide-vue-next'
import BidanLayout from '@/Layouts/BidanLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

defineProps({
  belum_verifikasi: Array,
  sudah_verifikasi: Array,
  stats: Object,
})

const activeTab = ref('belum')
const tabs = [
  { key: 'belum', label: 'Menunggu Validasi Medis' },
  { key: 'sudah', label: 'Telah Diverifikasi' },
]

const cfColor = (cf) => {
  if (cf >= 0.7) return 'text-rose-600 dark:text-rose-400'
  if (cf >= 0.4) return 'text-amber-600 dark:text-amber-400'
  return 'text-emerald-600 dark:text-emerald-400'
}
</script>
