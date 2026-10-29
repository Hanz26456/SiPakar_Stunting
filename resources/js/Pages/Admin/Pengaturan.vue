<template>
  <AdminLayout title="Pengaturan Sistem">
    <div class="max-w-4xl space-y-6">
      <!-- Header -->
      <div>
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
          Pengaturan & Informasi Sistem
        </h2>
        <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">
          Konfigurasi umum, metadata instansi posyandu, dan referensi parameter inferensi pakar.
        </p>
      </div>

      <!-- Informasi Skripsi & Sistem Card -->
      <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 space-y-4 shadow-xs">
        <div class="flex items-center gap-3 pb-3 border-b border-zinc-200 dark:border-zinc-800">
          <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center">
            <Settings class="w-5 h-5" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Identitas Aplikasi & Basis Penelitian</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">SiPakar Stunting — Politeknik Negeri Jember</p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
          <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-800">
            <span class="text-zinc-500 dark:text-zinc-400 block mb-1">Nama Aplikasi</span>
            <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ sistem.nama }}</span>
          </div>
          <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-800">
            <span class="text-zinc-500 dark:text-zinc-400 block mb-1">Versi Rilis</span>
            <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ sistem.versi }}</span>
          </div>
          <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-800">
            <span class="text-zinc-500 dark:text-zinc-400 block mb-1">Lokasi Studi Kasus</span>
            <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ sistem.posyandu }} ({{ sistem.lokasi }})</span>
          </div>
          <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-800">
            <span class="text-zinc-500 dark:text-zinc-400 block mb-1">Pakar Rujukan Utama</span>
            <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ sistem.pakar }}</span>
          </div>
          <div class="sm:col-span-2 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-800">
            <span class="text-zinc-500 dark:text-zinc-400 block mb-1">Metodologi & Algoritma</span>
            <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ sistem.metode }}</span>
          </div>
        </div>
      </div>

      <!-- Informasi Threshold Antropometri WHO 2006 -->
      <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 space-y-3 shadow-xs">
        <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
          <ShieldAlert class="w-4 h-4 text-amber-500" />
          <span>Batas Standar Diagnostik (Permenkes No. 2 Tahun 2020)</span>
        </h3>
        <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
          Sistem menggunakan tabel referensi LMS WHO Child Growth Standards 2006 untuk menghitung Z-score tinggi badan menurut umur (TB/U):
        </p>
        <div class="space-y-2 text-xs">
          <div class="flex items-center justify-between p-2.5 rounded-lg bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/40 text-red-800 dark:text-red-300">
            <span class="font-semibold">Stunting Berat (Severely Stunted)</span>
            <span class="font-mono font-bold">Z-score &lt; -3.00 SD</span>
          </div>
          <div class="flex items-center justify-between p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40 text-amber-800 dark:text-amber-300">
            <span class="font-semibold">Stunting (Pendek / Stunted)</span>
            <span class="font-mono font-bold">-3.00 SD s/d &lt; -2.00 SD</span>
          </div>
          <div class="flex items-center justify-between p-2.5 rounded-lg bg-yellow-50 dark:bg-yellow-950/20 border border-yellow-200 dark:border-yellow-900/40 text-yellow-800 dark:text-yellow-300">
            <span class="font-semibold">Berisiko Stunting</span>
            <span class="font-mono font-bold">-2.00 SD s/d &lt; -1.00 SD</span>
          </div>
          <div class="flex items-center justify-between p-2.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/40 text-emerald-800 dark:text-emerald-300">
            <span class="font-semibold">Gizi Normal</span>
            <span class="font-mono font-bold">&ge; -1.00 SD</span>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Settings, ShieldAlert } from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
  sistem: Object,
})
</script>
