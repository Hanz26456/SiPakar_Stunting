<template>
  <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', colorClass]">
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: String,  // 'normal' | 'berisiko' | 'stunting' | 'stunting_berat'
  label: String,
})

const colorClass = computed(() => ({
  normal:         'bg-green-100 text-green-800',
  berisiko:       'bg-amber-100 text-amber-800',
  stunting:       'bg-red-100 text-red-800',
  stunting_berat: 'bg-red-200 text-red-900',
}[props.status] ?? 'bg-gray-100 text-gray-600'))
</script>
