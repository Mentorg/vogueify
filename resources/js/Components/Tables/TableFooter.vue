<script setup>
import { computed, defineProps } from 'vue';
import { router } from '@inertiajs/vue3';
import { PhCaretLeft, PhCaretRight } from '@phosphor-icons/vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
  pagination: Object,
});

const { t } = useI18n();

const hasData = computed(() => props.pagination.data.length > 0)

const showPagination = computed(() =>
  props.pagination.total > props.pagination.per_page
)

const firstLink = computed(() =>
  props.pagination.links.find(l => l.label === '1')
)

const prevLink = computed(() =>
  props.pagination.links.find(l => l.label.includes('Previous'))
)

const nextLink = computed(() =>
  props.pagination.links.find(l => l.label.includes('Next'))
)

const lastLink = computed(() =>
  [...props.pagination.links].reverse().find(l => /^\d+$/.test(l.label))
)

</script>

<template>
  <tfoot v-if="hasData" class="bg-white sticky bottom-0 flex py-2 px-4 border-t-2 items-center justify-between text-sm"
    aria-label="Table pagination"><!-- TODO: add a translation key for this aria-label text -->
    <p>
      {{ t('common.table.pagination', { from: pagination.from, to: pagination.to, total: pagination.total }) }}
    </p>
    <ul v-if="showPagination" class="list-style-none flex gap-x-4 mx-2">
      <li>
        <button :disabled="!firstLink?.url || firstLink.active" @click="firstLink?.url && router.visit(firstLink.url)"
          class="px-3 py-1.5 rounded text-sm flex items-center gap-2 bg-slate-500 text-white disabled:opacity-50">
          {{ t('common.button.first') }}
        </button>
      </li>
      <li>
        <button :disabled="!prevLink?.url" @click="prevLink?.url && router.visit(prevLink.url)"
          class="px-3 py-1.5 rounded text-sm flex items-center gap-2 bg-slate-500 text-white disabled:opacity-50">
          <PhCaretLeft :size="12" />
          {{ t('common.button.previous') }}
        </button>
      </li>
      <li>
        <button :disabled="!nextLink?.url" @click="nextLink?.url && router.visit(nextLink.url)"
          class="px-3 py-1.5 rounded text-sm flex items-center gap-2 bg-slate-500 text-white disabled:opacity-50">
          {{ t('common.button.next') }}
          <PhCaretRight :size="12" />
        </button>
      </li>
      <li>
        <button :disabled="!lastLink?.url || lastLink.active" @click="lastLink?.url && router.visit(lastLink.url)"
          class="px-3 py-1.5 rounded text-sm flex items-center gap-2 bg-slate-500 text-white disabled:opacity-50">
          {{ t('common.button.last') }}
        </button>
      </li>
    </ul>
  </tfoot>
</template>
