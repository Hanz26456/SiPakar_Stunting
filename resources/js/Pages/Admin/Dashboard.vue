<template>
  <AdminLayout title="Dashboard Administrator">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-100">
          Ringkasan Sistem
        </h2>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
          Pantau status diagnosis Certainty Factor, pertumbuhan balita, dan keaktifan pengguna di Posyandu Melati Pujer.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <Link
          :href="route('admin.log')"
          class="inline-flex items-center px-3.5 py-2 rounded-xl border border-orbit-border bg-orbit-surface hover:bg-white/5 text-xs font-medium text-slate-300 hover:text-white transition-colors"
        >
          Lihat Log
        </Link>
        <Link
          :href="route('admin.rule-cf.index')"
          class="inline-flex items-center px-3.5 py-2 rounded-xl bg-orbit-primary hover:bg-orbit-primary-light text-xs font-semibold text-white transition-all shadow-sm"
        >
          Kelola Basis CF
        </Link>
      </div>
    </div>

    <!-- Stat cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <StatCard
        label="Total Pengguna"
        :value="stats.total_pengguna"
        sub="Terdaftar dalam 4 role"
      />

      <StatCard
        label="Rule CF Aktif"
        :value="stats.total_rule"
        sub="Basis pengetahuan pakar"
      />

      <StatCard
        label="Total Diagnosis"
        :value="stats.total_diagnosis"
        sub="Pemeriksaan tercatat"
      />

      <StatCard
        label="Akurasi Sistem"
        :value="(stats.akurasi ?? 0) + '%'"
        sub="Validasi verifikasi bidan"
      />
    </div>

    <!-- Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
      <!-- Pengguna per role -->
      <div class="bg-orbit-surface rounded-2xl border border-orbit-border p-5">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-semibold text-slate-200">Distribusi Pengguna per Role</h3>
          <span class="text-xs text-slate-500">{{ stats.total_pengguna }} akun</span>
        </div>

        <div class="space-y-3">
          <div
            v-for="r in roleStats" :key="r.label"
            class="flex items-center gap-3"
          >
            <div class="text-xs font-medium w-32 text-slate-400">
              {{ r.label }}
            </div>
            <div class="flex-1 h-2 bg-orbit-surface2 rounded-full overflow-hidden border border-orbit-border/50">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="r.barClass"
                :style="{ width: stats.total_pengguna > 0 ? (r.count / stats.total_pengguna * 100) + '%' : '0%' }"
              />
            </div>
            <div class="text-xs font-semibold text-slate-200 w-8 text-right flex-shrink-0">
              {{ r.count }}
            </div>
          </div>
        </div>

        <div class="mt-5 pt-3.5 border-t border-orbit-border flex justify-end">
          <Link
            :href="route('admin.users.index')"
            class="text-xs text-orbit-primary-light hover:underline font-medium"
          >
            Kelola data pengguna →
          </Link>
        </div>
      </div>

      <!-- Aktivitas terbaru -->
      <div class="bg-orbit-surface rounded-2xl border border-orbit-border p-5 flex flex-col">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-semibold text-slate-200">Log Aktivitas Terkini</h3>
          <span class="text-xs text-slate-500">Terbaru</span>
        </div>

        <div v-if="!aktivitas_terbaru || aktivitas_terbaru.length === 0" class="text-xs text-slate-500 text-center py-8 flex-1 flex items-center justify-center">
          Belum ada aktivitas tercatat
        </div>

        <div v-else class="space-y-2.5 flex-1 overflow-y-auto max-h-[220px] pr-1">
          <div 
            v-for="(a, idx) in aktivitas_terbaru" 
            :key="idx" 
            class="p-2.5 rounded-xl bg-orbit-surface2/40 border border-orbit-border/50"
          >
            <div class="text-xs font-medium text-slate-200 truncate">{{ a.label }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">{{ a.user }} · {{ a.waktu }}</div>
          </div>
        </div>

        <div class="mt-4 pt-3.5 border-t border-orbit-border flex justify-end">
          <Link
            :href="route('admin.log')"
            class="text-xs text-slate-400 hover:text-slate-200 hover:underline font-medium"
          >
            Buka log audit lengkap →
          </Link>
        </div>
      </div>
    </div>

    <!-- Distribusi 6 bulan + Ringkasan Stunting Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
      <div class="lg:col-span-2 bg-orbit-surface rounded-2xl border border-orbit-border p-5">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-sm font-semibold text-slate-200">Tren Diagnosis Gizi (6 Bulan Terakhir)</h3>
            <p class="text-xs text-slate-400 mt-0.5">Proporsi status gizi anak per bulan</p>
          </div>
          <div class="flex items-center gap-3 text-xs">
            <span class="flex items-center gap-1.5 text-slate-400">
              <span class="w-2.5 h-2.5 rounded-sm bg-emerald-400" /> Normal
            </span>
            <span class="flex items-center gap-1.5 text-slate-400">
              <span class="w-2.5 h-2.5 rounded-sm bg-amber-400" /> Berisiko
            </span>
            <span class="flex items-center gap-1.5 text-slate-400">
              <span class="w-2.5 h-2.5 rounded-sm bg-rose-500" /> Stunting
            </span>
          </div>
        </div>

        <div v-if="!distribusi || distribusi.length === 0" class="text-xs text-slate-500 text-center py-12">
          Belum ada riwayat data balita
        </div>

        <div v-else class="pt-2">
          <div class="flex items-end gap-3 h-32 mb-3 px-1">
            <div
              v-for="d in distribusi" :key="d.bulan"
              class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group"
            >
              <div class="w-full max-w-[42px] flex flex-col-reverse gap-0.5 h-[100px] bg-orbit-surface2/60 rounded-lg p-1 border border-orbit-border/50">
                <div
                  v-if="d.stunting"
                  :style="{ height: pct(d.stunting, d.total) + '%' }"
                  class="w-full bg-rose-500 rounded-sm"
                  :title="`Stunting: ${d.stunting}`"
                />
                <div
                  v-if="d.berisiko"
                  :style="{ height: pct(d.berisiko, d.total) + '%' }"
                  class="w-full bg-amber-400 rounded-sm"
                  :title="`Berisiko: ${d.berisiko}`"
                />
                <div
                  v-if="d.normal"
                  :style="{ height: pct(d.normal, d.total) + '%' }"
                  class="w-full bg-emerald-400 rounded-sm"
                  :title="`Normal: ${d.normal}`"
                />
              </div>
              <div class="text-[11px] font-medium text-slate-400 truncate max-w-[50px]">
                {{ d.bulan.split(' ')[0] }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Ringkasan Status Box -->
      <div class="bg-orbit-surface rounded-2xl border border-orbit-border p-5 flex flex-col justify-between">
        <div>
          <h3 class="text-sm font-semibold text-slate-200 mb-3.5">Ringkasan Klasifikasi Gizi</h3>
          <div class="space-y-2">
            <div class="flex items-center justify-between p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">
              <span class="text-xs font-medium">Normal</span>
              <span class="text-sm font-bold">{{ stats.normal ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400">
              <span class="text-xs font-medium">Berisiko</span>
              <span class="text-sm font-bold">{{ stats.berisiko ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between p-2.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400">
              <span class="text-xs font-medium">Stunting</span>
              <span class="text-sm font-bold">{{ stats.stunting ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between p-2.5 rounded-xl bg-rose-500/20 border border-rose-500/30 text-rose-300">
              <span class="text-xs font-medium">Stunting Berat</span>
              <span class="text-sm font-bold">{{ stats.stunting_berat ?? 0 }}</span>
            </div>
          </div>
        </div>

        <div class="mt-4 pt-3 border-t border-orbit-border flex items-center justify-between text-xs">
          <span class="text-slate-400">Total: <strong class="text-slate-200">{{ stats.total_balita ?? 0 }} balita</strong></span>
          <span class="text-slate-400">Prevalensi: <strong class="text-rose-400 font-semibold">{{ stats.persen_stunting ?? 0 }}%</strong></span>
        </div>
      </div>
    </div>

    <!-- Quick Access Orbit Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <Link
        :href="route('admin.gejala.index')"
        class="bg-orbit-surface rounded-2xl border border-orbit-border hover:border-orbit-border2 p-4 transition-colors flex items-center justify-between group shadow-sm"
      >
        <div>
          <div class="text-sm font-medium text-slate-200 group-hover:text-white">Kelola Gejala</div>
          <div class="text-xs text-slate-400 mt-0.5">{{ stats.total_gejala ?? 17 }} gejala terdaftar</div>
        </div>
        <span class="text-xs text-slate-500 group-hover:text-slate-300">Buka →</span>
      </Link>

      <Link
        :href="route('admin.rule-cf.index')"
        class="bg-orbit-surface rounded-2xl border border-orbit-border hover:border-orbit-border2 p-4 transition-colors flex items-center justify-between group shadow-sm"
      >
        <div>
          <div class="text-sm font-medium text-slate-200 group-hover:text-white">Kelola Rule CF</div>
          <div class="text-xs text-slate-400 mt-0.5">{{ stats.total_rule }} rule aktif</div>
        </div>
        <span class="text-xs text-slate-500 group-hover:text-slate-300">Buka →</span>
      </Link>

      <Link
        :href="route('admin.users.index')"
        class="bg-orbit-surface rounded-2xl border border-orbit-border hover:border-orbit-border2 p-4 transition-colors flex items-center justify-between group shadow-sm"
      >
        <div>
          <div class="text-sm font-medium text-slate-200 group-hover:text-white">Manajemen Pengguna</div>
          <div class="text-xs text-slate-400 mt-0.5">{{ stats.total_pengguna }} akun aktif</div>
        </div>
        <span class="text-xs text-slate-500 group-hover:text-slate-300">Buka →</span>
      </Link>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatCard from '@/Components/StatCard.vue'

const props = defineProps({
  stats: Object,
  aktivitas_terbaru: Array,
  distribusi: Array,
})

const roleStats = computed(() => [
  { label: 'Administrator', count: props.stats?.jumlah_admin ?? 0, barClass: 'bg-purple-500' },
  { label: 'Tenaga Medis (Bidan)', count: props.stats?.jumlah_bidan ?? 0, barClass: 'bg-cyan-500' },
  { label: 'Kader Posyandu', count: props.stats?.jumlah_kader ?? 0, barClass: 'bg-emerald-500' },
  { label: 'Orang Tua Balita', count: props.stats?.jumlah_ortu ?? 0, barClass: 'bg-blue-500' },
])

const pct = (val, total) => total > 0 ? Math.round(val / total * 100) : 0
</script>