<template>
  <AdminLayout title="Log Aktivitas Sistem">
    <div class="space-y-5">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
            Log Aktivitas & Audit Trail
          </h2>
          <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">
            Riwayat pencatatan pemeriksaan balita, kalkulasi Certainty Factor, dan verifikasi medis bidan.
          </p>
        </div>
      </div>

      <!-- Log Table Card -->
      <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead>
              <tr class="border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/75 dark:bg-zinc-800/40 text-zinc-500 dark:text-zinc-400 text-[11px] uppercase tracking-wider font-semibold">
                <th class="py-3 px-4">Waktu</th>
                <th class="py-3 px-4">Balita</th>
                <th class="py-3 px-4">Kader Penginput</th>
                <th class="py-3 px-4">Hasil CF Sistem</th>
                <th class="py-3 px-4">Status Akhir</th>
                <th class="py-3 px-4">Verifikator Medis</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/70">
              <tr v-if="!aktivitas.data || aktivitas.data.length === 0">
                <td colspan="6" class="py-14 text-center">
                  <div class="w-12 h-12 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 flex items-center justify-center mx-auto mb-3">
                    <FileText class="w-6 h-6" />
                  </div>
                  <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Belum ada riwayat aktivitas</div>
                  <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 max-w-sm mx-auto">
                    Seluruh kalkulasi sistem dan pemeriksaan balita akan tercatat otomatis di sini.
                  </p>
                </td>
              </tr>
              <tr
                v-for="log in aktivitas.data"
                :key="log.id"
                class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition-colors"
              >
                <td class="py-3.5 px-4 text-zinc-500 dark:text-zinc-400 text-xs font-mono whitespace-nowrap">
                  {{ new Date(log.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) }}
                </td>
                <td class="py-3.5 px-4 font-semibold text-zinc-900 dark:text-white">
                  {{ log.kunjungan?.balita?.nama ?? '-' }}
                  <span class="block text-[11px] font-normal text-zinc-500 dark:text-zinc-400">
                    {{ log.kunjungan?.balita?.usia_format ?? '' }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-zinc-700 dark:text-zinc-300">
                  <span class="inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ log.kader?.name ?? '-' }}</span>
                  </span>
                </td>
                <td class="py-3.5 px-4 font-mono font-bold" :class="cfColor(log.cf_kombinasi)">
                  {{ log.cf_persen }}
                </td>
                <td class="py-3.5 px-4">
                  <StatusBadge :status="log.status_final" :label="log.label_status" />
                </td>
                <td class="py-3.5 px-4 text-xs text-zinc-500 dark:text-zinc-400">
                  <span v-if="log.sudah_diverifikasi" class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 font-medium">
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    <span>{{ log.verifikator?.name ?? 'Bidan' }}</span>
                  </span>
                  <span v-else class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 italic">
                    <Clock class="w-3.5 h-3.5" />
                    <span>Belum diverifikasi</span>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="aktivitas.last_page > 1" class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
          <div class="text-xs text-zinc-500 dark:text-zinc-400">
            Menampilkan {{ aktivitas.from }}–{{ aktivitas.to }} dari {{ aktivitas.total }} riwayat log
          </div>
          <div class="flex items-center gap-1">
            <Link
              v-for="link in aktivitas.links"
              :key="link.label"
              :href="link.url ?? '#'"
              v-html="link.label"
              class="px-3 py-1 text-xs rounded-lg border transition-colors"
              :class="[
                link.active
                  ? 'bg-blue-600 text-white border-blue-600 font-semibold'
                  : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700',
                !link.url ? 'opacity-40 pointer-events-none' : ''
              ]"
            />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { FileText, CheckCircle2, Clock } from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

defineProps({
  aktivitas: Object,
})

const cfColor = (cf) => {
  if (cf >= 0.7) return 'text-red-600 dark:text-red-400'
  if (cf >= 0.4) return 'text-amber-600 dark:text-amber-400'
  return 'text-emerald-600 dark:text-emerald-400'
}
</script>
