<script setup lang="ts">
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

defineProps<{
  currentPage: number
  lastPage: number
  from: number | null
  to: number | null
  total: number
  canPrevious: boolean
  canNext: boolean
}>()

const emit = defineEmits<{
  previous: []
  next: []
}>()
</script>

<template>
  <div class="flex items-center justify-between text-sm text-muted-foreground">
    <span>
      Showing {{ from ?? 0 }}-{{ to ?? 0 }} of {{ total }} results
    </span>
    <div class="flex items-center gap-1">
      <Button
        variant="outline"
        size="icon"
        :disabled="!canPrevious"
        @click="emit('previous')"
      >
        <ChevronLeft class="size-4" />
      </Button>
      <span class="px-2">
        Page {{ currentPage }} of {{ lastPage }}
      </span>
      <Button
        variant="outline"
        size="icon"
        :disabled="!canNext"
        @click="emit('next')"
      >
        <ChevronRight class="size-4" />
      </Button>
    </div>
  </div>
</template>