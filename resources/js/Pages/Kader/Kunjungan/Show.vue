<template>
  <KaderLayout title="Detail Kunjungan">
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-5">
      <Link :href="route('kader.kunjungan.index')" class="hover:text-gray-600">Kunjungan</Link>
      <span>›</span>
      <span class="text-gray-600">{{ balita.nama }} — {{ kunjungan.tanggal }}</span>
    </div>

    <div class="grid grid-cols-3 gap-4">
      <!-- Kolom kiri -->
      <div class="space-y-4">
        <!-- Info balita -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-xl">👶</div>
            <div>
              <div class="font-semibold text-gray-800">{{ balita.nama }}</div>
              <div class="text-xs text-gray-400">{{ balita.usia_format }} · {{ balita.jenis_kelamin }}</div>
            </div>
          </div>
          <div class="text-xs text-gray-400">
            <div>Ibu: {{ balita.nama_ibu }}</div>
            <div v-if="balita.desa">Desa: {{ balita.desa }}</div>
          </div>
          <Link :href="route('kader.balita.show', balita.id)"
            class="mt-3 block text-center text-xs text-emerald-600 border border-emerald-200 py-1.5 rounded-lg hover:bg-emerald-50">
            Lihat profil lengkap →
          </Link>
        </div>

        <!-- Data pengukuran -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
          <div class="text-sm font-medium text-gray-700 mb-3">📏 Data Pengukuran</div>
          <div class="space-y-2.5 text-sm">
            <div class="flex justify-between"><span class="text-gray-400">Tanggal</span><span class="font-medium">{{ kunjungan.tanggal }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Usia</span><span class="font-medium">{{ kunjungan.usia_bulan }} bulan</span></div>
            <div class="border-t border-gray-100 pt-2">
              <div class="flex justify-between mb-1.5"><span class="text-gray-400">Berat badan</span><span class="font-semibold">{{ kunjungan.berat_badan }} kg</span></div>
              <div class="flex justify-between mb-1.5"><span class="text-gray-400">Tinggi badan</span><span class="font-semibold">{{ kunjungan.tinggi_badan }} cm</span></div>
              <div class="flex justify-between mb-1.5"><span class="text-gray-400">LILA</span><span class="font-semibold">{{ kunjungan.lila ? kunjungan.lila + ' cm' : '—' }}</span></div>
              <div class="flex justify-between"><span class="text-gray-400">Lingkar kepala</span><span class="font-semibold">{{ kunjungan.lingkar_kepala ? kunjungan.lingkar_kepala + ' cm' : '—' }}</span></div>
            </div>
          </div>
        </div>

        <!-- Z-score -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
          <div class="text-sm font-medium text-gray-700 mb-3">📊 Hasil Z-score (WHO 2006)</div>
          <div class="space-y-3">
            <div>
              <div class="flex justify-between items-center mb-1">
                <span class="text-xs text-gray-500">Z-score TB/U</span>
                <span class="text-sm font-bold" :class="zColor(kunjungan.zscore_tbu)">{{ kunjungan.zscore_tbu?.toFixed(2) ?? '—' }} SD</span>
              </div>
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full" :class="zBarColor(kunjungan.zscore_tbu)"
                  :style="{ width: Math.min(100, Math.max(2, (kunjungan.zscore_tbu + 3) / 6 * 100)) + '%' }"></div>
              </div>
              <div class="text-xs mt-1 font-medium" :class="zColor(kunjungan.zscore_tbu)">{{ kunjungan.status_zscore }}</div>
            </div>
            <div v-if="kunjungan.zscore_bbu != null">
              <div class="flex justify-between items-center mb-1">
                <span class="text-xs text-gray-500">Z-score BB/U</span>
                <span class="text-sm font-bold" :class="zColor(kunjungan.zscore_bbu)">{{ kunjungan.zscore_bbu?.toFixed(2) }} SD</span>
              </div>
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full" :class="zBarColor(kunjungan.zscore_bbu)"
                  :style="{ width: Math.min(100, Math.max(2, (kunjungan.zscore_bbu + 3) / 6 * 100)) + '%' }"></div>
              </div>
            </div>
          </div>
          <div class="mt-3 pt-3 border-t border-gray-100 space-y-1">
            <div class="text-xs text-gray-400 font-medium mb-1">Referensi kategori:</div>
            <div class="flex items-center gap-2 text-xs"><div class="w-2 h-2 rounded-full bg-red-500"></div><span class="text-gray-500">Di bawah −3 SD → Stunting berat</span></div>
            <div class="flex items-center gap-2 text-xs"><div class="w-2 h-2 rounded-full bg-red-400"></div><span class="text-gray-500">−3 s/d −2 SD → Stunting</span></div>
            <div class="flex items-center gap-2 text-xs"><div class="w-2 h-2 rounded-full bg-amber-400"></div><span class="text-gray-500">−2 s/d −1 SD → Berisiko</span></div>
            <div class="flex items-center gap-2 text-xs"><div class="w-2 h-2 rounded-full bg-emerald-400"></div><span class="text-gray-500">Di atas −1 SD → Normal</span></div>
          </div>
        </div>

        <div class="text-xs text-gray-400 text-center">Dicatat oleh: <strong class="text-gray-600">{{ kunjungan.kader }}</strong></div>
      </div>

      <!-- Kolom tengah + kanan -->
      <div class="col-span-2 space-y-4">

        <!-- Tren 6 bulan -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
          <div class="text-sm font-medium text-gray-700 mb-4">📈 Tren Pertumbuhan — 6 Bulan Terakhir</div>
          <div v-if="tren.length === 0" class="text-sm text-gray-400 text-center py-4">Belum ada data riwayat</div>
          <div v-else>
            <div class="mb-4">
              <div class="text-xs text-gray-400 font-medium mb-2">Berat Badan (kg)</div>
              <div class="flex items-end gap-2 h-20">
                <div v-for="(t, i) in tren" :key="t.bulan" class="flex-1 flex flex-col items-center gap-1">
                  <div class="text-gray-600" style="font-size:10px">{{ t.berat_badan }}</div>
                  <div class="w-full rounded-t-sm"
                    :class="i > 0 && t.berat_badan < tren[i-1].berat_badan ? 'bg-red-300' : 'bg-emerald-300'"
                    :style="{ height: (t.berat_badan / Math.max(...tren.map(x=>x.berat_badan)) * 64) + 'px' }"></div>
                  <div class="text-gray-400" style="font-size:9px">{{ t.bulan.split(' ')[0] }}</div>
                </div>
              </div>
            </div>
            <div class="space-y-2">
              <div class="text-xs text-gray-400 font-medium mb-2">Z-score TB/U per bulan</div>
              <div v-for="t in tren" :key="t.bulan" class="flex items-center gap-3">
                <div class="text-xs text-gray-400 w-14 flex-shrink-0">{{ t.bulan.split(' ')[0] }}</div>
                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                  <div class="h-full rounded-full" :class="zBarColor(t.zscore_tbu)"
                    :style="{ width: Math.min(100, Math.max(2, (t.zscore_tbu + 3) / 6 * 100)) + '%' }"></div>
                </div>
                <span class="text-xs font-medium w-14 text-right" :class="zColor(t.zscore_tbu)">{{ t.zscore_tbu?.toFixed(1) }} SD</span>
                <span class="text-xs w-14 text-right text-gray-500">{{ t.berat_badan }} kg</span>
                <span class="text-xs w-20 text-right px-1.5 py-0.5 rounded-full"
                  :class="{ 'bg-red-100 text-red-700': ['Stunting berat','Stunting'].includes(t.status), 'bg-amber-100 text-amber-700': t.status === 'Berisiko', 'bg-green-100 text-green-700': t.status === 'Normal' }">
                  {{ t.status }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Status diagnosis -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
          <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-medium text-gray-700">🩺 Status Diagnosis</div>
            <span v-if="diagnosis?.sudah_diverifikasi" class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">✓ Diverifikasi bidan</span>
          </div>

          <div v-if="diagnosis">
            <div class="rounded-xl p-4 mb-3 border"
              :class="{ 'bg-red-50 border-red-200': ['stunting','stunting_berat'].includes(diagnosis.status_final), 'bg-amber-50 border-amber-200': diagnosis.status_final === 'berisiko', 'bg-green-50 border-green-200': diagnosis.status_final === 'normal' }">
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-lg font-bold mb-1" :class="cfColor(diagnosis.cf_kombinasi)">{{ diagnosis.label_status }}</div>
                  <div class="text-sm text-gray-500">Nilai CF: <strong>{{ diagnosis.cf_persen }}</strong></div>
                </div>
                <div class="text-4xl font-bold" :class="cfColor(diagnosis.cf_kombinasi)">{{ diagnosis.cf_persen }}</div>
              </div>
              <div class="mt-3">
                <div class="h-2 bg-white bg-opacity-60 rounded-full overflow-hidden">
                  <div class="h-full rounded-full" :class="cfBarColor(diagnosis.cf_kombinasi)"
                    :style="{ width: (diagnosis.cf_kombinasi * 100) + '%' }"></div>
                </div>
              </div>
              <div v-if="diagnosis.rekomendasi" class="mt-3 text-xs text-gray-600 bg-white bg-opacity-60 rounded-lg p-2">
                {{ diagnosis.rekomendasi }}
              </div>
            </div>
            <Link :href="route('kader.diagnosis.show', diagnosis.id)"
              class="block text-center py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50">
              Lihat detail perhitungan CF →
            </Link>
          </div>

          <div v-else class="text-center py-8">
            <div class="text-4xl mb-3">🔍</div>
            <div class="text-sm text-gray-500 mb-4">Kunjungan ini belum dijalankan diagnosis</div>
            <Link :href="route('kader.diagnosis.create', kunjungan.id)"
              class="inline-block px-5 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium">
              Jalankan Diagnosis →
            </Link>
          </div>
        </div>

        <div v-if="kunjungan.catatan" class="bg-white rounded-xl border border-gray-200 p-4">
          <div class="text-sm font-medium text-gray-700 mb-2">📝 Catatan Kunjungan</div>
          <div class="text-sm text-gray-600 leading-relaxed">{{ kunjungan.catatan }}</div>
        </div>
      </div>
    </div>
  </KaderLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import KaderLayout from '@/Layouts/KaderLayout.vue'

defineProps({ kunjungan: Object, balita: Object, diagnosis: Object, tren: Array })

const zColor    = (z) => z == null ? 'text-gray-400' : z < -2 ? 'text-red-600' : z < -1 ? 'text-amber-600' : 'text-green-600'
const zBarColor = (z) => z == null ? 'bg-gray-200' : z < -2 ? 'bg-red-400' : z < -1 ? 'bg-amber-400' : 'bg-emerald-400'
const cfColor   = (cf) => cf >= 0.7 ? 'text-red-600' : cf >= 0.4 ? 'text-amber-600' : 'text-green-600'
const cfBarColor = (cf) => cf >= 0.7 ? 'bg-red-400' : cf >= 0.4 ? 'bg-amber-400' : 'bg-emerald-400'
</script>