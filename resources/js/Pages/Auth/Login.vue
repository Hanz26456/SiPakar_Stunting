<template>
  <div class="min-h-screen bg-orbit-bg text-slate-100 flex items-center justify-center p-4 relative overflow-hidden font-sans antialiased selection:bg-purple-500 selection:text-white">
    <!-- Ambient Orbit Glow -->
    <div class="orbit-glow-bg" />

    <!-- Top Right Theme Switcher -->
    <div class="absolute top-4 right-4 z-20">
      <button
        @click="toggleTheme"
        type="button"
        class="p-2.5 rounded-xl border border-orbit-border bg-orbit-surface/80 hover:bg-white/5 text-slate-300 hover:text-white transition-colors focus:outline-none"
        :title="theme === 'dark' ? 'Mode Terang' : 'Mode Gelap'"
      >
        <Sun v-if="theme === 'dark'" class="w-4.5 h-4.5 text-amber-400" />
        <Moon v-else class="w-4.5 h-4.5 text-purple-400" />
      </button>
    </div>

    <!-- Login Container -->
    <div class="w-full max-w-md relative z-10">
      <!-- Logo + Header -->
      <div class="text-center mb-6">
        <div class="flex justify-center mb-3">
          <div class="w-14 h-14 rounded-2xl bg-orbit-primary flex items-center justify-center glow-primary shadow-xl shadow-purple-600/30">
            <svg viewBox="0 0 32 32" fill="none" class="w-8 h-8">
              <circle cx="16" cy="16" r="4" fill="white" />
              <ellipse cx="16" cy="16" rx="12" ry="5.5" stroke="white" stroke-width="1.8" stroke-opacity="0.8" transform="rotate(-30 16 16)" />
              <circle cx="24" cy="11" r="2.2" fill="#06B6D4" />
            </svg>
          </div>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-100">SiPakar Stunting</h1>
        <p class="text-slate-400 text-xs sm:text-sm mt-1">Posyandu Melati 3, Pujer — Bondowoso</p>
      </div>

      <!-- Form Card -->
      <div class="bg-orbit-surface/90 backdrop-blur-2xl rounded-3xl border border-orbit-border p-6 sm:p-8 shadow-2xl">
        <div class="mb-5">
          <h2 class="text-base font-semibold text-slate-200">Masuk ke Akun</h2>
          <p class="text-xs text-slate-400 mt-0.5">Gunakan email dan password terdaftar Anda</p>
        </div>

        <!-- Flash error dari Laravel -->
        <div v-if="$page.props.flash?.error" class="bg-rose-500/15 border border-rose-500/30 text-rose-300 text-sm px-4 py-3 rounded-2xl mb-4 flex items-center gap-2">
          <AlertCircle class="w-4 h-4 text-rose-400 flex-shrink-0" />
          <span>{{ $page.props.flash.error }}</span>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
          <!-- Email -->
          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1.5">
              Alamat Email
            </label>
            <input
              v-model="form.email"
              type="email"
              placeholder="email@posyandu.id"
              autocomplete="email"
              class="w-full bg-orbit-surface2 border border-orbit-border rounded-xl px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all"
              :class="form.errors.email ? 'border-rose-500/60 bg-rose-500/5' : ''"
              required
            >
            <div v-if="form.errors.email" class="text-xs text-rose-400 mt-1">
              {{ form.errors.email }}
            </div>
          </div>

          <!-- Password -->
          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1.5">
              Kata Sandi
            </label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••"
                autocomplete="current-password"
                class="w-full bg-orbit-surface2 border border-orbit-border rounded-xl px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 pr-10 transition-all"
                :class="form.errors.password ? 'border-rose-500/60 bg-rose-500/5' : ''"
                required
              >
              <button
                type="button"
                class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-200 transition-colors p-0.5 focus:outline-none"
                @click="showPassword = !showPassword"
              >
                <EyeOff v-if="showPassword" class="w-4 h-4" />
                <Eye v-else class="w-4 h-4" />
              </button>
            </div>
            <div v-if="form.errors.password" class="text-xs text-rose-400 mt-1">
              {{ form.errors.password }}
            </div>
          </div>

          <!-- Remember me -->
          <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-400 hover:text-slate-300 transition-colors">
              <input
                id="remember"
                v-model="form.remember"
                type="checkbox"
                class="w-4 h-4 rounded border-orbit-border bg-orbit-surface2 text-purple-600 focus:ring-purple-500/30 focus:ring-offset-0"
              >
              <span>Ingat saya di perangkat ini</span>
            </label>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-3 bg-orbit-primary hover:bg-orbit-primary-light text-white text-sm font-semibold rounded-xl glow-primary shadow-lg shadow-purple-600/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-[0.98] mt-2 flex items-center justify-center gap-2"
          >
            <span v-if="form.processing" class="flex items-center justify-center gap-2">
              <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>Memproses...</span>
            </span>
            <span v-else class="flex items-center gap-1.5">
              <span>Masuk Sekarang</span>
              <ArrowRight class="w-4 h-4" />
            </span>
          </button>
        </form>
      </div>

      <!-- Demo Accounts Card -->
      <div class="mt-5 bg-orbit-surface/80 backdrop-blur-xl rounded-2xl border border-orbit-border p-4 shadow-lg">
        <div class="text-[10px] font-semibold text-slate-400 mb-2.5 uppercase tracking-wider">
          Akun Demo (Klik untuk Mengisi Cepat)
        </div>
        <div class="space-y-1.5">
          <div
            v-for="akun in akunDemo" :key="akun.role"
            class="flex items-center gap-3 p-2 rounded-xl hover:bg-white/5 border border-transparent hover:border-orbit-border cursor-pointer transition-all group"
            @click="isiAkun(akun)"
          >
            <div
              class="w-7 h-7 rounded-lg flex items-center justify-center text-xs flex-shrink-0 border font-bold"
              :class="akun.badgeClass"
            >
              {{ akun.role.slice(0, 1).toUpperCase() }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="text-xs font-semibold text-slate-200 group-hover:text-white">{{ akun.label }}</div>
              <div class="text-[11px] text-slate-400 truncate">{{ akun.email }}</div>
            </div>
            <span class="text-[11px] text-orbit-primary-light font-medium flex-shrink-0 group-hover:underline">
              Pilih
            </span>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <p class="text-center text-xs text-slate-500 mt-6">
        Sistem Pakar Diagnosis Dini Stunting · Polije © 2025
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Sun, Moon, Eye, EyeOff, ArrowRight, AlertCircle, Sparkles } from 'lucide-vue-next'
import { useTheme } from '@/Composables/useTheme'

const { theme, toggleTheme } = useTheme()
const showPassword = ref(false)

const form = useForm({
  email:    '',
  password: '',
  remember: false,
})

const akunDemo = [
  { role: 'admin', label: 'Administrator', email: 'admin@posyandu.id', password: 'password', badgeClass: 'bg-purple-500/15 text-purple-300 border-purple-500/30' },
  { role: 'bidan', label: 'Bidan / Nakes', email: 'bidan@posyandu.id', password: 'password', badgeClass: 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30' },
  { role: 'kader', label: 'Kader Posyandu', email: 'kader@posyandu.id', password: 'password', badgeClass: 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' },
]

const isiAkun = (akun) => {
  form.email    = akun.email
  form.password = akun.password
}

const submit = () => {
  form.post(route('login'))
}
</script>