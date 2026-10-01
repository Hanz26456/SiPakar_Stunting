<template>
  <div class="min-h-screen relative flex flex-col justify-between overflow-x-hidden font-sans antialiased selection:bg-indigo-500 selection:text-white">
    <!-- Health Background Illustration Layer -->
    <div
      class="fixed inset-0 bg-cover bg-bottom bg-no-repeat transition-all duration-300 pointer-events-none z-0 dark:brightness-[0.7] dark:contrast-[1.1]"
      style="background-image: url('/images/login-health-bg.jpg');"
    />

    <!-- Ambient Dark Overlay for Dark Mode Contrast -->
    <div class="fixed inset-0 bg-slate-900/5 dark:bg-slate-950/60 transition-colors pointer-events-none z-0" />

    <!-- Top Bar with Logo & Theme Toggle -->
    <header class="relative z-20 w-full px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border border-slate-200/90 dark:border-slate-800 shadow-sm">
        <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center shadow-xs">
          <svg viewBox="0 0 32 32" fill="none" class="w-4 h-4">
            <circle cx="16" cy="16" r="4" fill="white" />
            <ellipse cx="16" cy="16" rx="12" ry="5.5" stroke="white" stroke-width="1.8" stroke-opacity="0.8" transform="rotate(-30 16 16)" />
            <circle cx="24" cy="11" r="2.2" fill="#22D3EE" />
          </svg>
        </div>
        <span class="text-xs font-bold tracking-tight text-slate-900 dark:text-white uppercase">
          SiPakar Posyandu
        </span>
      </div>

      <button
        @click="toggleTheme"
        type="button"
        class="p-2 rounded-xl bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border border-slate-200/90 dark:border-slate-800 shadow-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors focus:outline-none"
        :title="theme === 'dark' ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'"
      >
        <Sun v-if="theme === 'dark'" class="w-4.5 h-4.5 text-amber-400" />
        <Moon v-else class="w-4.5 h-4.5 text-indigo-600" />
      </button>
    </header>

    <!-- Main Content Area -->
    <main class="relative z-10 flex-1 flex flex-col items-center justify-center px-4 py-3 sm:py-6 max-w-6xl mx-auto w-full">
      <!-- Top Center Header (Inspired by reference layout: 'FOCUS AS A SERVICE') -->
      <div class="text-center mb-5 sm:mb-6 max-w-xl px-4">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight text-indigo-900 dark:text-white uppercase drop-shadow-sm">
          SIPAKAR STUNTING
        </h1>
        <p class="text-xs sm:text-sm text-slate-800 dark:text-slate-200 mt-1.5 leading-relaxed font-semibold max-w-md mx-auto">
          Sistem Pakar Deteksi Dini Stunting Pada Balita Dengan Menggunakan Metode Certainty Factor Dengan Integrasi Data Pemantauan Tumbuh Kembang
        </p>
      </div>

      <!-- Center Floating Card (Inspired by reference card composition) -->
      <div class="w-full max-w-[390px] bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-200/90 dark:border-slate-800 rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-black/50 overflow-hidden transition-all">
        <!-- Card Body -->
        <div class="p-6 sm:p-7">
          <div class="mb-5">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
              Masuk ke akun
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
              Silakan masukkan kredensial Anda
            </p>
          </div>

          <!-- Flash error alert -->
          <div v-if="$page.props.flash?.error" class="bg-rose-50 dark:bg-rose-500/15 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs px-3.5 py-2.5 rounded-xl mb-4 flex items-center gap-2">
            <AlertCircle class="w-4 h-4 text-rose-500 flex-shrink-0" />
            <span>{{ $page.props.flash.error }}</span>
          </div>

          <form @submit.prevent="submit" class="space-y-4">
            <!-- Email -->
            <div>
              <label class="block text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1.5">
                Alamat Email
              </label>
              <input
                v-model="form.email"
                type="email"
                placeholder="nama@posyandu.id"
                autocomplete="email"
                class="w-full bg-slate-50/80 dark:bg-slate-800/90 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-indigo-600 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
                :class="form.errors.email ? 'border-rose-500 bg-rose-50/50 dark:bg-rose-500/10' : ''"
                required
              >
              <div v-if="form.errors.email" class="text-xs text-rose-500 mt-1">
                {{ form.errors.email }}
              </div>
            </div>

            <!-- Password -->
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                  Kata Sandi
                </label>
              </div>
              <div class="relative">
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="••••••••"
                  autocomplete="current-password"
                  class="w-full bg-slate-50/80 dark:bg-slate-800/90 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-indigo-600 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 pr-10 transition-all"
                  :class="form.errors.password ? 'border-rose-500 bg-rose-50/50 dark:bg-rose-500/10' : ''"
                  required
                >
                <button
                  type="button"
                  class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors focus:outline-none"
                  @click="showPassword = !showPassword"
                >
                  <EyeOff v-if="showPassword" class="w-4 h-4" />
                  <Eye v-else class="w-4 h-4" />
                </button>
              </div>
              <div v-if="form.errors.password" class="text-xs text-rose-500 mt-1">
                {{ form.errors.password }}
              </div>
            </div>

            <!-- Remember me -->
            <div class="flex items-center justify-between pt-0.5">
              <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 dark:text-slate-400">
                <input
                  id="remember"
                  v-model="form.remember"
                  type="checkbox"
                  class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500/30 dark:bg-slate-800"
                >
                <span>Ingat saya</span>
              </label>
            </div>

            <!-- Submit Button (Styled matching the reference's bold primary button) -->
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-2.5 sm:py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-md shadow-indigo-600/25 transition-all active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-1.5 mt-2"
            >
              <span v-if="form.processing">Memproses...</span>
              <span v-else class="flex items-center gap-1">
                <span>Masuk</span>
                <ArrowRight class="w-4 h-4" />
              </span>
            </button>
          </form>
        </div>

        <!-- Card Bottom Section: Demo Quick Login (matching bottom segment in reference image) -->
        <div class="border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/50 px-6 py-4">
          <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5 text-center">
            Pilihan Akun Demo (1-Klik)
          </div>
          <div class="grid grid-cols-3 gap-2">
            <button
              v-for="akun in akunDemo"
              :key="akun.role"
              type="button"
              @click="isiAkun(akun)"
              class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 text-xs font-medium text-center transition-colors shadow-2xs truncate"
            >
              {{ akun.label }}
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer Copyright with subtle glass pill badge -->
    <footer class="relative z-10 w-full py-3 flex justify-center px-4">
      <div class="px-4 py-1.5 rounded-full bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200/90 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-200 font-semibold shadow-md">
        Politeknik Negeri Jember & Posyandu Melati Pujer © 2026
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Sun, Moon, Eye, EyeOff, ArrowRight, AlertCircle } from 'lucide-vue-next'
import { useTheme } from '@/Composables/useTheme'

const { theme, toggleTheme } = useTheme()
const showPassword = ref(false)

const form = useForm({
  email:    '',
  password: '',
  remember: false,
})

const akunDemo = [
  { role: 'admin', label: 'Admin', email: 'admin@posyandu.id', password: 'password' },
  { role: 'bidan', label: 'Bidan', email: 'bidan@posyandu.id', password: 'password' },
  { role: 'kader', label: 'Kader', email: 'kader@posyandu.id', password: 'password' },
]

const isiAkun = (akun) => {
  form.email    = akun.email
  form.password = akun.password
}

const submit = () => {
  form.post(route('login'))
}
</script>