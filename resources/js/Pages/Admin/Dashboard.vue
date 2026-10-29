<template>
  <AdminLayout title="Dashboard Administrator">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
          Ringkasan Sistem & Pemantauan
        </h2>
        <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">
          Pantau status diagnosis Certainty Factor, pertumbuhan balita, dan keaktifan pengguna di Posyandu Melati Pujer.
        </p>
      </div>

      <div class="flex items-center gap-2.5">
        <Link
          :href="route('admin.log')"
          class="inline-flex items-center px-3.5 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-xs font-semibold text-zinc-700 dark:text-zinc-200 transition-colors shadow-2xs"
        >
          Lihat Log Audit
        </Link>
        <Link
          :href="route('admin.rule-cf.index')"
          class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-xs font-semibold text-white transition-all shadow-xs active:scale-[0.98]"
        >
          Kelola Basis CF
        </Link>
      </div>
    </div>

    <!-- Metis Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard
        label="Total Pengguna"
        :value="stats.total_pengguna"
        sub="Terdaftar dalam 4 role akun"
      >
        <template #icon>
          <Users class="w-4 h-4" />
        </template>
      </StatCard>

      <StatCard
        label="Rule CF Aktif"
        :value="stats.total_rule"
        sub="Basis pengetahuan pakar"
      >
        <template #icon>
          <SlidersHorizontal class="w-4 h-4" />
        </template>
      </StatCard>

      <StatCard
        label="Total Diagnosis"
        :value="stats.total_diagnosis"
        sub="Pemeriksaan anak tercatat"
      >
        <template #icon>
          <Activity class="w-4 h-4" />
        </template>
      </StatCard>

      <StatCard
        label="Akurasi Sistem"
        :value="(stats.akurasi ?? 0) + '%'"
        sub="Validasi verifikasi medis bidan"
      >
        <template #icon>
          <ShieldCheck class="w-4 h-4" />
        </template>
      </StatCard>
    </div>

    <!-- Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <!-- Pengguna per role -->
      <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5 shadow-2xs">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-100 dark:border-zinc-800">
          <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Distribusi Pengguna per Role</h3>
          <span class="text-xs text-zinc-400 font-mono">{{ stats.total_pengguna }} akun</span>
        </div>

        <div class="space-y-3.5">
          <div
            v-for="r in roleStats" :key="r.label"
            class="flex items-center gap-3"
          >
            <div class="text-xs font-medium w-36 text-zinc-600 dark:text-zinc-400">
              {{ r.label }}
            </div>
            <div class="flex-1 h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="r.barClass"
                :style="{ width: stats.total_pengguna > 0 ? (r.count / stats.total_pengguna * 100) + '%' : '0%' }"
              />
            </div>
            <div class="text-xs font-mono font-semibold text-zinc-800 dark:text-zinc-200 w-8 text-right flex-shrink-0">
              {{ r.count }}
            </div>
          </div>
        </div>

        <div class="mt-5 pt-3.5 border-t border-zinc-100 dark:border-zinc-800 flex justify-end">
          <Link
            :href="route('admin.users.index')"
            class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-semibold flex items-center gap-1"
          >
            <span>Kelola data pengguna</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </Link>
        </div>
      </div>

      <!-- Aktivitas terbaru -->
      <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5 shadow-2xs flex flex-col">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-100 dark:border-zinc-800">
          <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Log Aktivitas Sistem Terkini</h3>
          <span class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wider">Audit Trail</span>
        </div>

        <div v-if="!aktivitas_terbaru || aktivitas_terbaru.length === 0" class="text-xs text-zinc-400 text-center py-8 flex-1 flex items-center justify-center italic">
          Belum ada aktivitas tercatat
        </div>

        <div v-else class="space-y-2.5 flex-1 overflow-y-auto max-h-[220px] pr-1">
          <div 
            v-for="(a, idx) in aktivitas_terbaru" 
            :key="idx" 
            class="p-2.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-800"
          >
            <div class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate">{{ a.label }}</div>
            <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">{{ a.user }} · {{ a.waktu }}</div>
          </div>
        </div>

        <div class="mt-4 pt-3.5 border-t border-zinc-100 dark:border-zinc-800 flex justify-end">
          <Link
            :href="route('admin.log')"
            class="text-xs text-zinc-500 hover:text-blue-600 dark:text-zinc-400 dark:hover:text-blue-400 font-semibold flex items-center gap-1 transition-colors"
          >
            <span>Buka log audit lengkap</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </Link>
        </div>
      </div>
    </div>

    <!-- Distribusi 6 bulan + Ringkasan Stunting Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
      <div class="lg:col-span-2 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-zinc-100 dark:border-zinc-800">
          <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Tren Diagnosis Gizi (6 Bulan Terakhir)</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Proporsi status gizi anak per bulan pemeriksaan</p>
          </div>
          <div class="flex items-center gap-3 text-xs">
            <span class="flex items-center gap-1.5 text-zinc-600 dark:text-zinc-400">
              <span class="w-2.5 h-2.5 rounded-xs bg-emerald-500" /> Normal
            </span>
            <span class="flex items-center gap-1.5 text-zinc-600 dark:text-zinc-400">
              <span class="w-2.5 h-2.5 rounded-xs bg-amber-500" /> Berisiko
            </span>
            <span class="flex items-center gap-1.5 text-zinc-600 dark:text-zinc-400">
              <span class="w-2.5 h-2.5 rounded-xs bg-rose-500" /> Stunting
            </span>
          </div>
        </div>

        <div v-if="!distribusi || distribusi.length === 0" class="text-xs text-zinc-400 text-center py-12 italic">
          Belum ada riwayat data balita
        </div>

        <div v-else class="pt-2">
          <div class="flex items-end gap-3 h-32 mb-3 px-1">
            <div
              v-for="d in distribusi" :key="d.bulan"
              class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group"
            >
              <div class="w-full max-w-[42px] flex flex-col-reverse gap-0.5 h-[100px] bg-zinc-100 dark:bg-zinc-800 rounded-lg p-1 border border-zinc-200 dark:border-zinc-700">
                <div
                  v-if="d.stunting"
                  :style="{ height: pct(d.stunting, d.total) + '%' }"
                  class="w-full bg-rose-500 rounded-xs"
                  :title="`Stunting: ${d.stunting}`"
                />
                <div
                  v-if="d.berisiko"
                  :style="{ height: pct(d.berisiko, d.total) + '%' }"
                  class="w-full bg-amber-500 rounded-xs"
                  :title="`Berisiko: ${d.berisiko}`"
                />
                <div
                  v-if="d.normal"
                  :style="{ height: pct(d.normal, d.total) + '%' }"
                  class="w-full bg-emerald-500 rounded-xs"
                  :title="`Normal: ${d.normal}`"
                />
              </div>
              <div class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 truncate max-w-[50px]">
                {{ d.bulan.split(' ')[0] }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Ringkasan Status Box -->
      <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5 shadow-2xs flex flex-col justify-between">
        <div>
          <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-3.5 pb-2 border-b border-zinc-100 dark:border-zinc-800">
            Ringkasan Klasifikasi Gizi
          </h3>
          <div class="space-y-2">
            <div class="flex items-center justify-between p-2.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-300">
              <span class="text-xs font-semibold">Normal</span>
              <span class="text-sm font-bold font-mono">{{ stats.normal ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between p-2.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300">
              <span class="text-xs font-semibold">Berisiko</span>
              <span class="text-sm font-bold font-mono">{{ stats.berisiko ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between p-2.5 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-300">
              <span class="text-xs font-semibold">Stunting</span>
              <span class="text-sm font-bold font-mono">{{ stats.stunting ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between p-2.5 rounded-lg bg-rose-600/15 border border-rose-600/30 text-rose-800 dark:text-rose-200">
              <span class="text-xs font-semibold">Stunting Berat</span>
              <span class="text-sm font-bold font-mono">{{ stats.stunting_berat ?? 0 }}</span>
            </div>
          </div>
        </div>

        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs">
          <span class="text-zinc-500">Total: <strong class="text-zinc-800 dark:text-zinc-200">{{ stats.total_balita ?? 0 }} balita</strong></span>
          <span class="text-zinc-500">Prevalensi: <strong class="text-rose-600 dark:text-rose-400 font-semibold">{{ stats.persen_stunting ?? 0 }}%</strong></span>
        </div>
      </div>
    </div>

    <!-- Quick Access Metis Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <Link
        :href="route('admin.gejala.index')"
        class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-blue-500 p-4 transition-colors flex items-center justify-between group shadow-2xs"
      >
        <div>
          <div class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-blue-600 dark:group-hover:text-blue-400">Kelola Gejala</div>
          <div class="text-xs text-zinc-400 mt-0.5">{{ stats.total_gejala ?? 17 }} gejala terdaftar</div>
        </div>
        <ArrowRight class="w-4 h-4 text-zinc-400 group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all" />
      </Link>

      <Link
        :href="route('admin.rule-cf.index')"
        class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-blue-500 p-4 transition-colors flex items-center justify-between group shadow-2xs"
      >
        <div>
          <div class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-blue-600 dark:group-hover:text-blue-400">Kelola Basis Rule CF</div>
          <div class="text-xs text-zinc-400 mt-0.5">{{ stats.total_rule }} rule aktif</div>
        </div>
        <ArrowRight class="w-4 h-4 text-zinc-400 group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all" />
      </Link>

      <Link
        :href="route('admin.users.index')"
        class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-blue-500 p-4 transition-colors flex items-center justify-between group shadow-2xs"
      >
        <div>
          <div class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-blue-600 dark:group-hover:text-blue-400">Manajemen Pengguna</div>
          <div class="text-xs text-zinc-400 mt-0.5">{{ stats.total_pengguna }} akun aktif</div>
        </div>
        <ArrowRight class="w-4 h-4 text-zinc-400 group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all" />
      </Link>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
  Users,
  SlidersHorizontal,
  Activity,
  ShieldCheck,
  ArrowRight
} from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatCard from '@/Components/StatCard.vue'

const props = defineProps({
  stats: Object,
  aktivitas_terbaru: Array,
  distribusi: Array,
})

const roleStats = computed(() => [
  { label: 'Administrator', count: props.stats?.jumlah_admin ?? 0, barClass: 'bg-blue-600' },
  { label: 'Tenaga Medis (Bidan)', count: props.stats?.jumlah_bidan ?? 0, barClass: 'bg-cyan-600' },
  { label: 'Kader Posyandu', count: props.stats?.jumlah_kader ?? 0, barClass: 'bg-emerald-600' },
  { label: 'Orang Tua Balita', count: props.stats?.jumlah_ortu ?? 0, barClass: 'bg-indigo-600' },
])

const pct = (val, total) => total > 0 ? Math.round(val / total * 100) : 0
</script>
