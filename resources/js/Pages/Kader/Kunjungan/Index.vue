<template>
  <KaderLayout title="Daftar Kunjungan">
    <div class="flex items-center gap-3 mb-5">
      <div class="relative flex-1">
        <input v-model="search" type="text" placeholder="Cari nama balita..."
          class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 bg-white"
          @input="doFilter">
        <span class="absolute left-3 top-2.5 text-gray-400 text-sm">🔍</span>
      </div>
      <select v-model="filterBulan" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white" @change="doFilter">
        <option v-for="b in bulanOptions" :key="b.value" :value="b.value">{{ b.label }}</option>
      </select>
      <select v-model="filterStatus" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white" @change="doFilter">
        <option value="">Semua status</option>
        <option value="Normal">Normal</option>
        <option value="Berisiko">Berisiko</option>
        <option value="Stunting">Stunting</option>
        <option value="Stunting berat">Stunting Berat</option>
      </select>
      <Link :href="route('kader.kunjungan.create')"
        class="px-4 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 flex items-center gap-1.5 font-medium flex-shrink-0">
        <span>+</span> Input Kunjungan
      </Link>
    </div>

    <div class="grid grid-cols-4 gap-3 mb-5">
      <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
        <div class="text-2xl font-semibold text-gray-800">{{ kunjungan.total }}</div>
        <div class="text-xs text-gray-400 mt-1">Total kunjungan</div>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
        <div class="text-2xl font-semibold text-emerald-600">{{ jumlahSudahDiagnosa }}</div>
        <div class="text-xs text-gray-400 mt-1">Sudah diagnosis</div>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
        <div class="text-2xl font-semibold text-amber-600">{{ jumlahBelumDiagnosa }}</div>
        <div class="text-xs text-gray-400 mt-1">Belum diagnosis</div>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
        <div class="text-2xl font-semibold text-red-600">{{ jumlahStunting }}</div>
        <div class="text-xs text-gray-400 mt-1">Terdeteksi stunting</div>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-100 bg-gray-50">
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Nama Balita</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Tanggal</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Usia</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">BB / TB</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">LILA</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Z-score TB/U</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Status</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Diagnosis</th>
            <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="kunjungan.data.length === 0">
            <td colspan="9" class="py-12 text-center text-gray-400 text-sm">
              <div class="text-4xl mb-2">📋</div>
              Belum ada data kunjungan bulan ini
            </td>
          </tr>
          <tr v-for="k in kunjungan.data" :key="k.id"
            class="border-b border-gray-50 hover:bg-gray-50 cursor-pointer"
            @click="$inertia.visit(route('kader.kunjungan.show', k.id))">
            <td class="py-3 px-4">
              <div class="font-medium text-gray-800">{{ k.nama_balita }}</div>
              <div class="text-xs text-gray-400">{{ k.kader }}</div>
            </td>
            <td class="py-3 px-4 text-gray-600 text-xs">{{ k.tanggal }}</td>
            <td class="py-3 px-4 text-gray-500 text-xs">{{ k.usia_bulan }} bln</td>
            <td class="py-3 px-4 text-gray-600 text-xs">
              <div>{{ k.berat_badan }} kg</div>
              <div>{{ k.tinggi_badan }} cm</div>
            </td>
            <td class="py-3 px-4 text-gray-600 text-xs">{{ k.lila ? k.lila + ' cm' : '—' }}</td>
            <td class="py-3 px-4 text-xs font-semibold" :class="zColor(k.zscore_tbu)">
              {{ k.zscore_tbu?.toFixed(2) ?? '—' }} SD
            </td>
            <td class="py-3 px-4">
              <span class="text-xs px-2 py-0.5 rounded-full font-medium" :class="statusClass(k.status)">
                {{ k.status }}
              </span>
            </td>
            <td class="py-3 px-4">
              <span v-if="k.sudah_diagnosa" class="text-xs text-green-600 font-medium">✅ Selesai</span>
              <span v-else class="text-xs text-amber-500 font-medium">⏳ Belum</span>
            </td>
            <td class="py-3 px-4" @click.stop>
              <div class="flex items-center gap-1.5">
                <Link :href="route('kader.kunjungan.show', k.id)"
                  class="text-xs text-gray-500 border border-gray-200 px-2 py-1 rounded-lg hover:bg-gray-50">Detail</Link>
                <Link v-if="!k.sudah_diagnosa" :href="route('kader.diagnosis.create', k.id)"
                  class="text-xs text-white bg-emerald-600 px-2 py-1 rounded-lg hover:bg-emerald-700">Diagnosa →</Link>
                <!-- Menggunakan objek parameter dan ditambahkan pengecekan (|| '#') agar tidak error jika null -->
                <Link v-else :href="k.diagnosis_id ? route('kader.diagnosis.show', { diagnosis: k.diagnosis_id }) : '#'"
                class="text-xs text-emerald-600 border border-emerald-200 px-2 py-1 rounded-lg hover:bg-emerald-50">
                Hasil →
                </Link>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="kunjungan.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
        <div class="text-xs text-gray-400">Menampilkan {{ kunjungan.from }}–{{ kunjungan.to }} dari {{ kunjungan.total }} kunjungan</div>
        <div class="flex gap-1">
          <Link v-for="link in kunjungan.links" :key="link.label" :href="link.url ?? '#'"
            v-html="link.label" preserve-scroll
            :class="['px-3 py-1 text-xs rounded border',
              link.active ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50',
              !link.url ? 'opacity-40 pointer-events-none' : '']"/>
        </div>
      </div>
    </div>
  </KaderLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import KaderLayout from '@/Layouts/KaderLayout.vue'

const props = defineProps({ kunjungan: Object, filters: Object })
const search       = ref(props.filters?.search ?? '')
const filterBulan  = ref(props.filters?.bulan ?? new Date().getMonth() + 1)
const filterStatus = ref(props.filters?.status ?? '')

const bulanOptions = computed(() => {
  const opts = []
  for (let i = 0; i < 6; i++) {
    const d = new Date(); d.setMonth(d.getMonth() - i)
    opts.push({ value: d.getMonth() + 1, label: d.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }) })
  }
  return opts
})

const doFilter = () => {
  router.get(route('kader.kunjungan.index'), { search: search.value, bulan: filterBulan.value, status: filterStatus.value }, { preserveState: true, replace: true })
}

const jumlahSudahDiagnosa = computed(() => props.kunjungan.data.filter(k => k.sudah_diagnosa).length)
const jumlahBelumDiagnosa = computed(() => props.kunjungan.data.filter(k => !k.sudah_diagnosa).length)
const jumlahStunting      = computed(() => props.kunjungan.data.filter(k => ['Stunting','Stunting berat'].includes(k.status)).length)

const zColor = (z) => z == null ? 'text-gray-400' : z < -2 ? 'text-red-600' : z < -1 ? 'text-amber-600' : 'text-green-600'
const statusClass = (s) => ({ 'Normal': 'bg-green-100 text-green-700', 'Berisiko': 'bg-amber-100 text-amber-700', 'Stunting': 'bg-red-100 text-red-700', 'Stunting berat': 'bg-red-200 text-red-900' }[s] ?? 'bg-gray-100 text-gray-500')
</script>