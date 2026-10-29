<template>
  <div>
    <div v-if="!data || data.length === 0" class="text-xs text-zinc-500 text-center py-8">
      Belum ada data distribusi riwayat
    </div>
    <div v-else>
      <div class="flex items-end gap-2.5 sm:gap-4 h-32 mb-3 px-1">
        <div v-for="d in data" :key="d.bulan" class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group">
          <!-- Stack bar container -->
          <div class="w-full max-w-[48px] flex flex-col-reverse gap-1 h-[105px] bg-zinc-100 dark:bg-zinc-800/60 rounded-lg p-1 border border-zinc-200 dark:border-zinc-800 group-hover:border-zinc-300 dark:group-hover:border-zinc-700 transition-colors">
            <div
              v-if="d.stunting"
              :style="{ height: pct(d.stunting, d.total) + '%' }"
              class="w-full bg-red-500 rounded-xs shadow-xs transition-all group-hover:brightness-110"
              :title="`Stunting: ${d.stunting}`"
            />
            <div
              v-if="d.berisiko"
              :style="{ height: pct(d.berisiko, d.total) + '%' }"
              class="w-full bg-amber-400 rounded-xs shadow-xs transition-all group-hover:brightness-110"
              :title="`Berisiko: ${d.berisiko}`"
            />
            <div
              v-if="d.normal"
              :style="{ height: pct(d.normal, d.total) + '%' }"
              class="w-full bg-emerald-400 rounded-xs shadow-xs transition-all group-hover:brightness-110"
              :title="`Normal: ${d.normal}`"
            />
          </div>
          <!-- Label -->
          <div class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 truncate max-w-[50px]">
            {{ d.bulan.split(' ')[0] }}
          </div>
        </div>
      </div>

      <!-- Legend -->
      <div class="flex items-center gap-4 mt-3 pt-3 border-t border-zinc-200 dark:border-zinc-800 text-xs">
        <div class="flex items-center gap-1.5 text-zinc-600 dark:text-zinc-400">
          <div class="w-2.5 h-2.5 rounded-xs bg-emerald-400" />
          <span>Normal</span>
        </div>
        <div class="flex items-center gap-1.5 text-zinc-600 dark:text-zinc-400">
          <div class="w-2.5 h-2.5 rounded-xs bg-amber-400" />
          <span>Berisiko</span>
        </div>
        <div class="flex items-center gap-1.5 text-zinc-600 dark:text-zinc-400">
          <div class="w-2.5 h-2.5 rounded-xs bg-red-500" />
          <span>Stunting</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ data: Array })
const pct = (val, total) => total > 0 ? Math.round(val / total * 100) : 0
</script>
