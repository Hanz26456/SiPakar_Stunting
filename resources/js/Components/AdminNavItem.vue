<template>
  <Link
    :href="href"
    :title="!open ? label : undefined"
    :class="[
      'flex items-center gap-3 px-3 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors duration-150 group relative',
      active
        ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white font-semibold'
        : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/70 dark:hover:bg-zinc-800/60'
    ]"
  >
    <!-- Metis 2px Left Blue Accent Rail -->
    <div
      v-if="active"
      class="absolute left-0 top-1.5 bottom-1.5 w-[3px] bg-blue-600 rounded-r"
    />
    <span :class="['flex-shrink-0 transition-colors', active ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300']">
      <slot name="icon">
        <span v-if="icon" class="text-base flex-shrink-0 w-5 text-center">{{ icon }}</span>
      </slot>
    </span>
    <span v-if="open" class="truncate flex-1 tracking-tight">{{ label }}</span>
    <slot name="badge" />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href: String,
  active: Boolean,
  open: {
    type: Boolean,
    default: true
  },
  icon: String,
  label: String,
})
</script>
