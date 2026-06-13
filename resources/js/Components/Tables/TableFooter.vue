<script setup>
import { computed } from 'vue';
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
  <tfoot v-if="hasData" class="sticky bottom-0 z-20">
    <tr>
      <td colspan="100%" class="p-0">
        <div class="flex py-2 px-4 items-center justify-between text-sm bg-white border-t-2 border-slate-200 shadow-sm">
          <p aria-live="polite">
            {{ t('common.table.pagination', {
              from: pagination.from,
              to: pagination.to,
              total: pagination.total
            }) }}
          </p>
          <nav v-if="showPagination">
            <ul class="flex gap-x-4 mx-2">
              <li>
                <button type="button" :disabled="!firstLink?.url || firstLink.active"
                  :aria-disabled="!firstLink?.url || firstLink.active"
                  @click="firstLink?.url && router.visit(firstLink.url)" :title="t('common.table.firstTitle')"
                  class="px-3 py-1.5 rounded text-sm flex items-center gap-2 bg-slate-500 text-white disabled:opacity-50 focus:outline focus:outline-2 focus:outline-offset-2">
                  {{ t('common.button.first') }}
                </button>
              </li>
              <li>
                <button type="button" :disabled="!prevLink?.url" :aria-disabled="!prevLink?.url"
                  @click="prevLink?.url && router.visit(prevLink.url)" :title="t('common.table.previousTitle')"
                  class="px-3 py-1.5 rounded text-sm flex items-center gap-2 bg-slate-500 text-white disabled:opacity-50 focus:outline focus:outline-2 focus:outline-offset-2">
                  <PhCaretLeft :size="12" aria-hidden="true" />
                  {{ t('common.button.previous') }}
                </button>
              </li>
              <li>
                <button type="button" :disabled="!nextLink?.url" :aria-disabled="!nextLink?.url"
                  @click="nextLink?.url && router.visit(nextLink.url)" :title="t('common.table.nextTitle')"
                  class="px-3 py-1.5 rounded text-sm flex items-center gap-2 bg-slate-500 text-white disabled:opacity-50 focus:outline focus:outline-2 focus:outline-offset-2">
                  {{ t('common.button.next') }}
                  <PhCaretRight :size="12" aria-hidden="true" />
                </button>
              </li>
              <li>
                <button type="button" :disabled="!lastLink?.url || lastLink.active"
                  :aria-disabled="!lastLink?.url || lastLink.active"
                  @click="lastLink?.url && router.visit(lastLink.url)" :title="t('common.table.lastTitle')"
                  class="px-3 py-1.5 rounded text-sm flex items-center gap-2 bg-slate-500 text-white disabled:opacity-50 focus:outline focus:outline-2 focus:outline-offset-2">
                  {{ t('common.button.last') }}
                </button>
              </li>
            </ul>
          </nav>
        </div>
      </td>
    </tr>
  </tfoot>
</template>
