<template>
  <div>
    <div class="flex items-end gap-2 h-24 mb-2">
      <div v-for="d in data" :key="d.bulan" class="flex-1 flex flex-col items-center gap-1">
        <div class="w-full flex flex-col-reverse gap-0.5" style="height:80px">
          <div
            v-if="d.stunting"
            :style="{ height: pct(d.stunting, d.total) + '%' }"
            class="w-full bg-red-400 rounded-sm transition-all"
          ></div>
          <div
            v-if="d.berisiko"
            :style="{ height: pct(d.berisiko, d.total) + '%' }"
            class="w-full bg-amber-300 rounded-sm transition-all"
          ></div>
          <div
            v-if="d.normal"
            :style="{ height: pct(d.normal, d.total) + '%' }"
            class="w-full bg-emerald-300 rounded-sm transition-all"
          ></div>
        </div>
        <div class="text-xs text-gray-400">{{ d.bulan.split(' ')[0] }}</div>
      </div>
    </div>
    <div class="flex gap-4 mt-2">
      <div class="flex items-center gap-1.5 text-xs text-gray-500">
        <div class="w-3 h-3 rounded-sm bg-emerald-300"></div> Normal
      </div>
      <div class="flex items-center gap-1.5 text-xs text-gray-500">
        <div class="w-3 h-3 rounded-sm bg-amber-300"></div> Berisiko
      </div>
      <div class="flex items-center gap-1.5 text-xs text-gray-500">
        <div class="w-3 h-3 rounded-sm bg-red-400"></div> Stunting
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ data: Array })
const pct = (val, total) => total > 0 ? Math.round(val / total * 100) : 0
</script>
