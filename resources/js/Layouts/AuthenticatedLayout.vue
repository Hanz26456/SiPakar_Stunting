<script setup>
import { computed } from 'vue';
import MetisLogo from '@/Components/MetisLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Sun, Moon, LogOut, ArrowLeft } from 'lucide-vue-next';
import { useTheme } from '@/Composables/useTheme';

const { theme, toggleTheme } = useTheme();
const page = usePage();

const initials = computed(() => {
  const name = page.props.auth?.user?.name ?? '';
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
});
</script>

<template>
  <div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased transition-colors duration-150">

    <!-- Top Navigation Menu -->
    <header class="border-b border-zinc-200 dark:border-zinc-800 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md sticky top-0 z-30 transition-colors">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-14 justify-between items-center">
          <div class="flex items-center gap-6">
            <Link :href="route('dashboard')" class="focus:outline-none">
              <MetisLogo :collapsed="false" subtitle="Pengaturan Profil" />
            </Link>

            <Link
              :href="route('dashboard')"
              class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700/60 text-zinc-700 dark:text-zinc-200 transition-colors shadow-2xs"
            >
              <ArrowLeft class="w-3.5 h-3.5" />
              <span>Kembali ke Dashboard</span>
            </Link>
          </div>

          <div class="flex items-center gap-3">
            <!-- Theme Toggle -->
            <button
              @click="toggleTheme"
              type="button"
              class="p-2 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 transition-colors focus:outline-none shadow-2xs"
              :title="theme === 'dark' ? 'Mode Terang' : 'Mode Gelap'"
            >
              <Sun v-if="theme === 'dark'" class="w-4 h-4 text-amber-400" />
              <Moon v-else class="w-4 h-4 text-blue-600" />
            </button>

            <!-- User Info & Logout -->
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 flex items-center justify-center text-xs font-bold font-mono">
                {{ initials }}
              </div>
              <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 hidden sm:inline">
                {{ $page.props.auth.user.name }}
              </span>
            </div>

            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="p-2 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-colors"
              title="Keluar"
            >
              <LogOut class="w-4 h-4" />
            </Link>
          </div>
        </div>
      </div>
    </header>

    <!-- Page Content -->
    <main class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
      <slot />
    </main>
  </div>
</template>
