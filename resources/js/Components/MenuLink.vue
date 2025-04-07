<script setup>
import { defineProps } from 'vue';
import { PhCaretRight } from '@phosphor-icons/vue';

const props = defineProps({
  hoveredItem: Boolean,
  activeItem: String,
  content: String,
  openSubmenu: Function,
  openThirdLevelSubmenu: Function,
  thirdLevelContent: String,
});

const handleClick = () => {
  if (props.thirdLevelContent) {
    props.openThirdLevelSubmenu(props.thirdLevelContent);
  } else if (props.openSubmenu) {
    props.openSubmenu(props.content);
  }
};

</script>

<template>
  <button @mouseover="hoveredItem = content" @mouseleave="hoveredItem = null" :class="{
    'text-black': activeItem === content || hoveredItem === content,
    'text-gray-500': hoveredItem && hoveredItem !== content,
    'group': true
  }" class="flex justify-between w-full group py-3 relative overflow-hidden" @click="handleClick">
    <span class="relative z-10">{{ content }}
      <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
        class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
    </span>
    <span :class="{ 'scale-100': activeItem === content }"
      class="invisible group-hover:visible transform transition-all duration-300 ease-in-out opacity-0 group-hover:opacity-100 scale-0 group-hover:scale-100">
      <PhCaretRight :size="24" />
    </span>
  </button>
</template>
