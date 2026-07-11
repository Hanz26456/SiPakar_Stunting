<template>
  <OrtuLayout title="Tumbuh Kembang">
    <div class="px-4 py-4">

      <!-- Header -->
      <div class="flex items-center gap-3 mb-4">
        <Link :href="route('ortu.dashboard')" class="text-gray-400">←</Link>
        <div>
          <div class="font-semibold text-gray-800">{{ balita.nama }}</div>
          <div class="text-xs text-gray-400">{{ balita.usia_format }} · {{ balita.jenis_kelamin }}</div>
        </div>
      </div>

      <!-- Status terbaru -->
      <div v-if="status" class="rounded-xl p-4 mb-4 border"
        :class="{
          'bg-red-50 border-red-200':    ['stunting','stunting_berat'].includes(status.status_stunting ?? ''),
          'bg-amber-50 border-amber-200': status.label_status === 'Berisiko',
          'bg-green-50 border-green-200': status.label_status === 'Normal',
        }"
      >
        <div class="flex items-center gap-2 mb-1">
          <span class="text-lg font-bold"
            :class="{
              'text-red-600':   ['Stunting','Stunting Berat'].includes(status.label_status),
              'text-amber-600': status.label_status === 'Berisiko',
              'text-green-600': status.label_status === 'Normal',
            }"
          >{{ status.label_status }}</span>
          <span class="text-sm text-gray-400">CF: {{ status.cf_persen }}</span>
          <span v-if="status.verified" class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">✓ Terverifikasi bidan</span>
        </div>
        <div v-if="status.rekomendasi" class="text-sm text-gray-600 mt-2">{{ status.rekomendasi }}</div>
        <div v-if="status.jadwal_kontrol" class="text-sm text-gray-500 mt-2">
          📅 Jadwal kontrol: <strong>{{ status.jadwal_kontrol }}</strong>
        </div>
      </div>

      <!-- Pengukuran terbaru -->
      <div v-if="pengukuran_terbaru" class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
        <div class="text-sm font-medium text-gray-700 mb-3">Pengukuran terakhir — {{ pengukuran_terbaru.tanggal }}</div>
        <div class="grid grid-cols-3 gap-3">
          <div class="text-center">
            <div class="text-xl font-bold text-gray-800">{{ pengukuran_terbaru.berat_badan }}</div>
            <div class="text-xs text-gray-400">BB (kg)</div>
          </div>
          <div class="text-center">
            <div class="text-xl font-bold text-gray-800">{{ pengukuran_terbaru.tinggi_badan }}</div>
            <div class="text-xs text-gray-400">TB (cm)</div>
          </div>
          <div class="text-center">
            <div class="text-xl font-bold"
              :class="pengukuran_terbaru.zscore_tbu < -2 ? 'text-red-600' : pengukuran_terbaru.zscore_tbu < -1 ? 'text-amber-600' : 'text-green-600'"
            >{{ pengukuran_terbaru.zscore_tbu?.toFixed(1) }}</div>
            <div class="text-xs text-gray-400">Z-score</div>
          </div>
        </div>
      </div>

      <!-- Tren pertumbuhan -->
      <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
        <div class="text-sm font-medium text-gray-700 mb-3">Tren pertumbuhan</div>
        <div class="space-y-3">
          <div v-for="t in tren" :key="t.bulan" class="flex items-center gap-2">
            <div class="text-xs text-gray-400 w-14 flex-shrink-0">{{ t.bulan.split(' ')[0] }}</div>
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-0.5">
                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                  <div
                    class="h-full rounded-full"
                    :class="t.zscore_tbu < -2 ? 'bg-red-400' : t.zscore_tbu < -1 ? 'bg-amber-400' : 'bg-emerald-400'"
                    :style="{ width: Math.min(100, Math.max(5, (t.zscore_tbu + 3) / 6 * 100)) + '%' }"
                  ></div>
                </div>
                <span class="text-xs w-12 text-right flex-shrink-0"
                  :class="t.zscore_tbu < -2 ? 'text-red-600' : t.zscore_tbu < -1 ? 'text-amber-600' : 'text-green-600'">
                  {{ t.zscore_tbu?.toFixed(1) }} SD
                </span>
              </div>
              <div class="text-xs text-gray-400">{{ t.berat_badan }} kg · {{ t.tinggi_badan }} cm</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panduan -->
      <Link :href="route('ortu.panduan')"
        class="block bg-emerald-600 text-white rounded-xl p-4 text-center"
      >
        <div class="text-base font-semibold mb-0.5">💡 Lihat panduan gizi</div>
        <div class="text-sm text-emerald-100">Tips makanan & perawatan untuk {{ balita.nama }}</div>
      </Link>
    </div>
  </OrtuLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import OrtuLayout from '@/Layouts/OrtuLayout.vue'

defineProps({
  balita: Object,
  status: Object,
  pengukuran_terbaru: Object,
  tren: Array,
})
</script>