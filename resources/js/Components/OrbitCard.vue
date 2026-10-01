<template>
  <div
    :class="[
      'rounded-2xl border border-orbit-border bg-orbit-surface transition-all duration-200 overflow-hidden shadow-sm',
      glass ? 'glass' : '',
      hover ? 'hover:border-orbit-border2 hover:shadow-lg' : ''
    ]"
  >
    <slot />
  </div>
</template>

<script setup>
defineProps({
  glass: {
    type: Boolean,
    default: false
  },
  hover: {
    type: Boolean,
    default: false
  }
})
</script>
