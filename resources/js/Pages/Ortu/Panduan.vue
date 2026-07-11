<template>
  <OrtuLayout title="Panduan Gizi">
    <div class="px-4 py-4 space-y-4">
      <div class="text-base font-semibold text-gray-800 mb-1">Panduan untuk Ibu</div>
      <div class="text-sm text-gray-400 mb-4">Cara bantu anak tumbuh sehat</div>

      <div
        v-for="tip in tips" :key="tip.judul"
        class="bg-white rounded-xl border border-gray-200 p-4"
      >
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0"
            :style="{ background: tip.bg }">
            {{ tip.icon }}
          </div>
          <div>
            <div class="font-medium text-gray-800 text-sm mb-1">{{ tip.judul }}</div>
            <div class="text-sm text-gray-500 leading-relaxed">{{ tip.isi }}</div>
          </div>
        </div>
      </div>

      <!-- Resep MPASI -->
      <div class="bg-emerald-600 rounded-xl p-4 text-white">
        <div class="font-semibold mb-1">🍳 Resep MPASI bergizi</div>
        <div class="text-sm text-emerald-100 mb-3">Rekomendasi menu untuk balita 6–24 bulan</div>
        <div class="space-y-2">
          <div v-for="r in resep" :key="r.nama" class="bg-white bg-opacity-10 rounded-lg p-3">
            <div class="font-medium text-sm">{{ r.nama }}</div>
            <div class="text-xs text-emerald-100 mt-0.5">{{ r.bahan }}</div>
            <div class="text-xs text-emerald-200 mt-0.5">{{ r.usia }}</div>
          </div>
        </div>
      </div>
    </div>
  </OrtuLayout>
</template>

<script setup>
import OrtuLayout from '@/Layouts/OrtuLayout.vue'

const tips = [
  { icon: '🥚', bg: '#EAF3DE', judul: 'Protein setiap hari', isi: 'Berikan telur, ikan, tahu, tempe setiap hari. Untuk usia 18 bulan: 3 kali makan utama + 2 kali snack bergizi.' },
  { icon: '☀️', bg: '#FFF3E0', judul: 'Jemur pagi hari', isi: 'Paparan sinar matahari pagi 10–15 menit membantu penyerapan kalsium untuk tulang dan pertumbuhan tinggi badan.' },
  { icon: '💧', bg: '#E6F1FB', judul: 'Cukup cairan', isi: 'Anak usia 1–3 tahun butuh sekitar 1.3 liter cairan per hari, termasuk dari makanan dan minuman.' },
  { icon: '📅', bg: '#FCEBEB', judul: 'Rutin ke posyandu', isi: 'Jangan lewatkan jadwal posyandu setiap bulan. Pemantauan rutin membantu mendeteksi masalah lebih awal.' },
  { icon: '🛌', bg: '#EEEDFE', judul: 'Tidur cukup', isi: 'Balita usia 1–3 tahun butuh 12–14 jam tidur per hari termasuk tidur siang. Tidur adalah waktu hormon pertumbuhan bekerja.' },
]

const resep = [
  { nama: 'Bubur Ayam Wortel', bahan: 'Beras, ayam, wortel, kaldu', usia: 'Usia 6–8 bulan' },
  { nama: 'Tim Tahu Tempe', bahan: 'Nasi, tahu, tempe, sayuran hijau', usia: 'Usia 9–12 bulan' },
  { nama: 'Nasi Telur Bayam', bahan: 'Nasi lunak, telur, bayam, minyak kelapa', usia: 'Usia 12–24 bulan' },
]
</script>