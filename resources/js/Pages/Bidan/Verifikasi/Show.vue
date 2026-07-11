<template>
  <BidanLayout title="Verifikasi Diagnosis">
    <div class="max-w-5xl mx-auto">

      <!-- Header -->
      <div class="flex items-center justify-between mb-5">
        <Link :href="route('bidan.monitoring')" class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1">
          ← Kembali ke monitoring
        </Link>
        <span v-if="diagnosis.sudah_diverifikasi" class="text-xs bg-blue-50 text-blue-700 px-3 py-1 rounded-full">
          ✓ Sudah diverifikasi
        </span>
        <span v-else class="text-xs bg-amber-50 text-amber-700 px-3 py-1 rounded-full animate-pulse">
          ⏳ Menunggu verifikasi
        </span>
      </div>

      <div class="grid grid-cols-3 gap-4">

        <!-- Kolom kiri: data balita + CF -->
        <div class="col-span-2 space-y-4">

          <!-- Info balita -->
          <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-lg">👶</div>
              <div>
                <div class="font-medium text-gray-800">{{ balita.nama }}</div>
                <div class="text-sm text-gray-400">{{ balita.usia_format }} · {{ balita.jenis_kelamin }} · {{ balita.desa }}</div>
              </div>
              <div class="ml-auto text-right">
                <div class="text-xs text-gray-400">Kunjungan</div>
                <div class="text-sm font-medium text-gray-700">{{ kunjungan.tanggal }}</div>
              </div>
            </div>

            <!-- Antropometri -->
            <div class="grid grid-cols-4 gap-3">
              <div class="text-center p-2 bg-gray-50 rounded-lg">
                <div class="text-base font-semibold text-gray-800">{{ kunjungan.berat_badan }}</div>
                <div class="text-xs text-gray-400">BB (kg)</div>
              </div>
              <div class="text-center p-2 bg-gray-50 rounded-lg">
                <div class="text-base font-semibold text-gray-800">{{ kunjungan.tinggi_badan }}</div>
                <div class="text-xs text-gray-400">TB (cm)</div>
              </div>
              <div class="text-center p-2 bg-gray-50 rounded-lg">
                <div class="text-base font-semibold" :class="zColor(kunjungan.zscore_tbu)">
                  {{ kunjungan.zscore_tbu?.toFixed(2) }}
                </div>
                <div class="text-xs text-gray-400">Z-score TB/U</div>
              </div>
              <div class="text-center p-2 bg-gray-50 rounded-lg">
                <div class="text-base font-semibold text-gray-800">{{ kunjungan.lila ?? '-' }}</div>
                <div class="text-xs text-gray-400">LILA (cm)</div>
              </div>
            </div>
          </div>

          <!-- Detail CF -->
          <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-sm font-medium text-gray-700 mb-3">🔢 Detail perhitungan CF sistem</div>
            <div class="space-y-1.5">
              <div
                v-for="d in detail_gejala" :key="d.kode_rule"
                class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0"
                :class="d.gejala_aktif ? 'opacity-100' : 'opacity-35'"
              >
                <div class="w-12 text-xs text-gray-400 font-mono flex-shrink-0">{{ d.kode_rule }}</div>
                <div class="flex-1 text-sm text-gray-700 min-w-0 truncate">{{ d.nama_gejala }}</div>
                <div class="flex items-center gap-2 text-xs flex-shrink-0">
                  <span class="bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded text-xs">{{ d.sumber }}</span>
                  <span class="text-blue-500">MB {{ d.mb }}</span>
                  <span class="text-orange-500">MD {{ d.md }}</span>
                  <span class="font-medium w-16 text-right" :class="d.gejala_aktif ? 'text-red-500' : 'text-gray-300'">
                    CF {{ d.cf_pakar.toFixed(2) }}
                  </span>
                  <span class="w-4">{{ d.gejala_aktif ? '✓' : '✗' }}</span>
                </div>
              </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between">
              <span class="text-sm font-medium text-gray-700">CF Kombinasi</span>
              <span class="text-xl font-bold" :class="cfColor(diagnosis.cf_kombinasi)">{{ diagnosis.cf_persen }}</span>
            </div>
          </div>

          <!-- Tren pertumbuhan mini -->
          <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-sm font-medium text-gray-700 mb-3">📈 Tren pertumbuhan 6 bulan terakhir</div>
            <div class="space-y-2">
              <div v-for="t in tren" :key="t.bulan" class="flex items-center gap-3">
                <div class="text-xs text-gray-400 w-16 flex-shrink-0">{{ t.bulan }}</div>
                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                  <div
                    class="h-full rounded-full transition-all"
                    :class="zBarColor(t.zscore_tbu)"
                    :style="{ width: Math.min(100, Math.max(0, (t.zscore_tbu + 3) / 6 * 100)) + '%' }"
                  ></div>
                </div>
                <div class="text-xs font-medium w-16 text-right flex-shrink-0" :class="zColor(t.zscore_tbu)">
                  {{ t.zscore_tbu?.toFixed(1) }} SD
                </div>
                <div class="text-xs text-gray-400 w-16 text-right flex-shrink-0">{{ t.berat_badan }} kg</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Kolom kanan: form verifikasi -->
        <div class="space-y-4">

          <!-- Hasil sistem -->
          <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-xs text-gray-400 mb-1">Hasil sistem</div>
            <div class="flex items-center gap-2 mb-1">
              <StatusBadge :status="diagnosis.status_stunting" :label="diagnosis.label_status" />
              <span class="text-sm font-bold" :class="cfColor(diagnosis.cf_kombinasi)">{{ diagnosis.cf_persen }}</span>
            </div>
            <div class="text-xs text-gray-400">Oleh: {{ diagnosis.kader }}</div>
          </div>

          <!-- Form override -->
          <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-sm font-medium text-gray-700 mb-3">✏️ Verifikasi bidan</div>

            <form @submit.prevent="submit" class="space-y-3">
              <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Koreksi status (opsional)</label>
                <select
                  v-model="form.status_override"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                >
                  <option value="">— Ikuti hasil sistem —</option>
                  <option value="normal">Normal</option>
                  <option value="berisiko">Berisiko</option>
                  <option value="stunting">Stunting</option>
                  <option value="stunting_berat">Stunting Berat</option>
                </select>
              </div>

              <div v-if="form.status_override">
                <label class="text-xs font-medium text-gray-500 mb-1 block">Alasan koreksi</label>
                <input
                  v-model="form.alasan_override"
                  type="text"
                  placeholder="Catatan klinis..."
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                >
              </div>

              <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Tindak lanjut</label>
                <select
                  v-model="form.tindak_lanjut"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                >
                  <option value="">— Pilih tindakan —</option>
                  <option value="pantau">Pantau ketat</option>
                  <option value="edukasi_gizi">Edukasi gizi</option>
                  <option value="pmt">Pemberian PMT</option>
                  <option value="rujuk_puskesmas">Rujuk Puskesmas</option>
                  <option value="rujuk_rsud">Rujuk RSUD</option>
                </select>
              </div>

              <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Jadwal kontrol</label>
                <input
                  v-model="form.jadwal_kontrol"
                  type="date"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                >
              </div>

              <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Catatan bidan</label>
                <textarea
                  v-model="form.catatan_bidan"
                  rows="3"
                  placeholder="Catatan klinis tambahan..."
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-300"
                ></textarea>
              </div>

              <div class="flex gap-2 pt-1">
                <button
                  type="button"
                  @click="$inertia.visit(route('bidan.monitoring'))"
                  class="flex-1 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="flex-1 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 font-medium"
                >
                  {{ form.processing ? 'Menyimpan...' : 'Verifikasi ✓' }}
                </button>
              </div>
            </form>
          </div>

          <!-- Link pasien lengkap -->
          <Link
            :href="route('bidan.pasien.show', balita.id)"
            class="block bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm text-center text-gray-500 hover:bg-gray-100"
          >
            Lihat riwayat lengkap pasien →
          </Link>
        </div>
      </div>
    </div>
  </BidanLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import BidanLayout from '@/Layouts/BidanLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

const props = defineProps({
  diagnosis: Object,
  detail_gejala: Array,
  balita: Object,
  kunjungan: Object,
  tren: Array,
})

const form = useForm({
  status_override: props.diagnosis.status_override ?? '',
  alasan_override: props.diagnosis.alasan_override ?? '',
  catatan_bidan:   props.diagnosis.catatan_bidan ?? '',
  tindak_lanjut:   props.diagnosis.tindak_lanjut ?? '',
  jadwal_kontrol:  props.diagnosis.jadwal_kontrol ?? '',
})

const submit = () => {
  form.post(route('bidan.verifikasi.update', props.diagnosis.id))
}

const cfColor  = (cf) => cf >= 0.7 ? 'text-red-600' : cf >= 0.4 ? 'text-amber-600' : 'text-green-600'
const zColor   = (z)  => z == null ? 'text-gray-400' : z < -2 ? 'text-red-600' : z < -1 ? 'text-amber-600' : 'text-green-600'
const zBarColor = (z) => z == null ? 'bg-gray-200' : z < -2 ? 'bg-red-400' : z < -1 ? 'bg-amber-400' : 'bg-emerald-400'
</script>