<template>
  <div>
    <div v-if="!data || data.length === 0" class="text-xs text-slate-500 text-center py-8">
      Belum ada data distribusi riwayat
    </div>
    <div v-else>
      <div class="flex items-end gap-2.5 sm:gap-4 h-32 mb-3 px-1">
        <div v-for="d in data" :key="d.bulan" class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group">
          <!-- Stack bar container -->
          <div class="w-full max-w-[48px] flex flex-col-reverse gap-1 h-[105px] bg-orbit-surface2/60 rounded-xl p-1 border border-orbit-border/50 group-hover:border-orbit-border transition-colors">
            <div
              v-if="d.stunting"
              :style="{ height: pct(d.stunting, d.total) + '%' }"
              class="w-full bg-rose-500 rounded-sm shadow-sm transition-all group-hover:brightness-110"
              :title="`Stunting: ${d.stunting}`"
            />
            <div
              v-if="d.berisiko"
              :style="{ height: pct(d.berisiko, d.total) + '%' }"
              class="w-full bg-amber-400 rounded-sm shadow-sm transition-all group-hover:brightness-110"
              :title="`Berisiko: ${d.berisiko}`"
            />
            <div
              v-if="d.normal"
              :style="{ height: pct(d.normal, d.total) + '%' }"
              class="w-full bg-emerald-400 rounded-sm shadow-sm transition-all group-hover:brightness-110"
              :title="`Normal: ${d.normal}`"
            />
          </div>
          <!-- Label -->
          <div class="text-[11px] font-medium text-slate-400 truncate max-w-[50px]">
            {{ d.bulan.split(' ')[0] }}
          </div>
        </div>
      </div>

      <!-- Legend -->
      <div class="flex items-center gap-4 mt-3 pt-3 border-t border-orbit-border/50 text-xs">
        <div class="flex items-center gap-1.5 text-slate-400">
          <div class="w-2.5 h-2.5 rounded-sm bg-emerald-400" />
          <span>Normal</span>
        </div>
        <div class="flex items-center gap-1.5 text-slate-400">
          <div class="w-2.5 h-2.5 rounded-sm bg-amber-400" />
          <span>Berisiko</span>
        </div>
        <div class="flex items-center gap-1.5 text-slate-400">
          <div class="w-2.5 h-2.5 rounded-sm bg-rose-500" />
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
