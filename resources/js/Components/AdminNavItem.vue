<template>
  <Link
    :href="href"
    :class="[
      'flex items-center gap-3 px-2 py-2 rounded-lg text-sm transition-colors',
      active
        ? 'bg-gray-700 text-white font-medium'
        : 'text-gray-500 hover:bg-gray-800 hover:text-gray-300'
    ]"
  >
    <span class="text-base flex-shrink-0 w-5 text-center">{{ icon }}</span>
    <span v-if="open" class="truncate">{{ label }}</span>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  href: String,
  active: Boolean,
  open: Boolean,
  icon: String,
  label: String,
})

console.log(props.href)
</script>