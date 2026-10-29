<template>
  <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-5 transition-colors">
    <div class="flex items-start justify-between gap-2">
      <div>
        <p class="text-[11px] font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1">
          {{ label }}
        </p>
        <div class="text-2xl sm:text-3xl font-bold font-mono tracking-tight text-zinc-900 dark:text-zinc-100">
          {{ value }}
        </div>
      </div>
      <div
        v-if="$slots.icon || icon"
        class="w-9 h-9 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center flex-shrink-0"
      >
        <slot name="icon">
          <span>{{ icon }}</span>
        </slot>
      </div>
    </div>
    <div v-if="sub" class="text-xs text-zinc-500 dark:text-zinc-400 mt-2 font-normal">
      {{ sub }}
    </div>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  value: [Number, String],
  color: {
    type: String,
    default: 'default'
  },
  icon: String,
  sub: String,
})
</script>
