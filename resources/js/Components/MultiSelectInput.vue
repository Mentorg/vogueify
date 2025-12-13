<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { PhCaretDown, PhCaretUp } from '@phosphor-icons/vue';
import { capitalize } from '@/utils/capitalize';

const props = defineProps({
  entity: Object,
  entityType: String,
  selectedEntity: Array,
})

const { t } = useI18n();

const searchable = true;
const emit = defineEmits(['update:selectedEntity']);

const root = ref(null);
const entity = props.entity;
const isEntityDropdownOpen = ref(false);
const searchEntity = ref('');

const filteredEntities = computed(() => {
  if (!searchable || !searchEntity.value) {
    return entity;
  }
  const lowerCaseSearchTerm = searchEntity.value.toLowerCase();
  return entity.filter(item => {
    if (item.hasOwnProperty('name')) {
      return item.name.toLowerCase().includes(lowerCaseSearchTerm);
    } else {
      return item.product.name.toLowerCase().includes(lowerCaseSearchTerm)
    }
  })
})

const toggleEntityDropdown = () => {
  isEntityDropdownOpen.value = !isEntityDropdownOpen.value;
}

const isEntitySelected = (entity) => {
  return props.selectedEntity.some(item => item.id === entity.id);
}

const handleClickOutside = (event) => {
  if (!root.value) return;

  if (!root.value.contains(event.target)) {
    isEntityDropdownOpen.value = false;
  }
}

const toggleEntitySelection = (entity) => {
  const index = props.selectedEntity.findIndex(item => item.id === entity.id);
  if (index > -1) {
    props.selectedEntity.splice(index, 1);
  } else {
    props.selectedEntity.push(entity);
  }
  emit('update:selectedEntity', props.selectedEntity);
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

</script>
<template>
  <div ref="root" class="relative w-[300px] border border-gray-300 rounded-md">
    <div class="py-[8px] px-[12px] cursor-pointer flex justify-between items-center" @click="toggleEntityDropdown">
      <span class="truncate max-w-[220px]">
        {{selectedEntity.length === 0 ? `${capitalize(t('common.form.base.selectPlaceholder', {
          entityType: entityType
        }))}...`
          : selectedEntity.length <= 2 ? selectedEntity.map(item =>
            item.hasOwnProperty('name') ? capitalize(item.name) : capitalize(item.product.name) + `
          (${(item.sku)})`).join(', ') :
            `${t('common.form.base.selectedPlaceholder', { count: selectedEntity.length, entityType: entityType })}`}}
      </span>
      <span>
        <PhCaretUp v-if="isEntityDropdownOpen" size="14" />
        <PhCaretDown v-else size="14" />
      </span>
    </div>
    <div v-if="isEntityDropdownOpen"
      class="absolute w-full border border-gray-300 border-t-0 bg-white z-10 max-h-52 overflow-y-auto">
      <input v-if="searchable" type="text" v-model="searchEntity"
        :placeholder="`${t('common.form.base.searchPlaceholder')}...`"
        class="w-full py-[8px] px-[12px] border border-gray-300 mb-[5px]" />
      <ul class="list-none p-0 m-0">
        <li v-for="entity in filteredEntities" :key="entity.id" @click="toggleEntitySelection(entity)" :class="[
          'px-3 py-2 cursor-pointer flex items-center hover:bg-gray-100',
          isEntitySelected(entity) ? 'bg-gray-200' : ''
        ]">
          <input type="checkbox" :checked="isEntitySelected(entity)" @change.stop class="mr-2" />
          {{ entity.hasOwnProperty('name') ? capitalize(entity.name) : capitalize(entity.product.name) }}
        </li>
      </ul>
    </div>
  </div>
</template>
