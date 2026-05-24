<template>
  <KaderLayout title="Hasil Diagnosis">
    <div class="max-w-4xl mx-auto">

      <!-- Header hasil -->
      <div
        class="rounded-xl p-5 mb-5 border"
        :class="{
          'bg-red-50 border-red-200':    diagnosis.status_final === 'stunting_berat',
          'bg-red-50 border-red-200':    diagnosis.status_final === 'stunting',
          'bg-amber-50 border-amber-200':diagnosis.status_final === 'berisiko',
          'bg-green-50 border-green-200':diagnosis.status_final === 'normal',
        }"
      >
        <div class="flex items-center justify-between">
          <div>
            <div class="text-lg font-semibold text-gray-800 mb-1">
              {{ balita.nama }} — {{ kunjungan.tanggal }}
            </div>
            <div class="flex items-center gap-3">
              <StatusBadge :status="diagnosis.status_final" :label="diagnosis.label_status" />
              <span class="text-sm text-gray-500">CF: <strong>{{ diagnosis.cf_persen }}</strong></span>
              <span v-if="diagnosis.sudah_diverifikasi" class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                ✓ Diverifikasi {{ diagnosis.verifikator }}
              </span>
              <span v-else class="text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                ⏳ Menunggu verifikasi bidan
              </span>
            </div>
          </div>
          <div class="text-5xl font-bold" :class="cfColor(diagnosis.cf_kombinasi)">
            {{ diagnosis.cf_persen }}
          </div>
        </div>

        <!-- Rekomendasi -->
        <div class="mt-4 p-3 bg-white bg-opacity-60 rounded-lg">
          <div class="text-xs font-medium text-gray-500 mb-1">Rekomendasi</div>
          <div class="text-sm text-gray-700">{{ diagnosis.rekomendasi }}</div>
        </div>

        <!-- Tindak lanjut (kalau sudah diverifikasi) -->
        <div v-if="diagnosis.tindak_lanjut" class="mt-3 flex items-center gap-3 text-sm text-gray-600">
          <span>📋 Tindak lanjut: <strong>{{ tindakLanjutLabel }}</strong></span>
          <span v-if="diagnosis.jadwal_kontrol">· Kontrol: <strong>{{ diagnosis.jadwal_kontrol }}</strong></span>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <!-- Data kunjungan -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
          <div class="text-sm font-medium text-gray-700 mb-3">📏 Data pengukuran</div>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-400">Berat badan</span><span class="font-medium">{{ kunjungan.berat_badan }} kg</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Tinggi badan</span><span class="font-medium">{{ kunjungan.tinggi_badan }} cm</span></div>
            <div class="flex justify-between"><span class="text-gray-400">LILA</span><span class="font-medium">{{ kunjungan.lila ?? '-' }} cm</span></div>
            <div class="flex justify-between border-t pt-2">
              <span class="text-gray-400">Z-score TB/U</span>
              <span class="font-medium" :class="zscoreColor(kunjungan.zscore_tbu)">{{ kunjungan.zscore_tbu?.toFixed(2) ?? '-' }} SD</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Z-score BB/U</span>
              <span class="font-medium">{{ kunjungan.zscore_bbu?.toFixed(2) ?? '-' }} SD</span>
            </div>
          </div>
        </div>

        <!-- Catatan bidan (kalau ada) -->
        <div v-if="diagnosis.catatan_bidan" class="bg-blue-50 rounded-xl border border-blue-200 p-4">
          <div class="text-sm font-medium text-blue-700 mb-2">👩‍⚕️ Catatan bidan</div>
          <div class="text-sm text-gray-700">{{ diagnosis.catatan_bidan }}</div>
          <div v-if="diagnosis.alasan_override" class="mt-2 text-xs text-blue-600">
            Alasan koreksi: {{ diagnosis.alasan_override }}
          </div>
        </div>

        <!-- Detail CF -->
        <div class="col-span-2 bg-white rounded-xl border border-gray-200 p-4">
          <div class="text-sm font-medium text-gray-700 mb-3">🔢 Detail perhitungan CF</div>
          <div class="space-y-1.5">
            <div
              v-for="d in detail_gejala" :key="d.kode_rule"
              class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0"
              :class="d.gejala_aktif ? 'opacity-100' : 'opacity-40'"
            >
              <div class="w-12 text-xs text-gray-400 font-mono flex-shrink-0">{{ d.kode_rule }}</div>
              <div class="flex-1 text-sm text-gray-700 min-w-0">{{ d.nama_gejala }}</div>
              <div class="flex items-center gap-3 text-xs flex-shrink-0">
                <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-500">{{ d.sumber }}</span>
                <span class="text-blue-500">MB: {{ d.mb }}</span>
                <span class="text-orange-500">MD: {{ d.md }}</span>
                <span class="font-medium w-20 text-right" :class="d.gejala_aktif ? 'text-red-500' : 'text-gray-300'">
                  CF: {{ d.cf_pakar.toFixed(2) }}
                </span>
                <div class="w-5 text-center">
                  <span v-if="d.gejala_aktif" class="text-green-500">✓</span>
                  <span v-else class="text-gray-300">✗</span>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between items-center">
            <div class="text-sm font-medium">CF Kombinasi</div>
            <div class="text-xl font-bold" :class="cfColor(diagnosis.cf_kombinasi)">
              {{ diagnosis.cf_persen }}
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-between mt-5">
        <Link :href="route('kader.balita.show', balita.id)" class="px-4 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50">
          ← Lihat profil balita
        </Link>
        <div class="flex gap-2">
          <button class="px-4 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50">
            🖨️ Cetak
          </button>
          <Link :href="route('kader.kunjungan.create')" class="px-5 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
            + Input kunjungan baru
          </Link>
        </div>
      </div>
    </div>
  </KaderLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import KaderLayout from '@/Layouts/KaderLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

const props = defineProps({
  diagnosis: Object,
  detail_gejala: Array,
  balita: Object,
  kunjungan: Object,
})

const tindakLanjutLabel = computed(() => ({
  pantau:           'Pantau ketat',
  edukasi_gizi:     'Edukasi gizi',
  pmt:              'Pemberian PMT',
  rujuk_puskesmas:  'Rujuk Puskesmas',
  rujuk_rsud:       'Rujuk RSUD',
}[props.diagnosis.tindak_lanjut] ?? '-'))

const cfColor = (cf) => {
  if (cf >= 0.7) return 'text-red-600'
  if (cf >= 0.4) return 'text-amber-600'
  return 'text-green-600'
}
const zscoreColor = (z) => {
  if (z == null) return 'text-gray-400'
  if (z < -2) return 'text-red-600'
  if (z < -1) return 'text-amber-600'
  return 'text-green-600'
}
</script>
