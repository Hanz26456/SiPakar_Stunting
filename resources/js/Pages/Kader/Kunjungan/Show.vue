<template>
  <KaderLayout title="Detail Kunjungan Balita">
    <div class="space-y-5">
      <!-- Breadcrumb -->
      <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-400">
        <Link :href="route('kader.kunjungan.index')" class="hover:text-orbit-primary transition-colors">Daftar Kunjungan</Link>
        <span>›</span>
        <span class="text-slate-600 dark:text-slate-300 font-medium">{{ balita.nama }} ({{ kunjungan.tanggal }})</span>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Kolom kiri -->
        <div class="space-y-5">
          <!-- Info balita -->
          <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
            <div class="flex items-center gap-3.5 mb-3.5 pb-3.5 border-b border-slate-100 dark:border-orbit-border/50">
              <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-orbit-primary flex items-center justify-center flex-shrink-0">
                <Baby class="w-6 h-6" />
              </div>
              <div class="min-w-0">
                <div class="font-bold text-slate-800 dark:text-slate-100 truncate text-base">{{ balita.nama }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{{ balita.usia_format }} · {{ balita.jenis_kelamin }}</div>
              </div>
            </div>
            <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 space-y-1.5">
              <div>Nama Ibu: <strong class="text-slate-700 dark:text-slate-200">{{ balita.nama_ibu }}</strong></div>
              <div v-if="balita.desa">Desa: <strong class="text-slate-700 dark:text-slate-200">{{ balita.desa }}</strong></div>
            </div>
            <Link
              :href="route('kader.balita.show', balita.id)"
              class="mt-4 flex items-center justify-center gap-1.5 text-xs font-semibold text-orbit-primary border border-purple-500/30 bg-purple-500/10 hover:bg-purple-500/20 py-2 rounded-xl transition-colors w-full"
            >
              <span>Lihat Profil Lengkap Balita</span>
              <ArrowRight class="w-3.5 h-3.5" />
            </Link>
          </div>

          <!-- Data pengukuran -->
          <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200 mb-3.5">
              <Ruler class="w-4 h-4 text-orbit-primary" />
              <span>Data Antropometri Kunjungan</span>
            </div>
            <div class="space-y-2.5 text-xs sm:text-sm">
              <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
                <span class="text-slate-400">Tanggal Pemeriksaan</span>
                <span class="font-mono font-medium text-slate-700 dark:text-slate-200">{{ kunjungan.tanggal }}</span>
              </div>
              <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
                <span class="text-slate-400">Usia Saat Pemeriksaan</span>
                <span class="font-medium text-slate-700 dark:text-slate-200">{{ kunjungan.usia_bulan }} bulan</span>
              </div>
              <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
                <span class="text-slate-400">Berat Badan</span>
                <span class="font-mono font-bold text-slate-800 dark:text-slate-100">{{ kunjungan.berat_badan }} kg</span>
              </div>
              <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
                <span class="text-slate-400">Tinggi / Panjang Badan</span>
                <span class="font-mono font-bold text-slate-800 dark:text-slate-100">{{ kunjungan.tinggi_badan }} cm</span>
              </div>
              <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
                <span class="text-slate-400">Lingkar Lengan Atas (LiLA)</span>
                <span class="font-mono font-medium text-slate-700 dark:text-slate-200">{{ kunjungan.lila ? kunjungan.lila + ' cm' : '—' }}</span>
              </div>
              <div class="flex justify-between items-center py-1">
                <span class="text-slate-400">Lingkar Kepala</span>
                <span class="font-mono font-medium text-slate-700 dark:text-slate-200">{{ kunjungan.lingkar_kepala ? kunjungan.lingkar_kepala + ' cm' : '—' }}</span>
              </div>
            </div>
          </div>

          <!-- Z-score -->
          <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200 mb-3.5">
              <BarChart2 class="w-4 h-4 text-orbit-primary" />
              <span>Hasil Z-Score (Standar WHO 2006)</span>
            </div>
            <div class="space-y-4">
              <div>
                <div class="flex justify-between items-center mb-1">
                  <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Z-Score TB/U</span>
                  <span class="text-xs sm:text-sm font-mono font-bold" :class="zColor(kunjungan.zscore_tbu)">
                    {{ kunjungan.zscore_tbu != null ? kunjungan.zscore_tbu.toFixed(2) : '—' }} SD
                  </span>
                </div>
                <div class="h-2 bg-slate-100 dark:bg-orbit-surface2 rounded-full overflow-hidden">
                  <div
                    class="h-full rounded-full transition-all"
                    :class="zBarColor(kunjungan.zscore_tbu)"
                    :style="{ width: Math.min(100, Math.max(2, (kunjungan.zscore_tbu + 3) / 6 * 100)) + '%' }"
                  ></div>
                </div>
                <div class="text-xs mt-1.5 font-semibold" :class="zColor(kunjungan.zscore_tbu)">{{ kunjungan.status_zscore }}</div>
              </div>
              <div v-if="kunjungan.zscore_bbu != null">
                <div class="flex justify-between items-center mb-1">
                  <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Z-Score BB/U</span>
                  <span class="text-xs sm:text-sm font-mono font-bold" :class="zColor(kunjungan.zscore_bbu)">
                    {{ kunjungan.zscore_bbu.toFixed(2) }} SD
                  </span>
                </div>
                <div class="h-2 bg-slate-100 dark:bg-orbit-surface2 rounded-full overflow-hidden">
                  <div
                    class="h-full rounded-full transition-all"
                    :class="zBarColor(kunjungan.zscore_bbu)"
                    :style="{ width: Math.min(100, Math.max(2, (kunjungan.zscore_bbu + 3) / 6 * 100)) + '%' }"
                  ></div>
                </div>
              </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-orbit-border/60 space-y-1.5">
              <div class="text-[11px] text-slate-400 uppercase tracking-wider font-semibold mb-1">Kategori Standar WHO:</div>
              <div class="flex items-center gap-2 text-xs"><div class="w-2 h-2 rounded-full bg-rose-600"></div><span class="text-slate-500 dark:text-slate-400">&lt; −3 SD → Severely Stunted</span></div>
              <div class="flex items-center gap-2 text-xs"><div class="w-2 h-2 rounded-full bg-rose-500"></div><span class="text-slate-500 dark:text-slate-400">−3 s/d −2 SD → Stunted</span></div>
              <div class="flex items-center gap-2 text-xs"><div class="w-2 h-2 rounded-full bg-amber-500"></div><span class="text-slate-500 dark:text-slate-400">−2 s/d −1 SD → Berisiko</span></div>
              <div class="flex items-center gap-2 text-xs"><div class="w-2 h-2 rounded-full bg-emerald-500"></div><span class="text-slate-500 dark:text-slate-400">&gt; −1 SD → Normal</span></div>
            </div>
          </div>

          <div class="text-xs text-slate-400 text-center font-medium">
            Pemeriksa: <strong class="text-slate-700 dark:text-slate-200">{{ kunjungan.kader }}</strong>
          </div>
        </div>

        <!-- Kolom tengah + kanan -->
        <div class="lg:col-span-2 space-y-5">
          <!-- Tren 6 bulan -->
          <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
              <TrendingUp class="w-4 h-4 text-orbit-primary" />
              <span>Tren Pertumbuhan 6 Bulan Terakhir</span>
            </div>
            <div v-if="tren.length === 0" class="text-xs sm:text-sm text-slate-400 text-center py-6 italic">
              Belum ada data riwayat kunjungan sebelumnya
            </div>
            <div v-else class="space-y-5">
              <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Grafik Berat Badan (kg)</div>
                <div class="flex items-end gap-2.5 h-24 pt-2">
                  <div v-for="(t, i) in tren" :key="t.bulan" class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                    <div class="text-slate-700 dark:text-slate-200 font-mono font-bold text-[10px]">{{ t.berat_badan }}</div>
                    <div
                      class="w-full rounded-t-lg transition-all"
                      :class="i > 0 && t.berat_badan < tren[i-1].berat_badan ? 'bg-rose-400/80 dark:bg-rose-500/60' : 'bg-purple-500/80 dark:bg-orbit-primary'"
                      :style="{ height: (t.berat_badan / Math.max(...tren.map(x=>x.berat_badan)) * 64) + 'px' }"
                    ></div>
                    <div class="text-slate-400 text-[10px] font-medium">{{ t.bulan.split(' ')[0] }}</div>
                  </div>
                </div>
              </div>
              <div class="space-y-3 pt-3 border-t border-slate-100 dark:border-orbit-border/60">
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Riwayat Z-Score TB/U per Bulan</div>
                <div v-for="t in tren" :key="t.bulan" class="flex items-center gap-3">
                  <div class="text-xs text-slate-400 w-14 flex-shrink-0 font-medium">{{ t.bulan.split(' ')[0] }}</div>
                  <div class="flex-1 h-2 bg-slate-100 dark:bg-orbit-surface2 rounded-full overflow-hidden">
                    <div
                      class="h-full rounded-full transition-all"
                      :class="zBarColor(t.zscore_tbu)"
                      :style="{ width: Math.min(100, Math.max(2, (t.zscore_tbu + 3) / 6 * 100)) + '%' }"
                    ></div>
                  </div>
                  <span class="text-xs font-mono font-semibold w-16 text-right" :class="zColor(t.zscore_tbu)">
                    {{ t.zscore_tbu != null ? t.zscore_tbu.toFixed(1) : '—' }} SD
                  </span>
                  <span class="text-xs font-mono w-14 text-right text-slate-500 dark:text-slate-400">{{ t.berat_badan }} kg</span>
                  <span
                    class="text-[11px] font-semibold w-24 text-center px-2 py-0.5 rounded-full"
                    :class="{
                      'bg-rose-500/10 text-rose-500 border border-rose-500/20': ['Stunting berat','Stunting'].includes(t.status),
                      'bg-amber-500/10 text-amber-500 border border-amber-500/20': t.status === 'Berisiko',
                      'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20': t.status === 'Normal'
                    }"
                  >
                    {{ t.status }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Status diagnosis -->
          <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-orbit-border/60">
              <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200">
                <Activity class="w-4 h-4 text-orbit-primary" />
                <span>Hasil Diagnosis Sistem Pakar</span>
              </div>
              <span v-if="diagnosis?.sudah_diverifikasi" class="inline-flex items-center gap-1 text-[11px] font-semibold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 px-2.5 py-0.5 rounded-full">
                <CheckCircle2 class="w-3.5 h-3.5" />
                <span>Terverifikasi Bidan</span>
              </span>
            </div>

            <div v-if="diagnosis">
              <div
                class="rounded-2xl p-4.5 mb-4 border transition-all"
                :class="{
                  'bg-rose-500/10 border-rose-500/20': ['stunting','stunting_berat'].includes(diagnosis.status_final),
                  'bg-amber-500/10 border-amber-500/20': diagnosis.status_final === 'berisiko',
                  'bg-emerald-500/10 border-emerald-500/20': diagnosis.status_final === 'normal'
                }"
              >
                <div class="flex items-center justify-between">
                  <div>
                    <div class="text-base sm:text-lg font-bold mb-1" :class="cfColor(diagnosis.cf_kombinasi)">
                      {{ diagnosis.label_status }}
                    </div>
                    <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                      Nilai Kepastian (CF): <strong class="font-mono">{{ diagnosis.cf_persen }}</strong>
                    </div>
                  </div>
                  <div class="text-3xl sm:text-4xl font-mono font-extrabold" :class="cfColor(diagnosis.cf_kombinasi)">
                    {{ diagnosis.cf_persen }}
                  </div>
                </div>
                <div class="mt-3">
                  <div class="h-2 bg-slate-200/50 dark:bg-orbit-surface2 rounded-full overflow-hidden">
                    <div
                      class="h-full rounded-full transition-all"
                      :class="cfBarColor(diagnosis.cf_kombinasi)"
                      :style="{ width: (diagnosis.cf_kombinasi * 100) + '%' }"
                    ></div>
                  </div>
                </div>
                <div v-if="diagnosis.rekomendasi" class="mt-3.5 text-xs text-slate-700 dark:text-slate-200 bg-white/70 dark:bg-orbit-surface/80 rounded-xl p-3 border border-slate-200/40 dark:border-orbit-border leading-relaxed font-medium">
                  {{ diagnosis.rekomendasi }}
                </div>
              </div>
              <Link
                :href="route('kader.diagnosis.show', diagnosis.id)"
                class="flex items-center justify-center gap-2 py-2.5 px-4 text-xs sm:text-sm font-semibold border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface2 hover:bg-slate-50 dark:hover:bg-white/5 text-slate-700 dark:text-slate-200 rounded-xl transition-all shadow-2xs"
              >
                <span>Lihat Detail Perhitungan CF</span>
                <ArrowRight class="w-4 h-4" />
              </Link>
            </div>

            <div v-else class="text-center py-8">
              <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-orbit-primary flex items-center justify-center mx-auto mb-3">
                <Sparkles class="w-6 h-6" />
              </div>
              <div class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">Diagnosis Belum Dilakukan</div>
              <div class="text-xs text-slate-400 mb-4 max-w-sm mx-auto">Kunjungan ini belum dihitung tingkat kepastian Certainty Factor berdasarkan gejala.</div>
              <Link
                :href="route('kader.diagnosis.create', kunjungan.id)"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-xs sm:text-sm bg-orbit-primary hover:bg-orbit-primary/90 text-white rounded-xl shadow-md shadow-purple-600/20 font-semibold transition-all active:scale-[0.98]"
              >
                <span>Jalankan Diagnosis Sekarang</span>
                <ArrowRight class="w-4 h-4" />
              </Link>
            </div>
          </div>

          <div v-if="kunjungan.catatan" class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">
              <FileText class="w-4 h-4 text-orbit-primary" />
              <span>Catatan Pemeriksaan Kader</span>
            </div>
            <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">{{ kunjungan.catatan }}</div>
          </div>
        </div>
      </div>
    </div>
  </KaderLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import {
  Baby,
  ArrowRight,
  Ruler,
  BarChart2,
  TrendingUp,
  Activity,
  CheckCircle2,
  Sparkles,
  FileText
} from 'lucide-vue-next'
import KaderLayout from '@/Layouts/KaderLayout.vue'

defineProps({ kunjungan: Object, balita: Object, diagnosis: Object, tren: Array })

const zColor = (z) => {
  if (z == null) return 'text-slate-400'
  if (z < -2) return 'text-rose-500'
  if (z < -1) return 'text-amber-500'
  return 'text-emerald-500'
}

const zBarColor = (z) => {
  if (z == null) return 'bg-slate-300 dark:bg-slate-700'
  if (z < -2) return 'bg-rose-500'
  if (z < -1) return 'bg-amber-500'
  return 'bg-emerald-500'
}

const cfColor = (cf) => {
  if (cf >= 0.7) return 'text-rose-500'
  if (cf >= 0.4) return 'text-amber-500'
  return 'text-emerald-500'
}

const cfBarColor = (cf) => {
  if (cf >= 0.7) return 'bg-rose-500'
  if (cf >= 0.4) return 'bg-amber-500'
  return 'bg-emerald-500'
}
</script>
