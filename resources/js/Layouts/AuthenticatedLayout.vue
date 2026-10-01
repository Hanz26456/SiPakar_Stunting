<script setup>
import { ref, computed } from 'vue';
import OrbitLogo from '@/Components/OrbitLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Sun, Moon, LogOut, User } from 'lucide-vue-next';
import { useTheme } from '@/Composables/useTheme';

const { theme, toggleTheme } = useTheme();
const page = usePage();

const initials = computed(() => {
  const name = page.props.auth?.user?.name ?? '';
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
});
</script>

<template>
  <div class="min-h-screen bg-orbit-bg relative font-sans antialiased selection:bg-purple-500 selection:text-white">
    <!-- Ambient Orbit Glow -->
    <div class="orbit-glow-bg" />

    <!-- Top Navigation Menu -->
    <header class="border-b border-orbit-border bg-orbit-surface/85 backdrop-blur-xl sticky top-0 z-30">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between items-center">
          <div class="flex items-center gap-6">
            <Link :href="route('dashboard')" class="focus:outline-none">
              <OrbitLogo :collapsed="false" subtitle="Pengaturan Profil" />
            </Link>

            <Link
              :href="route('dashboard')"
              class="hidden sm:inline-flex text-xs font-semibold px-3 py-1.5 rounded-xl border border-orbit-border bg-orbit-surface2 hover:bg-white/5 text-slate-300 hover:text-white transition-colors"
            >
              ← Kembali ke Dashboard
            </Link>
          </div>

          <div class="flex items-center gap-3">
            <!-- Theme Toggle -->
            <button
              @click="toggleTheme"
              type="button"
              class="p-2 rounded-xl border border-orbit-border bg-orbit-surface hover:bg-white/5 text-slate-300 hover:text-white transition-colors focus:outline-none"
              :title="theme === 'dark' ? 'Mode Terang' : 'Mode Gelap'"
            >
              <Sun v-if="theme === 'dark'" class="w-4.5 h-4.5 text-amber-400" />
              <Moon v-else class="w-4.5 h-4.5 text-purple-400" />
            </button>

            <!-- User Badge -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-orbit-border bg-orbit-surface">
              <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-purple-600 to-cyan-600 flex items-center justify-center text-white text-xs font-bold">
                {{ initials }}
              </div>
              <span class="text-xs font-semibold text-slate-200 hidden sm:inline">
                {{ $page.props.auth.user.name }}
              </span>
            </div>

            <!-- Logout -->
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="p-2 rounded-xl text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors"
              title="Keluar"
            >
              <LogOut class="w-4.5 h-4.5" />
            </Link>
          </div>
        </div>
      </div>
    </header>

    <!-- Page Heading -->
    <header class="border-b border-orbit-border/50 bg-orbit-surface/30 relative z-10" v-if="$slots.header">
      <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- Page Content -->
    <main class="relative z-10">
      <slot />
    </main>
  </div>
</template>
