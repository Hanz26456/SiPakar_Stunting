<script setup>
import OrbitLogo from '@/Components/OrbitLogo.vue';
import { Link } from '@inertiajs/vue3';
import { Sun, Moon } from 'lucide-vue-next';
import { useTheme } from '@/Composables/useTheme';

const { theme, toggleTheme } = useTheme();
</script>

<template>
  <div class="relative min-h-screen flex flex-col justify-center items-center bg-orbit-bg text-slate-100 p-4 sm:p-6 overflow-hidden selection:bg-purple-500 selection:text-white">
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

    <!-- Center Box -->
    <div class="w-full sm:max-w-md relative z-10">
      <div class="flex flex-col items-center mb-6">
        <Link href="/" class="focus:outline-none">
          <OrbitLogo :collapsed="false" subtitle="Sistem Pakar Stunting" />
        </Link>
      </div>

      <div class="w-full bg-orbit-surface/90 backdrop-blur-2xl border border-orbit-border rounded-3xl p-6 sm:p-8 shadow-2xl">
        <slot />
      </div>
    </div>
  </div>
</template>
