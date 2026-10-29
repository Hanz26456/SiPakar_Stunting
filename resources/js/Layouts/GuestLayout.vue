<script setup>
import MetisLogo from '@/Components/MetisLogo.vue';
import { Link } from '@inertiajs/vue3';
import { Sun, Moon } from 'lucide-vue-next';
import { useTheme } from '@/Composables/useTheme';

const { theme, toggleTheme } = useTheme();
</script>

<template>
  <div class="relative min-h-screen flex flex-col justify-center items-center bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 p-4 sm:p-6 overflow-hidden transition-colors duration-150">

    <!-- Top Right Theme Switcher -->
    <div class="absolute top-4 right-4 z-20">
      <button
        @click="toggleTheme"
        type="button"
        class="p-2 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 transition-colors focus:outline-none shadow-2xs"
        :title="theme === 'dark' ? 'Mode Terang' : 'Mode Gelap'"
      >
        <Sun v-if="theme === 'dark'" class="w-4 h-4 text-amber-400" />
        <Moon v-else class="w-4 h-4 text-blue-600" />
      </button>
    </div>

    <!-- Center Box -->
    <div class="w-full sm:max-w-md relative z-10">
      <div class="flex flex-col items-center mb-6">
        <Link href="/" class="focus:outline-none">
          <MetisLogo :collapsed="false" subtitle="Sistem Pakar Stunting Posyandu" />
        </Link>
      </div>

      <div class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 sm:p-8 shadow-xl">
        <slot />
      </div>
    </div>
  </div>
</template>
