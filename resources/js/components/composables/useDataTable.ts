import { route } from 'ziggy-js'

import {
  getCoreRowModel,
  useVueTable,
  type ColumnDef,
  type PaginationState,
  type SortingState,
} from '@tanstack/vue-table'

import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'


export interface PaginatedData<T> {
    data: T[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number
    to: number
}

export function useDataTable<TData, TValue>(options: {
    columns: ColumnDef<TData, TValue>[]
    paginatedData: () => PaginatedData<TData>
    routeName: string
    filters: () => Record<string, string>
}) {
    const search = ref(options.filters()?.search ?? '')
    const sortBy = ref(options.filters()?.sort_by ?? '')
    const sortDir = ref<'asc' | 'desc'>(
        (options.filters()?.sort_dir as 'asc' | 'desc') ?? 'desc',
    )
    const perPage = ref(options.paginatedData().per_page ?? 10)

    const pagination = ref<PaginationState>({
        pageIndex: options.paginatedData().current_page - 1,
        pageSize: perPage.value,
    })

    const sorting = ref<SortingState>(
        sortBy.value ? [{ id: sortBy.value, desc: sortDir.value === 'desc' }] : [],
    )

  function navigate(overrides: Record<string, unknown> = {}) {
    router.get(
      route(options.routeName),
      {
        ...options.filters(),
        search: search.value || undefined,
        sort_by: sortBy.value || undefined,
        sort_dir: sortBy.value ? sortDir.value : undefined,
        per_page: perPage.value,
        page: pagination.value.pageIndex + 1,
        ...overrides,
      },
      { preserveState: true, replace: true },
    )
  }

  const table = useVueTable({
    get data() { return options.paginatedData().data },
    get columns() { return options.columns },
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
    manualSorting: true,
    get pageCount() { return options.paginatedData().last_page },
    state: {
      get pagination() { return pagination.value },
      get sorting() { return sorting.value },
    },
    onPaginationChange: (updater) => {
      const next = typeof updater === 'function' ? updater(pagination.value) : updater
      pagination.value = next
      navigate({ page: next.pageIndex + 1, per_page: next.pageSize })
    },
    onSortingChange: (updater) => {
      const next = typeof updater === 'function' ? updater(sorting.value) : updater
      sorting.value = next
      if (next.length > 0) {
        sortBy.value = next[0].id
        sortDir.value = next[0].desc ? 'desc' : 'asc'
      } else {
        sortBy.value = ''
      }
      navigate({ page: 1 })
    },
  })

  let searchTimer: ReturnType<typeof setTimeout>
  watch(search, () => {
    clearTimeout(searchTimer)
    searchTimer = setTimeout(() => navigate({ page: 1 }), 350)
  })

  watch(perPage, () => navigate({ page: 1, per_page: perPage.value }))

  return { table, search, sortBy, sortDir, perPage, pagination }
}