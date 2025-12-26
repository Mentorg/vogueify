<script setup>
import { defineProps } from 'vue';
import { router } from '@inertiajs/vue3';
import { PhCaretLeft, PhCaretRight } from '@phosphor-icons/vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
  pagination: Object,
});

const { t } = useI18n();

</script>

<template>
  <tfoot v-if="pagination.data.length > 0"
    class="bg-white sticky bottom-0 flex py-2 px-4 border-t-2 items-center justify-between text-sm"
    aria-label="Page navigation example">
    <p>
      {{ t('common.table.pagination', { from: pagination.from, to: pagination.to, total: pagination.total }) }}
    </p>
    <ul v-if="pagination.data.length >= pagination.per_page" class="list-style-none flex gap-x-4 mx-2">
      <li v-if="pagination.first_page_url">
        <button class="bg-slate-500 text-white flex items-center gap-2 rounded px-3 py-1.5 text-sm"
          @click="router.visit(pagination.first_page_url)">
          {{ t('common.button.first') }}
        </button>
      </li>
      <li v-if="pagination.prev_page_url">
        <button class="bg-slate-500 text-white flex items-center gap-2 rounded px-3 py-1.5 text-sm"
          @click="router.visit(pagination.prev_page_url)">
          <PhCaretLeft :size="12" />
          {{ t('common.button.previous') }}
        </button>
      </li>
      <li v-if="pagination.next_page_url">
        <button class="bg-slate-500 text-white flex items-center gap-2 rounded px-3 py-1.5 text-sm"
          @click="router.visit(pagination.next_page_url)">
          {{ t('common.button.next') }}
          <PhCaretRight :size="12" />
        </button>
      </li>
      <li v-if="pagination.last_page_url">
        <button class="bg-slate-500 text-white flex items-center gap-2 rounded px-3 py-1.5 text-sm"
          @click="router.visit(pagination.last_page_url)">
          {{ t('common.button.last') }}
        </button>
      </li>
    </ul>
  </tfoot>
</template>
