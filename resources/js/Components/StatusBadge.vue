<template>
  <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border transition-colors', colorClass]">
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
  normal:         'bg-emerald-500/10 text-emerald-400 border-emerald-500/25',
  berisiko:       'bg-amber-500/10 text-amber-400 border-amber-500/25',
  stunting:       'bg-rose-500/10 text-rose-400 border-rose-500/25',
  stunting_berat: 'bg-rose-500/20 text-rose-300 border-rose-500/30',
}[props.status] ?? 'bg-slate-500/10 text-slate-300 border-slate-500/20'))

const dotClass = computed(() => ({
  normal:         'bg-emerald-400',
  berisiko:       'bg-amber-400',
  stunting:       'bg-rose-400',
  stunting_berat: 'bg-rose-500',
}[props.status] ?? 'bg-slate-400'))
</script>
