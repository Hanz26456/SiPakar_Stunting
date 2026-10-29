<template>
  <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border transition-colors', colorClass]">
    <span class="w-1.5 h-1.5 rounded-full mr-1.5 flex-shrink-0" :class="dotClass" />
    <span>{{ label }}</span>
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: String,
  label: String,
})

const colorClass = computed(() => ({
  normal:         'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/20',
  berisiko:       'bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-500/20',
  stunting:       'bg-rose-500/10 text-rose-700 dark:text-rose-300 border-rose-500/20',
  stunting_berat: 'bg-rose-600/15 text-rose-800 dark:text-rose-200 border-rose-600/30',
}[props.status] ?? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700'))

const dotClass = computed(() => ({
  normal:         'bg-emerald-500',
  berisiko:       'bg-amber-500',
  stunting:       'bg-rose-500',
  stunting_berat: 'bg-rose-600',
}[props.status] ?? 'bg-zinc-400'))
</script>
