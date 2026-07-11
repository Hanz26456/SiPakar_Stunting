<template>
  <BidanLayout title="Laporan Medis">
    <div class="max-w-5xl mx-auto space-y-4">

      <!-- Filter bulan -->
      <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-4">
        <div class="text-sm font-medium text-gray-700">Periode laporan:</div>
        <select
          v-model="filterBulan"
          class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
        >
          <option v-for="b in bulanOptions" :key="b.value" :value="b.value">{{ b.label }}</option>
        </select>
        <button
          @click="loadLaporan"
          class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Tampilkan
        </button>
        <div class="ml-auto flex gap-2">
          <a
            :href="route('bidan.laporan.export', { bulan: filterBulan, tahun: filterTahun })"
            class="px-4 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 flex items-center gap-1"
          >
            ⬇️ Ekspor PDF
          </a>
        </div>
      </div>

      <!-- Ringkasan -->
      <div class="grid grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
          <div class="text-2xl font-semibold text-gray-800">{{ ringkasan.total }}</div>
          <div class="text-xs text-gray-400 mt-1">Total diagnosis</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
          <div class="text-2xl font-semibold text-green-600">{{ ringkasan.normal }}</div>
          <div class="text-xs text-gray-400 mt-1">Normal</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
          <div class="text-2xl font-semibold text-amber-600">{{ ringkasan.berisiko }}</div>
          <div class="text-xs text-gray-400 mt-1">Berisiko</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
          <div class="text-2xl font-semibold text-red-600">{{ ringkasan.stunting }}</div>
          <div class="text-xs text-gray-400 mt-1">Stunting</div>
        </div>
      </div>

      <!-- Tabel laporan -->
      <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Nama balita</th>
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Usia</th>
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Tanggal</th>
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Status sistem</th>
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Verifikasi</th>
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">CF</th>
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Status final</th>
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Tindakan</th>
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Kontrol</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="d in laporan" :key="d.id"
              class="border-b border-gray-50 hover:bg-gray-50"
            >
              <td class="py-2.5 px-4 font-medium text-gray-800">{{ d.nama_balita }}</td>
              <td class="py-2.5 px-4 text-gray-500 text-xs">{{ d.usia_format }}</td>
              <td class="py-2.5 px-4 text-gray-500 text-xs">{{ d.tanggal }}</td>
              <td class="py-2.5 px-4">
                <StatusBadge :status="d.status_sistem" :label="d.label_status" />
              </td>
              <td class="py-2.5 px-4">
                <span v-if="d.sudah_diverifikasi" class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">
                  ✓ {{ d.verifikator }}
                </span>
                <span v-else class="text-xs text-amber-500">Menunggu</span>
              </td>
              <td class="py-2.5 px-4 font-medium text-xs" :class="cfColor(d.cf_kombinasi)">
                {{ d.cf_persen }}
              </td>
              <td class="py-2.5 px-4">
                <StatusBadge :status="d.status_final" :label="d.label_status" />
              </td>
              <td class="py-2.5 px-4 text-xs text-gray-500">{{ tindakLabel(d.tindak_lanjut) }}</td>
              <td class="py-2.5 px-4 text-xs text-gray-400">{{ d.jadwal_kontrol ?? '—' }}</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </BidanLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
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

const cfColor   = (cf) => cf >= 0.7 ? 'text-red-600' : cf >= 0.4 ? 'text-amber-600' : 'text-green-600'
const tindakLabel = (t) => ({
  pantau: 'Pantau', edukasi_gizi: 'Edukasi gizi',
  pmt: 'PMT', rujuk_puskesmas: 'Rujuk PKM', rujuk_rsud: 'Rujuk RSUD',
}[t] ?? '—')
</script>