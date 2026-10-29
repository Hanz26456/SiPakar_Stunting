<script setup>
defineProps({
  type: {
    type: String,
    default: 'button',
  },
})
</script>

<template>
  <button
    :type="type"
    class="inline-flex items-center justify-center gap-2 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/60 text-zinc-700 dark:text-zinc-200 px-4 py-2 text-xs sm:text-sm font-medium tracking-wide transition-all duration-150 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-zinc-400/30 disabled:opacity-50 shadow-2xs"
  >
    <slot />
  </button>
</template>
