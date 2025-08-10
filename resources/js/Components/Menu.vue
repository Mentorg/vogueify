<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { PhX } from "@phosphor-icons/vue";
import Header from './Header.vue';
import MenuLink from "./MenuLink.vue";
import MobileMenu from './MobileMenu.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const isMenuOpen = ref(false);
const activeSubmenu = ref(null);
const activeThirdLevelSubmenu = ref(null);
const hoveredItem = ref(false);
const activeItem = ref(null);

const openSubmenu = (item) => {
  if (activeSubmenu.value === item) {
    activeSubmenu.value = null;
    activeThirdLevelSubmenu.value = null;
  } else {
    activeSubmenu.value = item;
    activeThirdLevelSubmenu.value = null;
  }
};

const openThirdLevelSubmenu = (item) => {
  if (activeThirdLevelSubmenu.value === item) {
    activeThirdLevelSubmenu.value = null;
  } else {
    activeThirdLevelSubmenu.value = item;
  }
};

const closeMenu = () => {
  isMenuOpen.value = false;
  activeSubmenu.value = null;
  activeThirdLevelSubmenu.value = null;
};

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
}

const screenWidth = ref(window.innerWidth);

const isMobile = computed(() => screenWidth.value < 768);

const handleResize = () => {
  screenWidth.value = window.innerWidth;
}

onMounted(() => {
  window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize);
});

</script>

<template>
  <div v-if="isMobile">
    <Header :isMenuOpen="isMenuOpen" @toggleMenu="toggleMenu" />
    <MobileMenu :isMenuOpen="isMenuOpen" :toggleMenu="toggleMenu" :closeMenu="closeMenu" />
  </div>
  <div v-else>
    <div class="relative">
      <div :class="{ 'visible': isMenuOpen, 'invisible': !isMenuOpen }"
        class="w-dvw h-dvh bg-slate-950/85 fixed top-0 left-0 z-10 transition-all duration-200 ease-in-out" />
      <Header :isMenuOpen="isMenuOpen" @toggleMenu="toggleMenu" />
      <div :class="{ 'opacity-100': isMenuOpen, 'opacity-0': !isMenuOpen }"
        class="main-menu absolute top-0 left-0 w-full px-4 h-dvh bg-white py-2 transition-all duration-300 ease-in-out transform z-50 border-r md:px-[20px] md:py-[24px] md:w-[16rem] lg:px-[60px] lg:w-[30rem]"
        :style="isMenuOpen ? 'transform: translateX(0);' : 'transform: translateX(-100%);'">
        <nav class="flex flex-col w-full">
          <div class="flex justify-between">
            <button @click="closeMenu" class="flex items-center gap-2">
              <PhX :size="24" />{{ t('common.header.button.close') }}
            </button>
            <div class="locale-changer">
              <select name="locale-changer" id="locale-changer" v-model="$i18n.locale"
                class="py-0 cursor-pointer border-none text-sm">
                <option v-for="locale in $i18n.availableLocales" :key="`locale-${locale}`" :value="locale">{{
                  locale.charAt(0).toUpperCase() + locale.slice(1) }}
                </option>
              </select>
            </div>
          </div>
          <ul class="flex flex-col text-xl mt-4 py-8">
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="t('common.category.new')"
                :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="t('common.gender.woman', 2)"
                :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="t('common.gender.man', 2)"
                :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="t('common.category.jewelry', 2)"
                :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="t('common.category.watches', 2)"
                :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="t('common.category.perfumes', 2)"
                :openSubmenu="openSubmenu" />
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeSubmenu === t('common.category.new')"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :content="`${t('common.menuLabel.for')} ${t('common.gender.woman', 2)}`"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'NewWomenThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :content="`${t('common.menuLabel.for')} ${t('common.gender.man', 2)}`"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'NewMenThirdLevel'" />
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeSubmenu === t('common.gender.woman', 2)"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="t('common.category.bags', 2)"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'WomenBagsThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="t('common.category.shoes', 2)"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'WomenShoesThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :content="t('common.category.accessories', 2)" :openThirdLevelSubmenu="openThirdLevelSubmenu"
                :thirdLevelContent="'WomenAccessoriesThirdLevel'" />
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeSubmenu === t('common.gender.man', 2)"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="t('common.category.bags', 2)"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'MenBagsThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="t('common.category.shoes', 2)"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'MenShoesThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :content="t('common.category.accessories', 2)" :openThirdLevelSubmenu="openThirdLevelSubmenu"
                :thirdLevelContent="'MenAccessoriesThirdLevel'" />
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeSubmenu === t('common.category.jewelry', 2)"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="t('common.menuLabel.category', 2)"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'JewelryCategoriesThirdLevel'" />
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeSubmenu === t('common.category.watches', 2)"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?category=watches"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.menuLabel.all') }} {{ t('common.category.watches', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeSubmenu === t('common.category.perfumes', 2)"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="t('common.menuLabel.category', 2)"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'PerfumesCategoriesThirdLevel'" />
            </li>
          </ul>
        </nav>
      </div>
      <!-- Third Level Menus -->
      <div v-if="activeThirdLevelSubmenu === 'NewWomenThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.category.bags', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=shoes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.category.shoes', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=accessories"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.category.accessories', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=jewelry"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.category.jewelry', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=watches"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.category.watches', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=perfumes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.category.perfumes', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'NewMenThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.category.bags', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=shoes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.category.shoes', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=accessories"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.category.accessories', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=jewelry"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.category.jewelry', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=watches"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.category.watches', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=perfumes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.category.perfumes', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'WomenBagsThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.menuLabel.all') }} {{ t('common.category.bags', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags&type=handbag"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.bag.handbag', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags&type=bucket"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.bag.bucket', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags&type=hobo"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.bag.hobo', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags&type=envelope"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.bag.envelope', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'WomenShoesThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=women&category=shoes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.menuLabel.all') }} {{ t('common.category.shoes', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=shoes&type=sneakers"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.shoe.sneaker', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=shoes&type=ankle-boots"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.shoe.ankle', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=shoes&type=sandals"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.shoe.sandal', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'WomenAccessoriesThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=women&category=accessories&type=scarf"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.accessory.scarf', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=accessories&type=sunglasses"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.accessory.sunglasses', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=accessories&type=belt"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.accessory.belt', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'MenBagsThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.menuLabel.all') }} {{ t('common.category.bags', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags&type=backpack"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.bag.backpack', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags&type=satchel"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.bag.satchel', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags&type=clutch"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.bag.clutch', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags&type=fanny"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.bag.fanny', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'MenShoesThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=men&category=shoes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.menuLabel.all') }} {{ t('common.category.shoes', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=shoes&type=sneakers"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.shoe.sneaker', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=shoes&type=dress-boots"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.shoe.boot', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'MenAccessoriesThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=men&category=accessories&type=belt"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.accessory.belt', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=accessories&type=scarf"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.accessory.scarf', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=accessories&type=sunglasses"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.accessory.sunglasses', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'JewelryCategoriesThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?category=jewelry"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.menuLabel.all', 2) }} {{ t('common.menuLabel.fine', 2) }} {{
                t('common.category.jewelry', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?category=jewelry&type=bracelet"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.jewelry.bracelet', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?category=jewelry&type=necklace"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.jewelry.necklace', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?category=jewelry&type=earrings"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.jewelry.earring', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?category=jewelry&type=ring"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.subCategory.jewelry.ring', 2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'PerfumesCategoriesThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?category=perfumes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.menuLabel.all', 2) }} {{ t('common.category.perfumes', 2)
                }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=perfumes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.gender.woman', 2) }} {{ t('common.category.perfumes',
                2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=perfumes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">{{ t('common.gender.man', 2) }} {{ t('common.category.perfumes',
                2) }}
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>
