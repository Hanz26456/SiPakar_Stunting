<template>
  <div class="bg-orbit-surface rounded-2xl border border-orbit-border hover:border-orbit-border2 p-5 transition-colors duration-150">
    <div class="flex items-center justify-between gap-2 mb-2">
      <span class="text-xs font-medium text-slate-400">
        {{ label }}
      </span>
      <div v-if="$slots.icon || icon" class="text-slate-500">
        <slot name="icon">
          <span>{{ icon }}</span>
        </slot>
      </div>
    </div>
    
    <div class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-100">
      {{ value }}
    </div>
    
    <div v-if="sub" class="text-xs mt-1.5 font-normal text-slate-400">
      <span>{{ sub }}</span>
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
