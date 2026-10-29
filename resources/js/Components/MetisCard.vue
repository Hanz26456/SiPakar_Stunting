<template>
  <div
    :class="[
      'rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 transition-colors',
      interactive ? 'cursor-pointer hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50' : '',
    ]"
  >
    <div v-if="$slots.header || title" class="px-5 py-3.5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
      <slot name="header">
        <h3 class="text-sm font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ title }}</h3>
        <p v-if="subtitle" class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ subtitle }}</p>
      </slot>
      <div v-if="$slots.actions" class="flex items-center gap-2">
        <slot name="actions" />
      </div>
    </div>

    <div :class="noPadding ? '' : 'p-5'">
      <slot />
    </div>

    <div v-if="$slots.footer" class="px-5 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/30">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  subtitle: String,
  interactive: {
    type: Boolean,
    default: false,
  },
  noPadding: {
    type: Boolean,
    default: false,
  },
})
</script>

