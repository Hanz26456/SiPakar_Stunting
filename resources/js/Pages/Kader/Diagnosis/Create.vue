<template>
  <KaderLayout title="Diagnosis Stunting">
    <div class="max-w-5xl mx-auto">

      <!-- Info balita + kunjungan -->
      <div class="grid grid-cols-3 gap-4 mb-5">
        <div class="col-span-2 bg-white rounded-xl border border-gray-200 p-4">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-lg">👶</div>
            <div>
              <div class="font-medium text-gray-800">{{ balita.nama }}</div>
              <div class="text-sm text-gray-400">{{ balita.usia_format }} · {{ balita.jenis_kelamin }}</div>
            </div>
          </div>
          <div class="grid grid-cols-4 gap-3">
            <div class="text-center p-2 bg-gray-50 rounded-lg">
              <div class="text-lg font-semibold text-gray-800">{{ kunjungan.berat_badan }}</div>
              <div class="text-xs text-gray-400">BB (kg)</div>
            </div>
            <div class="text-center p-2 bg-gray-50 rounded-lg">
              <div class="text-lg font-semibold text-gray-800">{{ kunjungan.tinggi_badan }}</div>
              <div class="text-xs text-gray-400">TB (cm)</div>
            </div>
            <div class="text-center p-2 bg-gray-50 rounded-lg">
              <div class="text-lg font-semibold" :class="zscoreColor(kunjungan.zscore_tbu)">
                {{ kunjungan.zscore_tbu?.toFixed(2) ?? '-' }}
              </div>
              <div class="text-xs text-gray-400">Z-score TB/U</div>
            </div>
            <div class="text-center p-2 rounded-lg" :class="statusBg(kunjungan.status_zscore)">
              <div class="text-sm font-semibold" :class="statusText(kunjungan.status_zscore)">
                {{ kunjungan.status_zscore }}
              </div>
              <div class="text-xs text-gray-400">Status</div>
            </div>
          </div>
        </div>

        <!-- Preview CF real-time -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-col">
          <div class="text-xs font-medium text-gray-500 mb-2">CF kombinasi</div>
          <div class="flex-1 flex flex-col items-center justify-center">
            <div v-if="loading" class="text-gray-300 text-2xl">⏳</div>
            <div v-else>
              <div
                class="text-4xl font-bold mb-1 text-center"
                :class="cfColor(preview?.cf_kombinasi ?? 0)"
              >
                {{ preview?.cf_persen ?? '0%' }}
              </div>
              <div class="text-center">
                <StatusBadge
                  :status="preview?.status_stunting ?? 'normal'"
                  :label="preview?.label_status ?? 'Normal'"
                />
              </div>
              <div class="text-xs text-gray-400 text-center mt-2">
                {{ preview?.gejala_aktif ?? 0 }} / {{ preview?.total_gejala ?? 0 }} gejala aktif
              </div>
            </div>
          </div>
          <!-- CF bar -->
          <div class="mt-3">
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="cfBarColor(preview?.cf_kombinasi ?? 0)"
                :style="{ width: ((preview?.cf_kombinasi ?? 0) * 100) + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <!-- Gejala otomatis -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
          <div class="text-sm font-medium text-gray-700 mb-1">🤖 Gejala terdeteksi otomatis</div>
          <div class="text-xs text-gray-400 mb-3">Dari data kunjungan & riwayat balita</div>
          <div class="space-y-2">
            <div
              v-for="g in gejalOtomatis" :key="g.id"
              class="flex items-start gap-3 p-2.5 rounded-lg"
              :class="g.terdeteksi ? 'bg-red-50 border border-red-100' : 'bg-gray-50'"
            >
              <div class="mt-0.5 flex-shrink-0">
                <span v-if="g.terdeteksi" class="text-red-500">⚠️</span>
                <span v-else class="text-green-500">✅</span>
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-sm text-gray-700">{{ g.nama_gejala }}</div>
                <div class="text-xs text-gray-400 mt-0.5">{{ g.kode }} · otomatis</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Gejala manual -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
          <div class="text-sm font-medium text-gray-700 mb-1">👁️ Gejala klinis (centang manual)</div>
          <div class="text-xs text-gray-400 mb-3">Periksa langsung pada balita</div>
          <div class="space-y-2">
            <label
              v-for="g in gejala_manual" :key="g.id"
              class="flex items-start gap-3 p-2.5 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
              :class="gejalaManu[g.id] ? 'bg-amber-50 border border-amber-100' : ''"
            >
              <input
                type="checkbox"
                :checked="gejalaManu[g.id]"
                @change="toggleGejala(g.id)"
                class="mt-0.5 rounded accent-emerald-600"
              >
              <div class="flex-1">
                <div class="text-sm text-gray-700">{{ g.nama_gejala }}</div>
                <div class="text-xs text-gray-400 mt-0.5">{{ g.kode }} · {{ g.kategori }}</div>
              </div>
            </label>
          </div>
        </div>

        <!-- Detail CF per gejala -->
        <div class="col-span-2 bg-white rounded-xl border border-gray-200 p-4">
          <div class="text-sm font-medium text-gray-700 mb-3">📊 Detail perhitungan Certainty Factor</div>
          <div class="space-y-2">
            <div
              v-for="d in preview?.detail_gejala ?? []" :key="d.kode_rule"
              class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0"
            >
              <div class="w-12 text-xs text-gray-400 font-mono">{{ d.kode_rule }}</div>
              <div class="flex-1 text-sm text-gray-700 min-w-0 truncate">{{ d.nama_gejala }}</div>
              <div class="flex items-center gap-3 text-xs flex-shrink-0">
                <span class="text-blue-500 w-16">MB: {{ d.mb }}</span>
                <span class="text-orange-500 w-16">MD: {{ d.md }}</span>
                <span class="font-medium w-20" :class="d.gejala_aktif ? 'text-red-500' : 'text-gray-300'">
                  CF: {{ d.cf_pakar.toFixed(2) }}
                </span>
                <span v-if="d.gejala_aktif" class="w-5">✅</span>
                <span v-else class="w-5 text-gray-200">—</span>
              </div>
            </div>
          </div>
          <div v-if="preview" class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
            <div class="text-sm font-medium text-gray-700">CF Kombinasi Final</div>
            <div class="text-lg font-bold" :class="cfColor(preview.cf_kombinasi)">
              {{ preview.cf_persen }}
            </div>
          </div>
        </div>

        <!-- Rekomendasi -->
        <div v-if="preview?.rekomendasi" class="col-span-2 p-4 rounded-xl border"
          :class="{
            'bg-red-50 border-red-200':    preview.status_stunting === 'stunting_berat',
            'bg-amber-50 border-amber-200': preview.status_stunting === 'stunting',
            'bg-yellow-50 border-yellow-200': preview.status_stunting === 'berisiko',
            'bg-green-50 border-green-200': preview.status_stunting === 'normal',
          }"
        >
          <div class="text-sm font-medium mb-1 text-gray-700">💡 Rekomendasi sistem</div>
          <div class="text-sm text-gray-600">{{ preview.rekomendasi }}</div>
        </div>
      </div>

      <!-- Action -->
      <div class="flex justify-end gap-3 mt-5">
        <Link :href="route('kader.kunjungan.index')" class="px-4 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50">
          Batalkan
        </Link>
        <button
          @click="simpanDiagnosis"
          :disabled="simpanLoading"
          class="px-5 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50 font-medium"
        >
          {{ simpanLoading ? 'Menyimpan...' : 'Simpan Diagnosis ✓' }}
        </button>
      </div>
    </div>
  </KaderLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import axios from 'axios'
import KaderLayout from '@/Layouts/KaderLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

const props = defineProps({
  kunjungan: Object,
  balita: Object,
  gejala_manual: Array,
  semua_gejala: Array,
  gejala_terdeteksi: Object,
})

const gejalaManu = ref({})
const preview = ref(null)
const loading = ref(false)
const simpanLoading = ref(false)

const gejalOtomatis = computed(() =>
  props.semua_gejala?.filter(g => g.sumber === 'otomatis') ?? []
)

const toggleGejala = async (id) => {
  gejalaManu.value[id] = !gejalaManu.value[id]
  await fetchPreview()
}

const fetchPreview = async () => {
  loading.value = true
  try {
    const res = await axios.post(
      route('kader.diagnosis.preview', props.kunjungan.id),
      { gejala: gejalaManu.value }
    )
    preview.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const simpanDiagnosis = () => {
  simpanLoading.value = true
  router.post(route('kader.diagnosis.store', props.kunjungan.id), {
    gejala: gejalaManu.value
  })
}

// Load preview awal
fetchPreview()

// Helpers warna
const zscoreColor = (z) => {
  if (z == null) return 'text-gray-400'
  if (z < -2) return 'text-red-600'
  if (z < -1) return 'text-amber-600'
  return 'text-green-600'
}
const cfColor = (cf) => {
  if (cf >= 0.7) return 'text-red-600'
  if (cf >= 0.4) return 'text-amber-600'
  return 'text-green-600'
}
const cfBarColor = (cf) => {
  if (cf >= 0.7) return 'bg-red-400'
  if (cf >= 0.4) return 'bg-amber-400'
  return 'bg-emerald-400'
}
const statusBg = (s) => ({
  'Stunting berat': 'bg-red-100', 'Stunting': 'bg-red-50',
  'Berisiko': 'bg-amber-50', 'Normal': 'bg-green-50'
}[s] ?? 'bg-gray-50')
const statusText = (s) => ({
  'Stunting berat': 'text-red-800', 'Stunting': 'text-red-600',
  'Berisiko': 'text-amber-600', 'Normal': 'text-green-600'
}[s] ?? 'text-gray-600')
</script>