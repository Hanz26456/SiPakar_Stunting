<template>
  <OrtuLayout title="Beranda">
    <div class="px-4 py-4">

      <!-- Greeting -->
      <div class="mb-4">
        <div class="text-xs text-gray-400">Selamat datang,</div>
        <div class="text-base font-semibold text-gray-800">{{ $page.props.auth.user.name }}</div>
      </div>

      <!-- Kartu balita -->
      <div v-for="b in balita" :key="b.id" class="mb-4">
        <!-- Status hero -->
        <div
          class="rounded-xl p-4 mb-3 border"
          :class="{
            'bg-red-50 border-red-200':    ['stunting','stunting_berat'].includes(b.status_terbaru),
            'bg-amber-50 border-amber-200': b.status_terbaru === 'berisiko',
            'bg-green-50 border-green-200': b.status_terbaru === 'normal',
            'bg-gray-50 border-gray-200':   !b.status_terbaru,
          }"
        >
          <div class="flex items-start justify-between mb-3">
            <div>
              <div class="font-semibold text-gray-800">{{ b.nama }}</div>
              <div class="text-sm text-gray-500">{{ b.usia_format }} · {{ b.jenis_kelamin }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-white bg-opacity-60 flex items-center justify-center text-xl">👶</div>
          </div>

          <div v-if="b.status_terbaru">
            <div class="flex items-center gap-2 mb-2">
              <span
                class="text-base font-bold"
                :class="{
                  'text-red-600':   ['stunting','stunting_berat'].includes(b.status_terbaru),
                  'text-amber-600': b.status_terbaru === 'berisiko',
                  'text-green-600': b.status_terbaru === 'normal',
                }"
              >{{ b.label_status }}</span>
              <span class="text-sm text-gray-400">· CF {{ b.cf_persen }}</span>
            </div>

            <!-- Progress bar CF -->
            <div class="h-2 bg-white bg-opacity-60 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all"
                :class="{
                  'bg-red-400':   ['stunting','stunting_berat'].includes(b.status_terbaru),
                  'bg-amber-400': b.status_terbaru === 'berisiko',
                  'bg-green-400': b.status_terbaru === 'normal',
                }"
                :style="{ width: (b.cf_persen?.replace('%','') ?? 0) + '%' }"
              ></div>
            </div>

            <div v-if="b.kunjungan_terakhir" class="text-xs text-gray-400 mt-2">
              Diperiksa {{ b.kunjungan_terakhir }}
            </div>
          </div>

          <div v-else class="text-sm text-gray-400">Belum ada data diagnosis</div>
        </div>

        <!-- Ukuran terbaru -->
        <div class="grid grid-cols-3 gap-2 mb-3">
          <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
            <div class="text-base font-semibold text-gray-800">{{ b.berat_terbaru ?? '—' }}</div>
            <div class="text-xs text-gray-400 mt-0.5">BB (kg)</div>
            <div class="text-xs" :class="b.berat_terbaru ? 'text-gray-300' : 'text-gray-200'">▼</div>
          </div>
          <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
            <div class="text-base font-semibold text-gray-800">{{ b.tinggi_terbaru ?? '—' }}</div>
            <div class="text-xs text-gray-400 mt-0.5">TB (cm)</div>
          </div>
          <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
            <div
              class="text-base font-semibold"
              :class="{
                'text-red-600': b.zscore_tbu < -2,
                'text-amber-600': b.zscore_tbu >= -2 && b.zscore_tbu < -1,
                'text-green-600': b.zscore_tbu >= -1,
                'text-gray-400': b.zscore_tbu == null,
              }"
            >{{ b.zscore_tbu?.toFixed(1) ?? '—' }}</div>
            <div class="text-xs text-gray-400 mt-0.5">Z-score</div>
          </div>
        </div>

        <!-- Rekomendasi -->
        <div
          v-if="b.status_terbaru && b.status_terbaru !== 'normal'"
          class="bg-white rounded-xl border border-gray-200 p-4 mb-3"
        >
          <div class="text-sm font-medium text-gray-700 mb-2">Yang perlu dilakukan:</div>
          <div v-if="['stunting','stunting_berat'].includes(b.status_terbaru)" class="space-y-2">
            <div class="flex items-start gap-2 text-sm text-gray-600">
              <span class="text-red-500 flex-shrink-0">🏥</span>
              <span>Segera periksakan ke Puskesmas Pujer. Bawa buku KMS dan KIA.</span>
            </div>
            <div class="flex items-start gap-2 text-sm text-gray-600">
              <span class="text-amber-500 flex-shrink-0">🥗</span>
              <span>Berikan makanan tinggi protein: telur, ikan, tahu, tempe setiap hari.</span>
            </div>
          </div>
          <div v-else class="flex items-start gap-2 text-sm text-gray-600">
            <span class="text-amber-500 flex-shrink-0">⚠️</span>
            <span>Pantau pertumbuhan lebih ketat. Pastikan asupan gizi seimbang setiap hari.</span>
          </div>
        </div>

        <!-- Tombol detail -->
        <Link
          :href="route('ortu.anak.show', b.id)"
          class="block w-full text-center py-2.5 text-sm bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-medium"
        >
          Lihat tumbuh kembang lengkap →
        </Link>
      </div>

      <!-- Kalau tidak ada balita -->
      <div v-if="balita.length === 0" class="text-center py-12">
        <div class="text-4xl mb-3">👶</div>
        <div class="text-gray-500 text-sm">Data anak belum tersedia</div>
        <div class="text-gray-400 text-xs mt-1">Hubungi kader posyandu untuk mendaftarkan anak Anda</div>
      </div>
    </div>
  </OrtuLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import OrtuLayout from '@/Layouts/OrtuLayout.vue'
defineProps({ balita: Array })
</script>