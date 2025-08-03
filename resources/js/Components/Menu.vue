<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
  PhX,
  PhGlobe,
} from "@phosphor-icons/vue";
import Header from './Header.vue';
import MenuLink from "./MenuLink.vue";
import MobileMenu from './MobileMenu.vue';

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
        class="main-menu absolute top-0 left-0 w-full px-4 h-dvh bg-white py-2 transition-all duration-300 ease-in-out transform z-20 border-r md:px-[20px] md:py-[24px] md:w-[16rem] lg:px-[60px] lg:w-[30rem]"
        :style="isMenuOpen ? 'transform: translateX(0);' : 'transform: translateX(-100%);'">
        <nav class="flex flex-col w-full">
          <div class="flex justify-between">
            <button @click="closeMenu" class="flex items-center gap-2">
              <PhX :size="24" />Close
            </button>
            <button>
              <PhGlobe :size="24" />
            </button>
          </div>
          <ul class="flex flex-col text-xl mt-4 py-8">
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'New'"
                :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'Women'"
                :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'Men'"
                :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'Jewelry'"
                :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'Watches'"
                :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'Perfumes'"
                :openSubmenu="openSubmenu" />
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeSubmenu === 'New'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-30 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'For Women'"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'NewWomenThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'For Men'"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'NewMenThirdLevel'" />
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeSubmenu === 'Women'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-30 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'Bags'"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'WomenBagsThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'Shoes'"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'WomenShoesThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'Accessories'"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'WomenAccessoriesThirdLevel'" />
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeSubmenu === 'Men'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-30 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'Bags'"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'MenBagsThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'Shoes'"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'MenShoesThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'Accessories'"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'MenAccessoriesThirdLevel'" />
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeSubmenu === 'Jewelry'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-30 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'Categories'"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'JewelryCategoriesThirdLevel'" />
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeSubmenu === 'Watches'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-30 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?category=watches"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">All Watches
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeSubmenu === 'Perfumes'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-30 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :content="'Categories'"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'PerfumesCategoriesThirdLevel'" />
            </li>
          </ul>
        </nav>
      </div>
      <!-- Third Level Menus -->
      <div v-if="activeThirdLevelSubmenu === 'NewWomenThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-40 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Bags
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=shoes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Shoes
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=accessories"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Accessories
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=jewelry"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Jewelry
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=watches"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Watches
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=perfumes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Perfumes
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'NewMenThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-40 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Bags
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=shoes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Shoes
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=accessories"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Accessories
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=jewelry"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Jewelry
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=watches"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Watches
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=perfumes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Perfumes
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'WomenBagsThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-40 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">All Bags
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags&type=handbag"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Handbags
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags&type=bucket"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Bucket
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags&type=hobo"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Hobo
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags&type=envelope"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Envelope
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'WomenShoesThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-40 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=women&category=shoes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">All Shoes
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=shoes&type=sneakers"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Sneakers
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=shoes&type=ankle-boots"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Ankle Boots
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=shoes&type=sandals"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Sandals
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'WomenAccessoriesThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-40 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=women&category=accessories&type=scarf"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Scarves
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=accessories&type=sunglasses"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Sunglasses
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=accessories&type=belt"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Belts
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'MenBagsThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-40 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">All Bags
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags&type=backpack"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Backpacks
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags&type=satchel"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Satchels
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags&type=clutch"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Clutches
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags&type=fanny"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Fanny Packs
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'MenShoesThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-40 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=men&category=shoes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">All Shoes
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=shoes&type=sneakers"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Sneakers
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=shoes&type=dress-boots"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Boots
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'MenAccessoriesThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-40 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?gender=men&category=accessories&type=belt"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Belts
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=accessories&type=scarf"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Scarves
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=accessories&type=sunglasses"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Sunglasses
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'JewelryCategoriesThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-40 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?category=jewelry"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">All Fine Jewelry
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?category=jewelry&type=bracelet"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Bracelets
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?category=jewelry&type=necklace"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Necklaces
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?category=jewelry&type=earrings"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Earrings
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?category=jewelry&type=ring"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Rings
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeThirdLevelSubmenu === 'PerfumesCategoriesThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-40 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?category=perfumes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">All Perfumes
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=perfumes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Women's Perfumes
                <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                  class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
              </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=perfumes"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
              <span class="relative">Men's Perfumes
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
