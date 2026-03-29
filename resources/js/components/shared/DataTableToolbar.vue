<script setup lang="ts">
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

defineProps<{
  searchable?: boolean
  searchPlaceholder?: string
}>()

const search = defineModel<string>('search', { default: '' })
const perPage = defineModel<number>('perPage', { default: 10 })
</script>

<template>
  <div class="flex items-center justify-between gap-3 flex-wrap">
    <Input
      v-if="searchable"
      v-model="search"
      :placeholder="searchPlaceholder ?? 'Search...'"
      class="max-w-xs"
    />
    <div class="flex items-center gap-2 ml-auto">
      <span class="text-sm text-muted-foreground">Rows per page</span>
      <Select v-model="perPage">
        <SelectTrigger class="w-[70px]">
          <SelectValue />
        </SelectTrigger>
        <SelectContent>
          <SelectItem :value="10">10</SelectItem>
          <SelectItem :value="25">25</SelectItem>
          <SelectItem :value="50">50</SelectItem>
        </SelectContent>
      </Select>
    </div>
  </div>
</template>