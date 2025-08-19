<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
  <footer class="border-t border-t-[#D9D9D9]">
    <div
      class="grid grid-cols-2 gap-y-6 justify-items-center py-10 md:gap-x-2 md:grid-cols-4 lg:container lg:grid-cols-4">
      <div class="flex flex-col w-full px-4">
        <h2>{{ t('common.footer.company.label') }}</h2>
        <ul class="mt-4 flex flex-col gap-2">
          <li><a href="#!" class="font-medium">{{ t('common.footer.company.aboutUs') }}</a></li>
          <li><a href="#!" class="font-medium">{{ t('common.footer.company.faq', 2) }}</a></li>
          <li><a href="#!" class="font-medium">{{ t('common.footer.company.missionStatement') }}</a>
          </li>
          <li><a href="#!" class="font-medium">{{ t('common.footer.company.career', 2) }}</a></li>
          <li><a href="#!" class="font-medium">{{ t('common.footer.company.press') }}</a></li>
        </ul>
      </div>
      <div class="flex flex-col w-full px-4">
        <h2>{{ t('common.footer.contact.label') }}</h2>
        <ul class="mt-4 flex flex-col gap-2">
          <li><span class="font-medium">{{ t('common.footer.contact.address') }}: </span>742 Maplewood Drive,
            Springfield, IL 62704, USA
          </li>
          <li><span class="font-medium">{{ t('common.footer.contact.email') }}: </span> contact@vogueify.com</li>
          <li><a href="#!" class="font-medium">{{ t('common.footer.contact.liveChat') }}</a></li>
          <li><a href="#!" class="font-medium">{{ t('common.footer.contact.supportHours') }}</a></li>
        </ul>
      </div>
      <div class="flex flex-col w-full px-4">
        <h2>{{ t('common.footer.socialNetworks.label') }}</h2>
        <ul class="mt-4 flex flex-col gap-2">
          <li>
            <a href="#!" class="font-medium">Facebook</a>
          </li>
          <li>
            <a href="#!" class="font-medium">Instagram</a>
          </li>
          <li>
            <a href="#!" class="font-medium">Pinterest</a>
          </li>
          <li>
            <a href="#!" class="font-medium">TikTok</a>
          </li>
        </ul>
      </div>
      <div class="flex flex-col w-full px-4">
        <h2>{{ t('common.footer.privacyLegal.label') }}</h2>
        <ul class="mt-4 flex flex-col gap-2">
          <li>
            <a href="#!" class="font-medium">{{ t('common.footer.privacyLegal.privacyPolicy') }}</a>
          </li>
          <li>
            <a href="#!" class="font-medium">{{ t('common.footer.privacyLegal.termsConditions') }}</a>
          </li>
          <li>
            <a href="#!" class="font-medium">{{ t('common.footer.privacyLegal.cookiePolicy') }}</a>
          </li>
        </ul>
      </div>
    </div>
    <div class="flex justify-center py-8">
      <p class="text-3xl font-medium">VOGUEIFY</p>
    </div>
  </footer>
</template>
