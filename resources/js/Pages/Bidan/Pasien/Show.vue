<template>
  <BidanLayout title="Detail Pasien">
    <div class="max-w-5xl mx-auto">

      <!-- Header balita -->
      <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-xl">👶</div>
          <div class="flex-1">
            <div class="text-lg font-semibold text-gray-800">{{ balita.nama }}</div>
            <div class="text-sm text-gray-400">
              {{ balita.usia_format }} · {{ balita.jenis_kelamin }} · {{ balita.desa }}
            </div>
            <div class="text-sm text-gray-400">Ibu: {{ balita.nama_ibu }}</div>
          </div>
          <div v-if="riwayat_kunjungan[0]?.diagnosis" class="text-right">
            <StatusBadge
              :status="riwayat_kunjungan[0].diagnosis.status_final"
              :label="riwayat_kunjungan[0].diagnosis.label_status"
            />
            <div class="text-xs text-gray-400 mt-1">Status terakhir</div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-4">

        <!-- Tren pertumbuhan -->
        <div class="col-span-2 space-y-4">
          <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-sm font-medium text-gray-700 mb-4">📈 Tren tumbuh kembang</div>
            <div class="space-y-3">
              <div v-for="t in tren" :key="t.bulan" class="flex items-center gap-3">
                <div class="text-xs text-gray-400 w-16 flex-shrink-0">{{ t.bulan }}</div>
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                      <div
                        class="h-full rounded-full transition-all"
                        :class="zBarColor(t.zscore_tbu)"
                        :style="{ width: Math.min(100, Math.max(5, (t.zscore_tbu + 3) / 6 * 100)) + '%' }"
                      ></div>
                    </div>
                    <span class="text-xs font-medium w-14 text-right flex-shrink-0" :class="zColor(t.zscore_tbu)">
                      {{ t.zscore_tbu?.toFixed(1) }} SD
                    </span>
                  </div>
                  <div class="flex gap-4 text-xs text-gray-400">
                    <span>BB: <strong class="text-gray-600">{{ t.berat_badan }} kg</strong></span>
                    <span>TB: <strong class="text-gray-600">{{ t.tinggi_badan }} cm</strong></span>
                    <span v-if="t.lila">LILA: <strong class="text-gray-600">{{ t.lila }} cm</strong></span>
                  </div>
                </div>
                <div class="text-xs flex-shrink-0">
                  <span
                    class="px-2 py-0.5 rounded-full"
                    :class="{
                      'bg-red-100 text-red-700':   t.status === 'Stunting berat' || t.status === 'Stunting',
                      'bg-amber-100 text-amber-700': t.status === 'Berisiko',
                      'bg-green-100 text-green-700': t.status === 'Normal',
                    }"
                  >{{ t.status }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Timeline riwayat -->
          <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-sm font-medium text-gray-700 mb-4">🗓️ Riwayat kunjungan & diagnosis</div>
            <div class="space-y-4">
              <div v-for="k in riwayat_kunjungan" :key="k.id" class="flex gap-3">
                <div class="flex flex-col items-center">
                  <div
                    class="w-3 h-3 rounded-full border-2 flex-shrink-0 mt-1"
                    :class="{
                      'bg-red-100 border-red-500':   k.warna === 'red',
                      'bg-amber-100 border-amber-500': k.warna === 'amber',
                      'bg-green-100 border-green-500': k.warna === 'green',
                    }"
                  ></div>
                  <div class="flex-1 w-px bg-gray-100 mt-1"></div>
                </div>
                <div class="flex-1 pb-4">
                  <div class="flex items-center gap-2 mb-1">
                    <span class="text-sm font-medium text-gray-700">{{ k.tanggal }}</span>
                    <span class="text-xs text-gray-400">usia {{ k.usia_bulan }} bln</span>
                    <span v-if="k.diagnosis?.verified" class="text-xs bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded">✓ terverifikasi</span>
                  </div>
                  <div class="text-xs text-gray-500 mb-1">
                    BB: {{ k.berat_badan }} kg · TB: {{ k.tinggi_badan }} cm · Z-score: {{ k.zscore_tbu?.toFixed(2) }} SD
                  </div>
                  <div v-if="k.diagnosis" class="flex items-center gap-2">
                    <StatusBadge :status="k.diagnosis.status_final" :label="k.diagnosis.label_status" />
                    <span class="text-xs text-gray-400">CF: {{ k.diagnosis.cf_persen }}</span>
                    <span v-if="k.diagnosis.tindak_lanjut" class="text-xs text-gray-400">
                      · {{ tindakLanjulLabel(k.diagnosis.tindak_lanjut) }}
                    </span>
                  </div>
                  <div v-else class="text-xs text-gray-300">Belum ada diagnosis</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Kolom kanan: riwayat medis -->
        <div class="space-y-4">
          <div v-if="riwayat" class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-sm font-medium text-gray-700 mb-3">📋 Riwayat medis</div>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-400">ASI eksklusif</span>
                <span :class="riwayat.asi_eksklusif ? 'text-green-600' : 'text-red-500'">
                  {{ riwayat.asi_eksklusif ? '✓ Ya' : '✗ Tidak' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">MPASI sesuai</span>
                <span :class="riwayat.mpasi_sesuai_usia ? 'text-green-600' : 'text-red-500'">
                  {{ riwayat.mpasi_sesuai_usia ? '✓ Ya' : '✗ Tidak' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Infeksi berulang</span>
                <span :class="riwayat.infeksi_berulang ? 'text-red-500' : 'text-green-600'">
                  {{ riwayat.infeksi_berulang ? '⚠️ Ya' : '✓ Tidak' }}
                </span>
              </div>
              <div class="flex justify-between" v-if="riwayat.berat_lahir">
                <span class="text-gray-400">Berat lahir</span>
                <span class="font-medium">{{ riwayat.berat_lahir }} kg</span>
              </div>
              <div class="flex justify-between" v-if="riwayat.mulai_mpasi">
                <span class="text-gray-400">Mulai MPASI</span>
                <span class="font-medium">{{ riwayat.mulai_mpasi }}</span>
              </div>
            </div>
          </div>

          <!-- Kontak orang tua -->
          <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-sm font-medium text-gray-700 mb-3">📞 Kontak orang tua</div>
            <div class="text-sm text-gray-600">{{ balita.nama_ibu }}</div>
            <div v-if="balita.no_hp_ortu" class="text-sm text-blue-600 mt-1">
              {{ balita.no_hp_ortu }}
            </div>
            <div v-else class="text-xs text-gray-400 mt-1">Nomor HP belum tersimpan</div>
          </div>

          <!-- Aksi cepat -->
          <div class="bg-white rounded-xl border border-gray-200 p-4 space-y-2">
            <div class="text-sm font-medium text-gray-700 mb-2">⚡ Aksi cepat</div>
            <Link
              v-if="riwayat_kunjungan[0]?.diagnosis && !riwayat_kunjungan[0].diagnosis.verified"
              :href="route('bidan.verifikasi.show', riwayat_kunjungan[0].diagnosis.id)"
              class="block w-full text-center py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              Verifikasi diagnosis terbaru →
            </Link>
            <button
              @click="cetakLaporan"
              class="block w-full text-center py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50"
            >
              🖨️ Cetak riwayat pasien
            </button>
          </div>
        </div>
      </div>
    </div>
  </BidanLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import BidanLayout from '@/Layouts/BidanLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

defineProps({
  balita: Object,
  riwayat: Object,
  riwayat_kunjungan: Array,
  tren: Array,
})

const tindakLanjulLabel = (t) => ({
  pantau: 'Pantau', edukasi_gizi: 'Edukasi',
  pmt: 'PMT', rujuk_puskesmas: 'Rujuk PKM', rujuk_rsud: 'Rujuk RSUD',
}[t] ?? '-')

const cetakLaporan = () => window.print()

const cfColor  = (cf) => cf >= 0.7 ? 'text-red-600' : cf >= 0.4 ? 'text-amber-600' : 'text-green-600'
const zColor   = (z)  => z == null ? 'text-gray-400' : z < -2 ? 'text-red-600' : z < -1 ? 'text-amber-600' : 'text-green-600'
const zBarColor = (z) => z == null ? 'bg-gray-200' : z < -2 ? 'bg-red-400' : z < -1 ? 'bg-amber-400' : 'bg-emerald-400'
</script>