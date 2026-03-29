<script setup lang="ts" generic="TData, TValue">
import { FlexRender, type ColumnDef } from '@tanstack/vue-table'
import { ChevronUp, ChevronDown, ChevronsUpDown } from 'lucide-vue-next'
import {
  Table, TableBody, TableCell,
  TableHead, TableHeader, TableRow,
} from '@/components/ui/table'
import DataTableToolbar from '@/components/shared/DataTableToolbar.vue'
import DataTablePagination from '@/components/shared/DataTablePagination.vue'
import { useDataTable, type PaginatedData } from '@/components/composables/useDataTable'

const props = withDefaults(
  defineProps<{
    columns: ColumnDef<TData, TValue>[]
    paginatedData: PaginatedData<TData>
    routeName: string
    filters?: Record<string, string>
    searchable?: boolean
    searchPlaceholder?: string
  }>(),
  {
    filters: () => ({}),
    searchable: true,
    searchPlaceholder: 'Search...',
  },
)

const { table, search, sortBy, sortDir, perPage } = useDataTable({
  columns: props.columns,
  paginatedData: () => props.paginatedData,
  routeName: props.routeName,
  filters: () => props.filters,
})

function sortIcon(colId: string) {
  if (sortBy.value !== colId) return ChevronsUpDown
  return sortDir.value === 'asc' ? ChevronUp : ChevronDown
}
</script>

<template>
  <div class="space-y-4">

    <DataTableToolbar
      v-model:search="search"
      v-model:perPage="perPage"
      :searchable="props.searchable"
      :searchPlaceholder="props.searchPlaceholder"
    />

    <div class="rounded-md border overflow-x-auto">
      <Table>
        <TableHeader>
          <TableRow
            v-for="headerGroup in table.getHeaderGroups()"
            :key="headerGroup.id"
          >
            <TableHead
              v-for="header in headerGroup.headers"
              :key="header.id"
              :class="header.column.getCanSort() ? 'cursor-pointer select-none' : ''"
              @click="header.column.getCanSort() ? header.column.toggleSorting() : undefined"
            >
              <div class="flex items-center gap-1">
                <FlexRender
                  :render="header.column.columnDef.header"
                  :props="header.getContext()"
                />
                <component
                  :is="sortIcon(header.column.id)"
                  v-if="header.column.getCanSort()"
                  class="size-4 opacity-60"
                />
              </div>
            </TableHead>
          </TableRow>
        </TableHeader>

        <TableBody>
          <template v-if="table.getRowModel().rows.length">
            <TableRow
              v-for="row in table.getRowModel().rows"
              :key="row.id"
            >
              <TableCell
                v-for="cell in row.getVisibleCells()"
                :key="cell.id"
              >
                <FlexRender
                  :render="cell.column.columnDef.cell"
                  :props="cell.getContext()"
                />
              </TableCell>
            </TableRow>
          </template>
          <TableRow v-else>
            <TableCell
              :colspan="props.columns.length"
              class="h-24 text-center text-muted-foreground"
            >
              No results found.
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <DataTablePagination
      :currentPage="props.paginatedData.current_page"
      :lastPage="props.paginatedData.last_page"
      :from="props.paginatedData.from"
      :to="props.paginatedData.to"
      :total="props.paginatedData.total"
      :canPrevious="table.getCanPreviousPage()"
      :canNext="table.getCanNextPage()"
      @previous="table.previousPage()"
      @next="table.nextPage()"
    />

  </div>
</template>