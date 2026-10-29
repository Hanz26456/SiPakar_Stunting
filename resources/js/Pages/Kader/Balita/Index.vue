<template>
  <KaderLayout title="Data Balita">
    <div class="space-y-6">

      <!-- Header & Action Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
            Data Balita Posyandu
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
            Kelola data demografi dan riwayat pemantauan gizi balita Posyandu Melati Pujer.
          </p>
        </div>

        <Link
          :href="route('kader.balita.create')"
          class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-orbit-primary hover:bg-orbit-primary/90 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-md shadow-purple-600/20 transition-all active:scale-[0.98] flex-shrink-0"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Balita Baru</span>
        </Link>
      </div>

      <!-- Quick Stats Row -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-4 shadow-2xs">
          <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Terdaftar</span>
            <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/40 flex items-center justify-center text-purple-600 dark:text-purple-400">
              <Baby class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-2">
            {{ balita.total }}
          </div>
          <div class="text-[11px] text-slate-400 mt-0.5">Balita aktif</div>
        </div>

        <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-4 shadow-2xs">
          <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Status Normal</span>
            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
              <Activity class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-2">
            {{ jumlahStatus('normal') }}
          </div>
          <div class="text-[11px] text-slate-400 mt-0.5">Gizi & TB baik</div>
        </div>

        <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-4 shadow-2xs">
          <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Berisiko Stunting</span>
            <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-600 dark:text-amber-400">
              <Activity class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 dark:text-amber-400 mt-2">
            {{ jumlahStatus('berisiko') }}
          </div>
          <div class="text-[11px] text-slate-400 mt-0.5">Perlu perhatian kader</div>
        </div>

        <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-4 shadow-2xs">
          <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Stunting / Berat</span>
            <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/40 flex items-center justify-center text-rose-600 dark:text-rose-400">
              <Activity class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl sm:text-3xl font-extrabold text-rose-600 dark:text-rose-400 mt-2">
            {{ jumlahStatus('stunting') + jumlahStatus('stunting_berat') }}
          </div>
          <div class="text-[11px] text-slate-400 mt-0.5">Prioritas rujukan bidan</div>
        </div>
      </div>

      <!-- Search & Filter Controls -->
      <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-3.5 shadow-2xs flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <div class="relative flex-1">
          <Search class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" />
          <input
            v-model="search"
            type="text"
            placeholder="Cari nama balita atau nama ibu..."
            class="w-full bg-slate-50 dark:bg-orbit-surface2 border border-slate-200 dark:border-orbit-border rounded-xl pl-9.5 pr-3 py-2 text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all"
            @input="doSearch"
          >
        </div>

        <div class="flex items-center gap-2">
          <Filter class="w-4 h-4 text-slate-400 ml-1 hidden sm:block" />
          <select
            v-model="filterStatus"
            class="w-full sm:w-auto bg-slate-50 dark:bg-orbit-surface2 border border-slate-200 dark:border-orbit-border rounded-xl px-3.5 py-2 text-xs sm:text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all"
            @change="doSearch"
          >
            <option value="">Semua Status Diagnosis</option>
            <option value="normal">Normal</option>
            <option value="berisiko">Berisiko</option>
            <option value="stunting">Stunting</option>
            <option value="stunting_berat">Stunting Berat</option>
          </select>
        </div>
      </div>

      <!-- Main Data Table Card -->
      <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead>
              <tr class="border-b border-slate-100 dark:border-orbit-border/80 bg-slate-50/80 dark:bg-orbit-surface2/50 text-slate-500 dark:text-slate-400 text-[11px] uppercase tracking-wider font-semibold">
                <th class="py-3 px-4">Nama Balita</th>
                <th class="py-3 px-4">Usia</th>
                <th class="py-3 px-4">L/P</th>
                <th class="py-3 px-4">Nama Ibu</th>
                <th class="py-3 px-4">Desa</th>
                <th class="py-3 px-4">Kunjungan Terakhir</th>
                <th class="py-3 px-4">Status Diagnosis</th>
                <th class="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-orbit-border/60">
              <!-- Empty State -->
              <tr v-if="balita.data.length === 0">
                <td colspan="8" class="py-14 text-center">
                  <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center mx-auto mb-3">
                    <Baby class="w-6 h-6" />
                  </div>
                  <div class="text-sm font-semibold text-slate-800 dark:text-slate-200">Belum ada data balita</div>
                  <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                    Data balita yang sesuai kriteria pencarian tidak ditemukan atau belum pernah didaftarkan.
                  </p>
                </td>
              </tr>

              <!-- Data Rows -->
              <tr
                v-for="b in balita.data"
                :key="b.id"
                class="hover:bg-slate-50/80 dark:hover:bg-white/[0.02] transition-colors cursor-pointer group"
                @click="$inertia.visit(route('kader.balita.show', b.id))"
              >
                <td class="py-3.5 px-4">
                  <div class="font-semibold text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                    {{ b.nama }}
                  </div>
                  <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
                    <Calendar class="w-3 h-3" />
                    <span>Lahir: {{ b.tanggal_lahir }}</span>
                  </div>
                </td>

                <td class="py-3.5 px-4 font-medium text-slate-700 dark:text-slate-300">
                  {{ b.usia_format }}
                </td>

                <td class="py-3.5 px-4">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold"
                    :class="b.jenis_kelamin === 'Laki-laki'
                      ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/40'
                      : 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/40'"
                  >
                    {{ b.jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }} · {{ b.jenis_kelamin }}
                  </span>
                </td>

                <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                  {{ b.nama_ibu }}
                </td>

                <td class="py-3.5 px-4 text-xs text-slate-500 dark:text-slate-400">
                  {{ b.desa ?? '—' }}
                </td>

                <td class="py-3.5 px-4 text-xs text-slate-500 dark:text-slate-400">
                  {{ b.kunjungan_terakhir ?? 'Belum ada' }}
                </td>

                <td class="py-3.5 px-4">
                  <StatusBadge
                    v-if="b.status_terbaru"
                    :status="b.status_terbaru"
                    :label="labelStatus(b.status_terbaru)"
                  />
                  <span v-else class="text-xs text-slate-400 italic">Belum diagnosis</span>
                </td>

                <td class="py-3.5 px-4 text-right" @click.stop>
                  <div class="flex items-center justify-end gap-1.5">
                    <Link
                      :href="route('kader.kunjungan.create-for-balita', b.id)"
                      class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-colors"
                      title="Catat Kunjungan Baru"
                    >
                      + Kunjungan
                    </Link>
                    <Link
                      :href="route('kader.balita.edit', b.id)"
                      class="px-2 py-1 text-xs text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 transition-colors"
                    >
                      Edit
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Controls -->
        <div v-if="balita.last_page > 1" class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-slate-100 dark:border-orbit-border/80 bg-slate-50/50 dark:bg-orbit-surface2/30">
          <div class="text-xs text-slate-500 dark:text-slate-400">
            Menampilkan {{ balita.from }}–{{ balita.to }} dari {{ balita.total }} balita
          </div>
          <div class="flex items-center gap-1">
            <Link
              v-for="link in balita.links"
              :key="link.label"
              :href="link.url ?? '#'"
              v-html="link.label"
              preserve-scroll
              class="px-3 py-1 text-xs rounded-lg border transition-colors"
              :class="[
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
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { Plus, Search, Filter, Baby, Activity, Calendar } from 'lucide-vue-next'
import KaderLayout from '@/Layouts/KaderLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

const props = defineProps({
  balita: Object,
  filters: Object,
})

const search = ref(props.filters?.search ?? '')
const filterStatus = ref(props.filters?.status ?? '')

const doSearch = () => {
  router.get(route('kader.balita.index'), {
    search: search.value,
    status: filterStatus.value,
  }, { preserveState: true, replace: true })
}

const jumlahStatus = (status) => {
  return props.balita.data.filter(b => b.status_terbaru === status).length
}

const labelStatus = (status) => ({
  normal: 'Normal',
  berisiko: 'Berisiko',
  stunting: 'Stunting',
  stunting_berat: 'Stunting Berat',
}[status] ?? status)
</script>