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
    class="inline-flex items-center justify-center gap-2 rounded-xl border border-orbit-border bg-orbit-surface hover:bg-white/5 text-slate-300 hover:text-white px-4 py-2.5 text-xs sm:text-sm font-medium tracking-wide transition-all duration-150 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-purple-500/30 disabled:opacity-50"
  >
    <slot />
  </button>
</template>
