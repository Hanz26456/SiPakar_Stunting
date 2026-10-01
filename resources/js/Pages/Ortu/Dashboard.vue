<template>
  <OrtuLayout title="Beranda Orang Tua">
    <div class="py-2">
      <!-- Greeting Header -->
      <div class="mb-5">
        <span class="text-xs text-slate-400 font-medium">Selamat datang,</span>
        <h2 class="text-lg sm:text-xl font-bold text-slate-100 mt-0.5">
          {{ $page.props.auth.user.name }}
        </h2>
        <p class="text-xs text-slate-400 mt-1">
          Pantau status tumbuh kembang dan rekomendasi nutrisi anak Anda secara berkala.
        </p>
      </div>

      <!-- Kartu Balita -->
      <div v-for="b in balita" :key="b.id" class="mb-6 space-y-4">
        <!-- Status Hero Card -->
        <div
          class="rounded-3xl p-5 border relative overflow-hidden transition-all shadow-lg"
          :class="{
            'bg-rose-500/15 border-rose-500/30 text-rose-300': ['stunting','stunting_berat'].includes(b.status_terbaru),
            'bg-amber-500/15 border-amber-500/30 text-amber-300': b.status_terbaru === 'berisiko',
            'bg-emerald-500/15 border-emerald-500/30 text-emerald-300': b.status_terbaru === 'normal',
            'bg-orbit-surface border-orbit-border text-slate-300': !b.status_terbaru,
          }"
        >
          <div class="mb-3">
            <h3 class="text-base sm:text-lg font-bold text-slate-100">{{ b.nama }}</h3>
            <p class="text-xs text-slate-400 mt-0.5">{{ b.usia_format }} · {{ b.jenis_kelamin }}</p>
          </div>

          <div v-if="b.status_terbaru" class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
              <span
                class="text-base font-bold"
                :class="{
                  'text-rose-400': ['stunting','stunting_berat'].includes(b.status_terbaru),
                  'text-amber-400': b.status_terbaru === 'berisiko',
                  'text-emerald-400': b.status_terbaru === 'normal',
                }"
              >
                {{ b.label_status }}
              </span>
              <span class="text-xs text-slate-400">· Tingkat Keyakinan {{ b.cf_persen }}</span>
            </div>

            <!-- Progress bar CF -->
            <div class="h-2.5 bg-orbit-surface2 rounded-full overflow-hidden border border-orbit-border/50">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="{
                  'bg-rose-500 shadow-sm shadow-rose-500/50': ['stunting','stunting_berat'].includes(b.status_terbaru),
                  'bg-amber-400 shadow-sm shadow-amber-400/50': b.status_terbaru === 'berisiko',
                  'bg-emerald-400 shadow-sm shadow-emerald-400/50': b.status_terbaru === 'normal',
                }"
                :style="{ width: (b.cf_persen?.replace('%','') ?? 0) + '%' }"
              />
            </div>

            <div v-if="b.kunjungan_terakhir" class="text-[11px] text-slate-400 mt-2 flex items-center gap-1">
              <Calendar class="w-3.5 h-3.5 text-slate-500" />
              <span>Pemeriksaan terakhir: {{ b.kunjungan_terakhir }}</span>
            </div>
          </div>

          <div v-else class="text-xs text-slate-400 relative z-10">Belum ada riwayat pemeriksaan posyandu</div>
        </div>

        <!-- Ukuran Terbaru Grid -->
        <div class="grid grid-cols-3 gap-3">
          <div class="bg-orbit-surface rounded-2xl border border-orbit-border p-3.5 text-center shadow-sm">
            <div class="text-lg font-bold text-slate-100">{{ b.berat_terbaru ?? '—' }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Berat Badan (kg)</div>
          </div>
          <div class="bg-orbit-surface rounded-2xl border border-orbit-border p-3.5 text-center shadow-sm">
            <div class="text-lg font-bold text-slate-100">{{ b.tinggi_terbaru ?? '—' }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Tinggi Badan (cm)</div>
          </div>
          <div class="bg-orbit-surface rounded-2xl border border-orbit-border p-3.5 text-center shadow-sm">
            <div
              class="text-lg font-bold"
              :class="{
                'text-rose-400': b.zscore_tbu < -2,
                'text-amber-400': b.zscore_tbu >= -2 && b.zscore_tbu < -1,
                'text-emerald-400': b.zscore_tbu >= -1,
                'text-slate-400': b.zscore_tbu == null,
              }"
            >
              {{ b.zscore_tbu != null ? Number(b.zscore_tbu).toFixed(1) : '—' }}
            </div>
            <div class="text-[11px] text-slate-400 mt-0.5">Z-Score TB/U</div>
          </div>
        </div>

        <!-- Rekomendasi Nutrisi -->
        <div
          v-if="b.status_terbaru && b.status_terbaru !== 'normal'"
          class="bg-orbit-surface rounded-2xl border border-orbit-border p-4 shadow-sm"
        >
          <div class="text-xs font-semibold text-slate-200 mb-3">
            Tindakan Yang Dianjurkan:
          </div>
          <div v-if="['stunting','stunting_berat'].includes(b.status_terbaru)" class="space-y-2.5 text-xs text-slate-300">
            <div class="flex items-start gap-2.5 p-2 rounded-xl bg-rose-500/10 border border-rose-500/20">
              <span class="text-rose-400 font-bold">1.</span>
              <span>Segera periksakan ke Puskesmas Pujer atau bidan desa. Bawa buku KIA/KMS balita Anda.</span>
            </div>
            <div class="flex items-start gap-2.5 p-2 rounded-xl bg-amber-500/10 border border-amber-500/20">
              <span class="text-amber-400 font-bold">2.</span>
              <span>Tingkatkan asupan protein hewani setiap hari: telur, ikan lele/kembung, ayam, atau susu.</span>
            </div>
          </div>
          <div v-else class="space-y-2.5 text-xs text-slate-300">
            <div class="flex items-start gap-2.5 p-2 rounded-xl bg-amber-500/10 border border-amber-500/20">
              <span class="text-amber-400 font-bold">1.</span>
              <span>Pantau pertumbuhan lebih intensif di posyandu bulan berikutnya.</span>
            </div>
            <div class="flex items-start gap-2.5 p-2 rounded-xl bg-purple-500/10 border border-purple-500/20">
              <span class="text-purple-400 font-bold">2.</span>
              <span>Pastikan porsi gizi seimbang (makanan pokok, lauk hewani, sayur, buah) dan pola asuh higienis.</span>
            </div>
          </div>
        </div>

        <!-- Tombol Detail Lengkap -->
        <Link
          :href="route('ortu.anak.show', b.id)"
          class="w-full flex items-center justify-center gap-2 py-3 text-xs sm:text-sm bg-orbit-primary hover:bg-orbit-primary-light glow-primary text-white font-semibold rounded-2xl shadow-lg shadow-purple-600/25 transition-all active:scale-[0.98]"
        >
          <span>Buka Riwayat Tumbuh Kembang Lengkap</span>
          <ArrowRight class="w-4 h-4" />
        </Link>
      </div>

      <!-- Jika Kosong -->
      <div v-if="!balita || balita.length === 0" class="text-center py-16 bg-orbit-surface rounded-3xl border border-orbit-border p-6">
        <Baby class="w-12 h-12 text-slate-500 mx-auto mb-3 opacity-60" />
        <h3 class="text-sm font-semibold text-slate-200">Data Balita Belum Dihubungkan</h3>
        <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">
          Silakan hubungi kader posyandu untuk menghubungkan profil anak Anda ke akun ini.
        </p>
      </div>
    </div>
  </OrtuLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import {
  Baby,
  Calendar,
  Sparkles,
  ArrowRight
} from 'lucide-vue-next'
import OrtuLayout from '@/Layouts/OrtuLayout.vue'

defineProps({ balita: Array })
</script>