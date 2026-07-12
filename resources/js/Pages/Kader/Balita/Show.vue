<template>
  <KaderLayout :title="balita.nama">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-5">
      <Link :href="route('kader.balita.index')" class="hover:text-gray-600">Data Balita</Link>
      <span>›</span>
      <span class="text-gray-600">{{ balita.nama }}</span>
    </div>

    <div class="grid grid-cols-3 gap-4">

      <!-- Kolom kiri: Info balita + Riwayat medis -->
      <div class="space-y-4">

        <!-- Kartu profil balita -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
          <div class="flex items-center gap-3 mb-4">
            <div
              class="w-12 h-12 rounded-full flex items-center justify-center text-xl flex-shrink-0"
              :class="balita.jenis_kelamin === 'Laki-laki' ? 'bg-blue-100' : 'bg-pink-100'"
            >
              {{ balita.jenis_kelamin === 'Laki-laki' ? '👦' : '👧' }}
            </div>
            <div class="flex-1">
              <div class="font-semibold text-gray-800">{{ balita.nama }}</div>
              <div class="text-sm text-gray-400">{{ balita.usia_format }} · {{ balita.jenis_kelamin }}</div>
            </div>
            <Link
              :href="route('kader.balita.edit', balita.id)"
              class="text-xs text-gray-400 hover:text-gray-600 border border-gray-200 px-2.5 py-1 rounded-lg"
            >
              ✏️ Edit
            </Link>
          </div>

          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-400">Tanggal lahir</span>
              <span class="text-gray-700">{{ balita.tanggal_lahir }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Nama ibu</span>
              <span class="text-gray-700">{{ balita.nama_ibu }}</span>
            </div>
            <div class="flex justify-between" v-if="balita.nama_ayah">
              <span class="text-gray-400">Nama ayah</span>
              <span class="text-gray-700">{{ balita.nama_ayah }}</span>
            </div>
            <div class="flex justify-between" v-if="balita.no_hp_ortu">
              <span class="text-gray-400">No. HP</span>
              <span class="text-gray-700">{{ balita.no_hp_ortu }}</span>
            </div>
            <div class="flex justify-between" v-if="balita.desa">
              <span class="text-gray-400">Desa</span>
              <span class="text-gray-700">{{ balita.desa }}</span>
            </div>
            <div class="flex justify-between" v-if="balita.alamat">
              <span class="text-gray-400">Alamat</span>
              <span class="text-gray-700 text-right max-w-32">{{ balita.alamat }}</span>
            </div>
          </div>
        </div>

        <!-- Riwayat medis -->
        <div v-if="riwayat" class="bg-white rounded-xl border border-gray-200 p-4">
          <div class="text-sm font-medium text-gray-700 mb-3">📋 Riwayat Medis & Gizi</div>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between items-center">
              <span class="text-gray-400">ASI eksklusif</span>
              <span :class="riwayat.asi_eksklusif ? 'text-green-600' : 'text-red-500'">
                {{ riwayat.asi_eksklusif ? '✅ Ya' : '❌ Tidak' }}
              </span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-gray-400">MPASI sesuai usia</span>
              <span :class="riwayat.mpasi_sesuai_usia ? 'text-green-600' : 'text-red-500'">
                {{ riwayat.mpasi_sesuai_usia ? '✅ Ya' : '❌ Tidak' }}
              </span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-gray-400">Infeksi berulang</span>
              <span :class="riwayat.infeksi_berulang ? 'text-red-500' : 'text-green-600'">
                {{ riwayat.infeksi_berulang ? '⚠️ Ada' : '✅ Tidak' }}
              </span>
            </div>
            <div v-if="riwayat.mulai_mpasi" class="flex justify-between">
              <span class="text-gray-400">Mulai MPASI</span>
              <span class="text-gray-700">{{ riwayat.mulai_mpasi }}</span>
            </div>
            <div v-if="riwayat.berat_lahir" class="flex justify-between">
              <span class="text-gray-400">Berat lahir</span>
              <span class="text-gray-700">{{ riwayat.berat_lahir }} kg</span>
            </div>
            <div v-if="riwayat.panjang_lahir" class="flex justify-between">
              <span class="text-gray-400">Panjang lahir</span>
              <span class="text-gray-700">{{ riwayat.panjang_lahir }} cm</span>
            </div>
          </div>
        </div>

        <!-- Tombol aksi -->
        <Link
          :href="route('kader.kunjungan.create-for-balita', balita.id)"
          class="block w-full text-center py-2.5 text-sm bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-medium"
        >
          + Input Kunjungan Bulan Ini
        </Link>
      </div>

      <!-- Kolom tengah + kanan: Tren + Riwayat kunjungan -->
      <div class="col-span-2 space-y-4">

        <!-- Grafik tren -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
          <div class="text-sm font-medium text-gray-700 mb-4">📈 Tren Tumbuh Kembang</div>
          <div v-if="tren.length === 0" class="text-sm text-gray-400 text-center py-6">
            Belum ada data kunjungan
          </div>
          <div v-else class="space-y-3">
            <div v-for="t in tren" :key="t.bulan" class="flex items-center gap-3">
              <div class="text-xs text-gray-400 w-16 flex-shrink-0">{{ t.bulan }}</div>
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-0.5">
                  <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div
                      class="h-full rounded-full transition-all"
                      :class="zBarColor(t.zscore_tbu)"
                      :style="{ width: Math.min(100, Math.max(5, (t.zscore_tbu + 3) / 6 * 100)) + '%' }"
                    ></div>
                  </div>
                  <span
                    class="text-xs font-medium w-14 text-right flex-shrink-0"
                    :class="zColor(t.zscore_tbu)"
                  >
                    {{ t.zscore_tbu?.toFixed(1) ?? '—' }} SD
                  </span>
                </div>
                <div class="text-xs text-gray-400">
                  BB: <strong class="text-gray-600">{{ t.berat_badan }} kg</strong>
                  · TB: <strong class="text-gray-600">{{ t.tinggi_badan }} cm</strong>
                  <span v-if="t.lila"> · LILA: <strong class="text-gray-600">{{ t.lila }} cm</strong></span>
                </div>
              </div>
              <span
                class="text-xs px-2 py-0.5 rounded-full flex-shrink-0"
                :class="{
                  'bg-red-100 text-red-700': t.status === 'Stunting berat' || t.status === 'Stunting',
                  'bg-amber-100 text-amber-700': t.status === 'Berisiko',
                  'bg-green-100 text-green-700': t.status === 'Normal',
                }"
              >{{ t.status }}</span>
            </div>
          </div>
        </div>

        <!-- Riwayat kunjungan -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
          <div class="flex items-center justify-between p-4 border-b border-gray-100">
            <div class="text-sm font-medium text-gray-700">🗓️ Riwayat Kunjungan</div>
            <span class="text-xs text-gray-400">{{ riwayat_kunjungan.length }} kunjungan</span>
          </div>
          <div v-if="riwayat_kunjungan.length === 0" class="p-8 text-center text-gray-400 text-sm">
            Belum ada riwayat kunjungan
          </div>
          <table v-else class="w-full text-sm">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100">
                <th class="text-left py-2.5 px-4 text-xs text-gray-400 font-medium">Tanggal</th>
                <th class="text-left py-2.5 px-4 text-xs text-gray-400 font-medium">Usia</th>
                <th class="text-left py-2.5 px-4 text-xs text-gray-400 font-medium">BB / TB</th>
                <th class="text-left py-2.5 px-4 text-xs text-gray-400 font-medium">Z-score</th>
                <th class="text-left py-2.5 px-4 text-xs text-gray-400 font-medium">CF</th>
                <th class="text-left py-2.5 px-4 text-xs text-gray-400 font-medium">Status</th>
                <th class="text-left py-2.5 px-4 text-xs text-gray-400 font-medium">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="k in riwayat_kunjungan" :key="k.id"
                class="border-b border-gray-50 hover:bg-gray-50"
              >
                <td class="py-2.5 px-4 text-gray-700">{{ k.tanggal }}</td>
                <td class="py-2.5 px-4 text-gray-500 text-xs">{{ k.usia_bulan }} bln</td>
                <td class="py-2.5 px-4 text-gray-600 text-xs">
                  {{ k.berat_badan }} kg / {{ k.tinggi_badan }} cm
                </td>
                <td class="py-2.5 px-4 text-xs font-medium" :class="zColor(k.zscore_tbu)">
                  {{ k.zscore_tbu?.toFixed(2) ?? '—' }}
                </td>
                <td class="py-2.5 px-4 text-xs font-medium">
                  {{ k.cf ?? '—' }}
                </td>
                <td class="py-2.5 px-4">
                  <StatusBadge
                    v-if="k.status_cf"
                    :status="k.diagnosis?.status_final ?? ''"
                    :label="k.status_cf"
                  />
                  <span v-else class="text-xs text-gray-300">—</span>
                </td>
                <td class="py-2.5 px-4">
                  <div class="flex items-center gap-2">
                    <Link
                      v-if="k.diagnosis_id"
                      :href="route('kader.diagnosis.show', k.diagnosis_id)"
                      class="text-xs text-emerald-600 hover:underline"
                    >
                      Lihat →
                    </Link>
                    <Link
                      v-else
                      :href="route('kader.diagnosis.create', k.id)"
                      class="text-xs text-amber-600 hover:underline"
                    >
                      Diagnosa →
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </KaderLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import KaderLayout from '@/Layouts/KaderLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

defineProps({
  balita: Object,
  riwayat: Object,
  riwayat_kunjungan: Array,
  tren: Array,
})

const zColor = (z) => {
  if (z == null) return 'text-gray-400'
  if (z < -2) return 'text-red-600'
  if (z < -1) return 'text-amber-600'
  return 'text-green-600'
}

const zBarColor = (z) => {
  if (z == null) return 'bg-gray-200'
  if (z < -2) return 'bg-red-400'
  if (z < -1) return 'bg-amber-400'
  return 'bg-emerald-400'
}
</script>