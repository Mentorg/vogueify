<script setup>
import { useI18n } from 'vue-i18n';

defineProps({
  percentage: Number
})

const { t } = useI18n();

</script>

<template>
  <div class="mt-6">
    <div class="flex justify-between text-sm mb-1">
      <span>{{ t('common.form.base.uploading') }}</span>
      <span>{{ percentage }}%</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
      <div :style="{ width: `${Math.min(100, Math.max(0, percentage))}%` }"
        class="bg-black h-3 transition-all duration-300" />
    </div>
  </div>
</template>
