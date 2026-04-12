<template>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th
                            v-for="header in headers"
                            :key="header.key"
                            :class="[
                                'px-6 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider',
                                header.align === 'right' ? 'text-right' : 'text-left',
                                header.className || '',
                            ]"
                        >
                            {{ header.label }}
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-slate-200">
                    <tr v-if="loading">
                        <td :colspan="headers.length" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-4 border-cyan-600 mb-4"></div>
                                <p class="text-lg font-medium text-slate-700">{{ loadingTitle }}</p>
                                <p class="text-sm text-slate-500 mt-1">{{ loadingSubtitle }}</p>
                            </div>
                        </td>
                    </tr>

                    <tr v-else-if="rows.length === 0">
                        <td :colspan="headers.length" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <slot name="empty-icon">
                                    <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </slot>
                                <p class="text-lg font-medium text-slate-700">{{ emptyTitle }}</p>
                                <p class="text-sm text-slate-500 mt-1">{{ emptySubtitle }}</p>
                            </div>
                        </td>
                    </tr>

                    <!-- Custom rows slot (full override) -->
                    <slot v-else-if="$slots['rows']" name="rows" :rows="rows" />

                    <!-- Default auto-render with optional actions slot -->
                    <template v-else>
                        <tr
                            v-for="row in rows"
                            :key="row.id"
                            class="hover:bg-slate-50 transition"
                        >
                            <td
                                v-for="col in columns"
                                :key="col.key"
                                :class="[
                                    'px-6 py-4 whitespace-nowrap',
                                    col.align === 'right' ? 'text-right' : 'text-left',
                                    col.tdClassName || '',
                                ]"
                            >
                                <slot :name="`cell-${col.key}`" :value="row[col.key]" :row="row">
                                    <div :class="['text-sm', col.className || 'text-slate-600']">
                                        {{ row[col.key] ?? col.fallback ?? '' }}
                                    </div>
                                </slot>
                            </td>

                            <td v-if="$slots['actions']" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <slot name="actions" :item="row" />
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div
            v-if="showPagination"
            class="px-6 py-4 border-t border-slate-200 flex justify-between items-center"
        >
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-slate-500">{{ perPageLabel }}:</label>
                    <select
                        :value="perPage"
                        @change="onPerPageChange"
                        class="px-2 py-1 border border-slate-300 rounded text-sm focus:ring-2 focus:ring-blue-500"
                    >
                        <option v-for="option in perPageOptions" :key="option" :value="option">
                            {{ option }}
                        </option>
                    </select>
                </div>

                <div class="text-sm text-slate-500">
                    {{ pageLabel }} {{ pagination.currentPage }} {{ ofLabel }} {{ pagination.lastPage }}
                    ({{ pagination.total }} {{ totalLabel }})
                </div>
            </div>

            <div class="flex gap-2">
                <button
                    @click="emit('changePage', pagination.currentPage - 1)"
                    :disabled="pagination.currentPage === 1"
                    class="px-3 py-1 border border-slate-300 rounded disabled:opacity-50 disabled:cursor-not-allowed hover:bg-slate-50"
                >
                    {{ previousLabel }}
                </button>
                <button
                    @click="emit('changePage', pagination.currentPage + 1)"
                    :disabled="pagination.currentPage === pagination.lastPage"
                    class="px-3 py-1 border border-slate-300 rounded disabled:opacity-50 disabled:cursor-not-allowed hover:bg-slate-50"
                >
                    {{ nextLabel }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
    export interface TableHeader {
        key: string;
        label: string;
        align?: 'left' | 'right';
        className?: string;
    }

    export interface TableColumn {
        key: string;
        align?: 'left' | 'right';
        className?: string;    // text style (font-medium, text-slate-900 etc.)
        tdClassName?: string;  // td wrapper style
        fallback?: string;     // value না থাকলে এটা দেখাবে (e.g. 'N/A')
    }

    export interface TablePagination {
        currentPage: number;
        perPage: number;
        total: number;
        lastPage: number;
    }

    const props = withDefaults(defineProps<{
        headers: TableHeader[];
        rows: Record<string, any>[];
        columns?: TableColumn[];
        loading?: boolean;
        loadingTitle?: string;
        loadingSubtitle?: string;
        emptyTitle?: string;
        emptySubtitle?: string;
        pagination?: TablePagination;
        perPage?: number;
        perPageOptions?: number[];
        showPagination?: boolean;
        perPageLabel?: string;
        pageLabel?: string;
        ofLabel?: string;
        totalLabel?: string;
        previousLabel?: string;
        nextLabel?: string;
    }>(), {
        columns: () => [],
        loading: false,
        loadingTitle: 'Loading',
        loadingSubtitle: 'Please wait',
        emptyTitle: 'No data found',
        emptySubtitle: '',
        pagination: () => ({
            currentPage: 1,
            perPage: 15,
            total: 0,
            lastPage: 1,
        }),
        perPage: 15,
        perPageOptions: () => [5, 15, 25, 50],
        showPagination: false,
        perPageLabel: 'Per page',
        pageLabel: 'Page',
        ofLabel: 'of',
        totalLabel: 'total',
        previousLabel: 'Previous',
        nextLabel: 'Next',
    });

    const emit = defineEmits<{
        changePage: [page: number];
        changePerPage: [value: number];
    }>();

    function onPerPageChange(event: Event): void {
        const target = event.target as HTMLSelectElement;
        emit('changePerPage', Number(target.value));
    }
</script>