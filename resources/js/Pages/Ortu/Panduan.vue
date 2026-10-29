<template>
  <OrtuLayout title="Panduan Gizi">
    <div class="px-4 py-4 space-y-4 max-w-xl mx-auto">
      <div>
        <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-slate-100">Panduan Edukasi Ibu & Anak</h2>
        <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Panduan praktis pencegahan stunting dan pola asuh tumbuh kembang optimal.</p>
      </div>

      <!-- Tips List -->
      <div class="space-y-3">
        <div
          v-for="tip in tips"
          :key="tip.judul"
          class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-4 shadow-2xs transition-all hover:border-orbit-primary/40"
        >
          <div class="flex items-start gap-3.5">
            <div
              class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
              :class="tip.badgeClass"
            >
              <component :is="tip.icon" class="w-5 h-5" />
            </div>
            <div>
              <div class="font-bold text-slate-800 dark:text-slate-100 text-sm mb-1">{{ tip.judul }}</div>
              <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">{{ tip.isi }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Resep MPASI -->
      <div class="bg-orbit-primary text-white rounded-2xl p-5 shadow-lg shadow-purple-600/20">
        <div class="flex items-center gap-2 font-bold text-sm sm:text-base mb-1">
          <ChefHat class="w-5 h-5 text-purple-200" />
          <span>Rekomendasi Resep MPASI Bergizi Seimbang</span>
        </div>
        <div class="text-xs text-purple-200 mb-4">Pedoman variasi menu padat gizi untuk balita usia 6–24 bulan</div>
        <div class="space-y-2.5">
          <div
            v-for="r in resep"
            :key="r.nama"
            class="bg-white/10 backdrop-blur-xs rounded-xl p-3 border border-white/10"
          >
            <div class="flex items-center justify-between">
              <span class="font-bold text-sm">{{ r.nama }}</span>
              <span class="text-[11px] font-medium bg-white/20 px-2 py-0.5 rounded-full text-purple-100">{{ r.usia }}</span>
            </div>
            <div class="text-xs text-purple-100 mt-1">Bahan: {{ r.bahan }}</div>
          </div>
        </div>
      </div>
    </div>
  </OrtuLayout>
</template>

<script setup>
import {
  Utensils,
  Sun,
  Droplets,
  Calendar,
  Moon,
  ChefHat
} from 'lucide-vue-next'
import OrtuLayout from '@/Layouts/OrtuLayout.vue'

const tips = [
  {
    icon: Utensils,
    badgeClass: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
    judul: 'Asupan Protein Hewani Setiap Hari',
    isi: 'Prioritaskan telur, ikan, daging ayam, atau hati ayam setiap hari. Untuk usia 12–24 bulan: 3 kali makan utama padat gizi ditambah 2 kali selingan sehat.'
  },
  {
    icon: Sun,
    badgeClass: 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
    judul: 'Aktivitas Fisik & Sinar Matahari Pagi',
    isi: 'Paparan sinar matahari pagi selama 10–15 menit mendukung sintesis vitamin D alami tubuh yang krusial untuk penyerapan kalsium dan pertumbuhan tinggi tulang.'
  },
  {
    icon: Droplets,
    badgeClass: 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
    judul: 'Kebutuhan Cairan & Hidrasi Sehat',
    isi: 'Balita usia 1–3 tahun membutuhkan sekitar 1.1 hingga 1.3 liter cairan per hari yang bersumber dari air matang bersih, sup, dan buah segar.'
  },
  {
    icon: Calendar,
    badgeClass: 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
    judul: 'Disiplin Kunjungan Posyandu Bulanan',
    isi: 'Pantau kurva tumbuh kembang di posyandu setiap bulan agar deviasi perlambatan pertumbuhan berat/tinggi badan dapat segera diintervensi oleh bidan.'
  },
  {
    icon: Moon,
    badgeClass: 'bg-purple-500/10 text-orbit-primary',
    judul: 'Kualitas & Durasi Tidur yang Cukup',
    isi: 'Hormon pertumbuhan (growth hormone) diproduksi maksimal saat balita tidur lelap pada malam hari. Pastikan si kecil tidur 11–14 jam per hari.'
  },
]

const resep = [
  { nama: 'Bubur Hati Ayam & Wortel', bahan: 'Beras putih, hati ayam cincang, wortel parut, santan murni', usia: 'Usia 6–8 Bulan' },
  { nama: 'Nasi Tim Ikan Kembung & Labu Siam', bahan: 'Nasi lembek, fillet kembung lumat, labu siam, minyak kelapa', usia: 'Usia 9–11 Bulan' },
  { nama: 'Nasi Telur Dadar Bayam & Tahu', bahan: 'Nasi pulen, telur ayam, daun bayam cincang, tahu sutra', usia: 'Usia 12–24 Bulan' },
]
</script>
