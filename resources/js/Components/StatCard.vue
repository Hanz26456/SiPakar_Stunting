<!-- StatCard.vue -->
<template>
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-2">
      <div class="text-xs text-gray-400">{{ label }}</div>
      <span class="text-lg">{{ icon }}</span>
    </div>
    <div class="text-2xl font-semibold" :class="valueColor">{{ value }}</div>
    <div v-if="sub" class="text-xs mt-1" :class="subColor">{{ sub }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({ label: String, value: [Number, String], color: String, icon: String, sub: String })
const valueColor = computed(() => ({
  green: 'text-green-600', amber: 'text-amber-600',
  red: 'text-red-600', gray: 'text-gray-800',
}[props.color] ?? 'text-gray-800'))
const subColor = computed(() => ({
  green: 'text-green-500', amber: 'text-amber-500',
  red: 'text-red-500', gray: 'text-gray-400',
}[props.color] ?? 'text-gray-400'))
</script>
