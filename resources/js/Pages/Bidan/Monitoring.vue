<template>
  <BidanLayout title="Monitoring Balita" :pending-count="belum_verifikasi.length">
    <div class="space-y-4">

      <!-- Tab -->
      <div class="flex gap-1 bg-gray-100 p-1 rounded-lg w-fit">
        <button
          v-for="tab in tabs" :key="tab.key"
          @click="activeTab = tab.key"
          :class="[
            'px-4 py-1.5 rounded-md text-sm font-medium transition-colors',
            activeTab === tab.key
              ? 'bg-white text-gray-800 shadow-sm'
              : 'text-gray-500 hover:text-gray-700'
          ]"
        >
          {{ tab.label }}
          <span v-if="tab.key === 'belum' && belum_verifikasi.length > 0"
            class="ml-1.5 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">
            {{ belum_verifikasi.length }}
          </span>
        </button>
      </div>

      <!-- Belum verifikasi -->
      <div v-if="activeTab === 'belum'">
        <div v-if="belum_verifikasi.length === 0" class="bg-white rounded-xl border border-gray-200 p-12 text-center">
          <div class="text-4xl mb-3">🎉</div>
          <div class="text-gray-500">Semua diagnosis sudah diverifikasi</div>
        </div>
        <div v-else class="bg-white rounded-xl border border-gray-200 overflow-hidden">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Balita</th>
                <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Tanggal</th>
                <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">CF</th>
                <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Status sistem</th>
                <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Kader</th>
                <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="d in belum_verifikasi" :key="d.id"
                class="border-b border-gray-50 hover:bg-gray-50"
              >
                <td class="py-3 px-4">
                  <div class="font-medium text-gray-800">{{ d.nama_balita }}</div>
                  <div class="text-xs text-gray-400">{{ d.usia_format }}</div>
                </td>
                <td class="py-3 px-4 text-gray-500">{{ d.tanggal }}</td>
                <td class="py-3 px-4">
                  <div class="font-semibold" :class="cfColor(d.cf_kombinasi)">{{ d.cf_persen }}</div>
                </td>
                <td class="py-3 px-4">
                  <StatusBadge :status="d.status" :label="d.label_status" />
                </td>
                <td class="py-3 px-4 text-gray-500 text-xs">{{ d.kader }}</td>
                <td class="py-3 px-4">
                  <Link
                    :href="route('bidan.verifikasi.show', d.id)"
                    class="text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700"
                  >
                    Tinjau →
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Sudah verifikasi -->
      <div v-if="activeTab === 'sudah'">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Balita</th>
                <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Tanggal</th>
                <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">CF</th>
                <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Status final</th>
                <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Verifikator</th>
                <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Tgl verifikasi</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="d in sudah_verifikasi" :key="d.id"
                class="border-b border-gray-50 hover:bg-gray-50"
              >
                <td class="py-3 px-4 font-medium text-gray-800">{{ d.nama_balita }}</td>
                <td class="py-3 px-4 text-gray-500">{{ d.tanggal }}</td>
                <td class="py-3 px-4 font-semibold" :class="cfColor(d.cf_kombinasi)">{{ d.cf_persen }}</td>
                <td class="py-3 px-4">
                  <StatusBadge :status="d.status_final" :label="d.label_status" />
                </td>
                <td class="py-3 px-4 text-gray-500 text-xs">{{ d.verifikator }}</td>
                <td class="py-3 px-4 text-gray-400 text-xs">{{ d.verified_at }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </BidanLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import BidanLayout from '@/Layouts/BidanLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

defineProps({
  belum_verifikasi: Array,
  sudah_verifikasi: Array,
  stats: Object,
})

const activeTab = ref('belum')
const tabs = [
  { key: 'belum', label: 'Perlu verifikasi' },
  { key: 'sudah', label: 'Sudah diverifikasi' },
]

const cfColor = (cf) => {
  if (cf >= 0.7) return 'text-red-600'
  if (cf >= 0.4) return 'text-amber-600'
  return 'text-green-600'
}
</script>