<template>
  <AdminLayout title="Dashboard Admin">

    <!-- Stat cards -->
    <div class="grid grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-2">
          <div class="text-xs text-gray-400">Total Pengguna</div>
          <span class="text-xl">👥</span>
        </div>
        <div class="text-2xl font-semibold text-gray-800">{{ stats.total_pengguna }}</div>
        <div class="text-xs text-gray-400 mt-1">Terdaftar di sistem</div>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-2">
          <div class="text-xs text-gray-400">Rule CF Aktif</div>
          <span class="text-xl">⚙️</span>
        </div>
        <div class="text-2xl font-semibold text-gray-800">{{ stats.total_rule }}</div>
        <div class="text-xs text-gray-400 mt-1">Basis pengetahuan</div>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-2">
          <div class="text-xs text-gray-400">Total Diagnosis</div>
          <span class="text-xl">📊</span>
        </div>
        <div class="text-2xl font-semibold text-gray-800">{{ stats.total_diagnosis }}</div>
        <div class="text-xs text-gray-400 mt-1">Semua waktu</div>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-2">
          <div class="text-xs text-gray-400">Akurasi Sistem</div>
          <span class="text-xl">🎯</span>
        </div>
        <div class="text-2xl font-semibold text-green-600">{{ stats.akurasi }}%</div>
        <div class="text-xs text-gray-400 mt-1">vs verifikasi bidan</div>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">

      <!-- Pengguna per role -->
      <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="text-sm font-medium text-gray-700 mb-4">👥 Pengguna per Role</div>
        <div class="space-y-3">
          <div
            v-for="r in roleStats" :key="r.label"
            class="flex items-center gap-3"
          >
            <div class="text-sm w-24 text-gray-500 flex-shrink-0 flex items-center gap-2">
              <span>{{ r.icon }}</span>
              <span>{{ r.label }}</span>
            </div>
            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all"
                :class="r.color"
                :style="{ width: stats.total_pengguna > 0 ? (r.count / stats.total_pengguna * 100) + '%' : '0%' }"
              ></div>
            </div>
            <div class="text-sm font-semibold text-gray-700 w-6 text-right flex-shrink-0">
              {{ r.count }}
            </div>
          </div>
        </div>
        <div class="mt-4 pt-3 border-t border-gray-100">
          <Link
            :href="route('admin.users.index')"
            class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1"
          >
            Kelola pengguna →
          </Link>
        </div>
      </div>

      <!-- Aktivitas terbaru -->
      <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="text-sm font-medium text-gray-700 mb-3">⚡ Aktivitas Terbaru</div>
        <div v-if="aktivitas_terbaru.length === 0" class="text-sm text-gray-400 text-center py-4">
          Belum ada aktivitas
        </div>
        <div v-else class="space-y-3">
          <div v-for="a in aktivitas_terbaru" :key="a.label" class="flex items-start gap-2">
            <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 mt-1.5 flex-shrink-0"></div>
            <div class="flex-1 min-w-0">
              <div class="text-sm text-gray-700 truncate">{{ a.label }}</div>
              <div class="text-xs text-gray-400">{{ a.user }} · {{ a.waktu }}</div>
            </div>
          </div>
        </div>
        <div class="mt-4 pt-3 border-t border-gray-100">
          <Link
            :href="route('admin.log')"
            class="text-xs text-gray-500 hover:text-gray-700"
          >
            Lihat semua log →
          </Link>
        </div>
      </div>
    </div>

    <!-- Distribusi 6 bulan + Stunting summary -->
    <div class="grid grid-cols-3 gap-4">

      <!-- Grafik distribusi -->
      <div class="col-span-2 bg-white rounded-xl border border-gray-200 p-4">
        <div class="text-sm font-medium text-gray-700 mb-4">📈 Distribusi Status — 6 Bulan Terakhir</div>
        <div v-if="!distribusi || distribusi.length === 0" class="text-sm text-gray-400 text-center py-8">
          Belum ada data
        </div>
        <div v-else>
          <!-- Bar chart -->
          <div class="flex items-end gap-3 h-28 mb-3">
            <div
              v-for="d in distribusi" :key="d.bulan"
              class="flex-1 flex flex-col items-center gap-1"
            >
              <div class="w-full flex flex-col-reverse gap-0.5" style="height: 88px">
                <div
                  v-if="d.stunting"
                  :style="{ height: pct(d.stunting, d.total) + '%' }"
                  class="w-full bg-red-400 rounded-sm"
                ></div>
                <div
                  v-if="d.berisiko"
                  :style="{ height: pct(d.berisiko, d.total) + '%' }"
                  class="w-full bg-amber-300 rounded-sm"
                ></div>
                <div
                  v-if="d.normal"
                  :style="{ height: pct(d.normal, d.total) + '%' }"
                  class="w-full bg-emerald-300 rounded-sm"
                ></div>
              </div>
              <div class="text-xs text-gray-400">{{ d.bulan.split(' ')[0] }}</div>
            </div>
          </div>
          <!-- Legenda -->
          <div class="flex gap-4">
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
              <div class="w-3 h-3 rounded-sm bg-emerald-300"></div> Normal
            </div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
              <div class="w-3 h-3 rounded-sm bg-amber-300"></div> Berisiko
            </div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
              <div class="w-3 h-3 rounded-sm bg-red-400"></div> Stunting
            </div>
          </div>
        </div>
      </div>

      <!-- Ringkasan stunting -->
      <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="text-sm font-medium text-gray-700 mb-4">🚨 Ringkasan Status</div>
        <div class="space-y-3">
          <div class="flex items-center justify-between p-2.5 bg-green-50 rounded-lg">
            <div class="text-sm text-green-700 font-medium">Normal</div>
            <div class="text-lg font-bold text-green-600">{{ stats.normal }}</div>
          </div>
          <div class="flex items-center justify-between p-2.5 bg-amber-50 rounded-lg">
            <div class="text-sm text-amber-700 font-medium">Berisiko</div>
            <div class="text-lg font-bold text-amber-600">{{ stats.berisiko }}</div>
          </div>
          <div class="flex items-center justify-between p-2.5 bg-red-50 rounded-lg">
            <div class="text-sm text-red-700 font-medium">Stunting</div>
            <div class="text-lg font-bold text-red-600">{{ stats.stunting }}</div>
          </div>
          <div class="flex items-center justify-between p-2.5 bg-red-100 rounded-lg">
            <div class="text-sm text-red-900 font-medium">Stunting Berat</div>
            <div class="text-lg font-bold text-red-900">{{ stats.stunting_berat ?? 0 }}</div>
          </div>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100">
          <div class="text-xs text-gray-400">Total balita: <strong class="text-gray-700">{{ stats.total_balita }}</strong></div>
          <div class="text-xs text-gray-400 mt-0.5">Prevalensi stunting: <strong class="text-red-600">{{ stats.persen_stunting }}%</strong></div>
        </div>
      </div>

    </div>

    <!-- Akses cepat -->
    <div class="grid grid-cols-3 gap-3 mt-4">
      <Link
        :href="route('admin.gejala.index')"
        class="bg-white rounded-xl border border-gray-200 p-4 hover:border-gray-300 hover:bg-gray-50 transition-colors flex items-center gap-3"
      >
        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-xl flex-shrink-0">📋</div>
        <div>
          <div class="text-sm font-medium text-gray-800">Kelola Gejala</div>
          <div class="text-xs text-gray-400">{{ stats.total_gejala ?? 17 }} gejala terdaftar</div>
        </div>
      </Link>
      <Link
        :href="route('admin.rule-cf.index')"
        class="bg-white rounded-xl border border-gray-200 p-4 hover:border-gray-300 hover:bg-gray-50 transition-colors flex items-center gap-3"
      >
        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-xl flex-shrink-0">⚙️</div>
        <div>
          <div class="text-sm font-medium text-gray-800">Kelola Rule CF</div>
          <div class="text-xs text-gray-400">{{ stats.total_rule }} rule aktif</div>
        </div>
      </Link>
      <Link
        :href="route('admin.users.index')"
        class="bg-white rounded-xl border border-gray-200 p-4 hover:border-gray-300 hover:bg-gray-50 transition-colors flex items-center gap-3"
      >
        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-xl flex-shrink-0">👥</div>
        <div>
          <div class="text-sm font-medium text-gray-800">Manajemen Pengguna</div>
          <div class="text-xs text-gray-400">{{ stats.total_pengguna }} pengguna</div>
        </div>
      </Link>
    </div>

  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  stats: Object,
  aktivitas_terbaru: Array,
  distribusi: Array,
})

const roleStats = computed(() => [
  { label: 'Admin',     icon: '🛡️', count: props.stats?.jumlah_admin  ?? 0, color: 'bg-gray-600' },
  { label: 'Bidan',     icon: '👩‍⚕️', count: props.stats?.jumlah_bidan  ?? 0, color: 'bg-blue-400' },
  { label: 'Kader',     icon: '👩',  count: props.stats?.jumlah_kader  ?? 0, color: 'bg-emerald-400' },
  { label: 'Orang tua', icon: '👨‍👩‍👧', count: props.stats?.jumlah_ortu   ?? 0, color: 'bg-purple-400' },
])

const pct = (val, total) => total > 0 ? Math.round(val / total * 100) : 0
</script>