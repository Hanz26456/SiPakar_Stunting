<template>
  <KaderLayout title="Data Balita">

    <!-- Search + Filter + Tambah -->
    <div class="flex items-center gap-3 mb-5">
      <div class="relative flex-1">
        <input
          v-model="search"
          type="text"
          placeholder="Cari nama balita atau nama ibu..."
          class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 bg-white"
          @input="doSearch"
        >
        <span class="absolute left-3 top-2.5 text-gray-400 text-sm">🔍</span>
      </div>
      <select
        v-model="filterStatus"
        class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none"
        @change="doSearch"
      >
        <option value="">Semua status</option>
        <option value="normal">Normal</option>
        <option value="berisiko">Berisiko</option>
        <option value="stunting">Stunting</option>
        <option value="stunting_berat">Stunting Berat</option>
      </select>
      <Link
        :href="route('kader.balita.create')"
        class="px-4 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 flex items-center gap-1.5 font-medium flex-shrink-0"
      >
        <span>+</span> Tambah Balita
      </Link>
    </div>

    <!-- Statistik ringkas -->
    <div class="grid grid-cols-4 gap-3 mb-5">
      <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
        <div class="text-2xl font-semibold text-gray-800">{{ balita.total }}</div>
        <div class="text-xs text-gray-400 mt-1">Total Balita</div>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
        <div class="text-2xl font-semibold text-green-600">{{ jumlahStatus('normal') }}</div>
        <div class="text-xs text-gray-400 mt-1">Normal</div>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
        <div class="text-2xl font-semibold text-amber-600">{{ jumlahStatus('berisiko') }}</div>
        <div class="text-xs text-gray-400 mt-1">Berisiko</div>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
        <div class="text-2xl font-semibold text-red-600">{{ jumlahStatus('stunting') + jumlahStatus('stunting_berat') }}</div>
        <div class="text-xs text-gray-400 mt-1">Stunting</div>
      </div>
    </div>

    <!-- Tabel balita -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-100 bg-gray-50">
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Nama Balita</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Usia</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">L/P</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Nama Ibu</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Desa</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Kunjungan Terakhir</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Status</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="balita.data.length === 0">
            <td colspan="8" class="py-12 text-center text-gray-400 text-sm">
              <div class="text-4xl mb-2">👶</div>
              Belum ada data balita
            </td>
          </tr>
          <tr
            v-for="b in balita.data" :key="b.id"
            class="border-b border-gray-50 hover:bg-gray-50 cursor-pointer"
            @click="$inertia.visit(route('kader.balita.show', b.id))"
          >
            <td class="py-3 px-4">
              <div class="font-medium text-gray-800">{{ b.nama }}</div>
              <div class="text-xs text-gray-400">{{ b.tanggal_lahir }}</div>
            </td>
            <td class="py-3 px-4 text-gray-600">{{ b.usia_format }}</td>
            <td class="py-3 px-4">
              <span
                class="px-2 py-0.5 rounded-full text-xs font-medium"
                :class="b.jenis_kelamin === 'Laki-laki'
                  ? 'bg-blue-50 text-blue-700'
                  : 'bg-pink-50 text-pink-700'"
              >
                {{ b.jenis_kelamin === 'Laki-laki' ? '♂' : '♀' }} {{ b.jenis_kelamin }}
              </span>
            </td>
            <td class="py-3 px-4 text-gray-600">{{ b.nama_ibu }}</td>
            <td class="py-3 px-4 text-gray-500 text-xs">{{ b.desa ?? '—' }}</td>
            <td class="py-3 px-4 text-gray-500 text-xs">{{ b.kunjungan_terakhir ?? 'Belum ada' }}</td>
            <td class="py-3 px-4">
              <StatusBadge
                v-if="b.status_terbaru"
                :status="b.status_terbaru"
                :label="labelStatus(b.status_terbaru)"
              />
              <span v-else class="text-xs text-gray-300">Belum diagnosis</span>
            </td>
            <td class="py-3 px-4" @click.stop>
              <div class="flex items-center gap-2">
                <Link
                  :href="route('kader.kunjungan.create-for-balita', b.id)"
                  class="text-xs bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-lg hover:bg-emerald-100 font-medium"
                >
                  + Kunjungan
                </Link>
                <Link
                  :href="route('kader.balita.edit', b.id)"
                  class="text-xs text-gray-500 hover:text-gray-700"
                >
                  Edit
                </Link>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="balita.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
        <div class="text-xs text-gray-400">
          Menampilkan {{ balita.from }}–{{ balita.to }} dari {{ balita.total }} balita
        </div>
        <div class="flex gap-1">
          <Link
            v-for="link in balita.links" :key="link.label"
            :href="link.url ?? '#'"
            v-html="link.label"
            preserve-scroll
            :class="[
              'px-3 py-1 text-xs rounded border',
              link.active
                ? 'bg-emerald-600 text-white border-emerald-600'
                : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50',
              !link.url ? 'opacity-40 pointer-events-none' : ''
            ]"
          />
        </div>
      </div>
    </div>

  </KaderLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
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