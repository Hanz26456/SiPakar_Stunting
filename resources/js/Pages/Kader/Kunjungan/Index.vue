<template>
  <KaderLayout title="Daftar Kunjungan Balita">
    <div class="space-y-5">
      <!-- Top Filters & Actions -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex flex-1 flex-col sm:flex-row items-stretch sm:items-center gap-3">
          <div class="relative flex-1">
            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              v-model="search"
              type="text"
              placeholder="Cari nama balita atau orang tua..."
              class="w-full border border-slate-200 dark:border-orbit-border rounded-xl pl-10 pr-3 py-2 text-xs sm:text-sm bg-white dark:bg-orbit-surface text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orbit-primary shadow-2xs"
              @input="doFilter"
            >
          </div>
          <div class="flex items-center gap-2">
            <select
              v-model="filterBulan"
              class="border border-slate-200 dark:border-orbit-border rounded-xl px-3 py-2 text-xs sm:text-sm bg-white dark:bg-orbit-surface text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orbit-primary shadow-2xs"
              @change="doFilter"
            >
              <option v-for="b in bulanOptions" :key="b.value" :value="b.value">{{ b.label }}</option>
            </select>
            <select
              v-model="filterStatus"
              class="border border-slate-200 dark:border-orbit-border rounded-xl px-3 py-2 text-xs sm:text-sm bg-white dark:bg-orbit-surface text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orbit-primary shadow-2xs"
              @change="doFilter"
            >
              <option value="">Semua Status Gizi</option>
              <option value="Normal">Normal</option>
              <option value="Berisiko">Berisiko</option>
              <option value="Stunting">Stunting</option>
              <option value="Stunting berat">Stunting Berat</option>
            </select>
          </div>
        </div>

        <Link
          :href="route('kader.kunjungan.create')"
          class="px-4 py-2 text-xs sm:text-sm bg-orbit-primary hover:bg-orbit-primary/90 text-white rounded-xl shadow-md shadow-purple-600/20 flex items-center justify-center gap-2 font-semibold flex-shrink-0 transition-all active:scale-[0.98]"
        >
          <Plus class="w-4 h-4" />
          <span>Input Kunjungan Baru</span>
        </Link>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-4 shadow-2xs">
          <div class="text-2xl font-mono font-bold text-slate-800 dark:text-slate-100">{{ kunjungan.total }}</div>
          <div class="text-xs text-slate-400 mt-1 font-medium">Total Pemeriksaan Bulan Ini</div>
        </div>
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-4 shadow-2xs">
          <div class="text-2xl font-mono font-bold text-emerald-500">{{ jumlahSudahDiagnosa }}</div>
          <div class="text-xs text-slate-400 mt-1 font-medium">Sudah Dihitung CF</div>
        </div>
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-4 shadow-2xs">
          <div class="text-2xl font-mono font-bold text-amber-500">{{ jumlahBelumDiagnosa }}</div>
          <div class="text-xs text-slate-400 mt-1 font-medium">Menunggu Diagnosis CF</div>
        </div>
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-4 shadow-2xs">
          <div class="text-2xl font-mono font-bold text-rose-500">{{ jumlahStunting }}</div>
          <div class="text-xs text-slate-400 mt-1 font-medium">Indikasi Stunting</div>
        </div>
      </div>

      <!-- Main Data Table -->
      <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-xs sm:text-sm text-left">
            <thead>
              <tr class="border-b border-slate-100 dark:border-orbit-border bg-slate-50 dark:bg-orbit-surface2/60 text-[11px] uppercase tracking-wider font-semibold text-slate-400">
                <th class="py-3 px-4">Nama Balita</th>
                <th class="py-3 px-4">Tanggal Periksa</th>
                <th class="py-3 px-4">Usia</th>
                <th class="py-3 px-4">BB / TB</th>
                <th class="py-3 px-4">LiLA</th>
                <th class="py-3 px-4">Z-score TB/U</th>
                <th class="py-3 px-4">Status Gizi</th>
                <th class="py-3 px-4">Status Diagnosis</th>
                <th class="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-orbit-border/60">
              <tr v-if="kunjungan.data.length === 0">
                <td colspan="9" class="py-12 text-center text-slate-400 text-xs sm:text-sm">
                  <ClipboardList class="w-10 h-10 mx-auto mb-2 opacity-30 text-slate-400" />
                  Belum ada data kunjungan pada filter yang dipilih.
                </td>
              </tr>
              <tr
                v-for="k in kunjungan.data"
                :key="k.id"
                class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] cursor-pointer transition-colors"
                @click="router.visit(route('kader.kunjungan.show', k.id))"
              >
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-800 dark:text-slate-100">{{ k.nama_balita }}</div>
                  <div class="text-[11px] text-slate-400 mt-0.5">Kader: {{ k.kader }}</div>
                </td>
                <td class="py-3 px-4 text-slate-600 dark:text-slate-300 font-mono text-xs whitespace-nowrap">{{ k.tanggal }}</td>
                <td class="py-3 px-4 text-slate-400 text-xs whitespace-nowrap">{{ k.usia_bulan }} bln</td>
                <td class="py-3 px-4 text-slate-600 dark:text-slate-300 font-mono text-xs whitespace-nowrap">
                  <div>{{ k.berat_badan }} kg</div>
                  <div>{{ k.tinggi_badan }} cm</div>
                </td>
                <td class="py-3 px-4 text-slate-600 dark:text-slate-300 font-mono text-xs whitespace-nowrap">
                  {{ k.lila ? k.lila + ' cm' : '—' }}
                </td>
                <td class="py-3 px-4 text-xs font-mono font-bold whitespace-nowrap" :class="zColor(k.zscore_tbu)">
                  {{ k.zscore_tbu != null ? k.zscore_tbu.toFixed(2) : '—' }} SD
                </td>
                <td class="py-3 px-4 whitespace-nowrap">
                  <span class="text-[11px] px-2.5 py-1 rounded-full font-semibold" :class="statusClass(k.status)">
                    {{ k.status }}
                  </span>
                </td>
                <td class="py-3 px-4 whitespace-nowrap">
                  <span v-if="k.sudah_diagnosa" class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 font-semibold">
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    <span>Selesai CF</span>
                  </span>
                  <span v-else class="inline-flex items-center gap-1 text-xs text-amber-500 font-medium">
                    <Clock class="w-3.5 h-3.5" />
                    <span>Belum CF</span>
                  </span>
                </td>
                <td class="py-3 px-4 text-right whitespace-nowrap" @click.stop>
                  <div class="flex items-center justify-end gap-1.5">
                    <Link
                      :href="route('kader.kunjungan.show', k.id)"
                      class="text-xs text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-orbit-border bg-slate-50 dark:bg-white/5 px-2.5 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-white/10 transition-colors font-medium"
                    >
                      Detail
                    </Link>
                    <Link
                      v-if="!k.sudah_diagnosa"
                      :href="route('kader.diagnosis.create', k.id)"
                      class="inline-flex items-center gap-1 text-xs text-white bg-orbit-primary hover:bg-orbit-primary/90 px-3 py-1.5 rounded-lg font-semibold shadow-xs transition-colors"
                    >
                      <span>Diagnosa</span>
                      <ArrowRight class="w-3 h-3" />
                    </Link>
                    <Link
                      v-else
                      :href="k.diagnosis_id ? route('kader.diagnosis.show', { diagnosis: k.diagnosis_id }) : '#'"
                      class="inline-flex items-center gap-1 text-xs text-orbit-primary border border-purple-500/30 bg-purple-500/10 hover:bg-purple-500/20 px-2.5 py-1.5 rounded-lg font-semibold transition-colors"
                    >
                      <span>Hasil</span>
                      <ArrowRight class="w-3 h-3" />
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="kunjungan.last_page > 1" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 py-3 border-t border-slate-100 dark:border-orbit-border bg-slate-50/50 dark:bg-orbit-surface2/30">
          <div class="text-xs text-slate-400 font-medium">
            Menampilkan {{ kunjungan.from }}–{{ kunjungan.to }} dari {{ kunjungan.total }} kunjungan
          </div>
          <div class="flex items-center gap-1">
            <Link
              v-for="link in kunjungan.links"
              :key="link.label"
              :href="link.url ?? '#'"
              v-html="link.label"
              preserve-scroll
              :class="[
                'px-3 py-1 text-xs rounded-lg border transition-colors',
                link.active
                  ? 'bg-orbit-primary text-white border-orbit-primary font-semibold'
                  : 'bg-white dark:bg-orbit-surface text-slate-600 dark:text-slate-300 border-slate-200 dark:border-orbit-border hover:bg-slate-50 dark:hover:bg-white/5',
                !link.url ? 'opacity-40 pointer-events-none' : ''
              ]"
            />
          </div>
        </div>
      </div>
    </div>
  </KaderLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import {
  Search,
  Plus,
  ClipboardList,
  CheckCircle2,
  Clock,
  ArrowRight
} from 'lucide-vue-next'
import KaderLayout from '@/Layouts/KaderLayout.vue'

const props = defineProps({ kunjungan: Object, filters: Object })
const search       = ref(props.filters?.search ?? '')
const filterBulan  = ref(props.filters?.bulan ?? new Date().getMonth() + 1)
const filterStatus = ref(props.filters?.status ?? '')

const bulanOptions = computed(() => {
  const opts = []
  for (let i = 0; i < 6; i++) {
    const d = new Date()
    d.setMonth(d.getMonth() - i)
    opts.push({
      value: d.getMonth() + 1,
      label: d.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' })
    })
  }
  return opts
})

const doFilter = () => {
  router.get(
    route('kader.kunjungan.index'),
    { search: search.value, bulan: filterBulan.value, status: filterStatus.value },
    { preserveState: true, replace: true }
  )
}

const jumlahSudahDiagnosa = computed(() => props.kunjungan.data.filter(k => k.sudah_diagnosa).length)
const jumlahBelumDiagnosa = computed(() => props.kunjungan.data.filter(k => !k.sudah_diagnosa).length)
const jumlahStunting      = computed(() => props.kunjungan.data.filter(k => ['Stunting','Stunting berat'].includes(k.status)).length)

const zColor = (z) => {
  if (z == null) return 'text-slate-400'
  if (z < -2) return 'text-rose-500'
  if (z < -1) return 'text-amber-500'
  return 'text-emerald-500'
}

const statusClass = (s) => ({
  'Normal': 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20',
  'Berisiko': 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20',
  'Stunting': 'bg-rose-500/10 text-rose-500 border border-rose-500/20',
  'Stunting berat': 'bg-rose-600/20 text-rose-600 dark:text-rose-400 border border-rose-600/30'
}[s] ?? 'bg-slate-100 dark:bg-orbit-border text-slate-500')
</script>
