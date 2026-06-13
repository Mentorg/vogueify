<script setup>
import { Link } from '@inertiajs/vue3';
import { PhX } from "@phosphor-icons/vue";
import { useI18n } from 'vue-i18n';
import Header from '@/Layouts/Header.vue';
import MobileMenu from "@/Layouts/MobileMenu.vue";
import MenuLink from "@/Components/MenuLink.vue";
import { capitalize } from '@/utils/capitalize';
import { useCustomerSidebarCatalog } from '@/composables/useCustomerSidebarCatalog';

const { t } = useI18n();
const {
  isMenuOpen,
  activeSubmenu,
  activeThirdLevelSubmenu,
  hoveredItem,
  activeItem,
  menuContainer,
  openSubmenu,
  openThirdLevelSubmenu,
  closeMenu,
  toggleMenu,
  isMobile,
} = useCustomerSidebarCatalog();

</script>

<template>
  <div v-if="isMobile">
    <Header :isMenuOpen="isMenuOpen" @toggleMenu="toggleMenu" />
    <MobileMenu :isMenuOpen="isMenuOpen" :toggleMenu="toggleMenu" :closeMenu="closeMenu" />
  </div>
  <div v-else>
    <div :class="{ 'visible': isMenuOpen, 'invisible': !isMenuOpen }"
      class="w-dvw h-dvh bg-slate-950/85 fixed top-0 left-0 z-10 transition-all duration-200 ease-in-out" />
    <div ref="menuContainer" class="relative">
      <Header :isMenuOpen="isMenuOpen" @toggleMenu="toggleMenu" />
      <div :class="{ 'opacity-100': isMenuOpen, 'opacity-0': !isMenuOpen }"
        class="main-menu absolute top-0 left-0 w-full px-4 h-dvh bg-white py-2 transition-all duration-300 ease-in-out transform z-50 border-r md:px-[20px] md:py-[24px] md:w-[16rem] lg:px-[60px] lg:w-[30rem]"
        :style="isMenuOpen ? 'transform: translateX(0);' : 'transform: translateX(-100%);'">
        <nav class="flex flex-col w-full">
          <div class="flex justify-between">
            <button @click="closeMenu" :title="t('common.button.closeCategoryMenuTitle')"
              class="flex items-center gap-2">
              <PhX :size="24" />{{ t('common.header.button.close') }}
            </button>
            <div class="locale-changer">
              <select name="locale-changer" id="locale-changer" v-model="$i18n.locale"
                class="py-0 cursor-pointer border-none text-sm">
                <option v-for="locale in $i18n.availableLocales" :key="`locale-${locale}`" :value="locale">{{
                  capitalize(locale) }}
                </option>
              </select>
            </div>
          </div>
          <ul class="flex flex-col text-xl mt-4 py-8">
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :title="t('common.catalogMenu.new.title')"
                :content="t('common.category.new')" :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :title="t('common.catalogMenu.women.title')"
                :content="t('common.gender.woman', 2)" :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem" :title="t('common.catalogMenu.men.title')"
                :content="t('common.gender.man', 2)" :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :title="t('common.catalogMenu.jewelry.title')" :content="t('common.category.jewelry', 2)"
                :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :title="t('common.catalogMenu.watches.title')" :content="t('common.category.watches', 2)"
                :openSubmenu="openSubmenu" />
            </li>
            <li>
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :title="t('common.catalogMenu.fragrances.title')" :content="t('common.category.fragrances', 2)"
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
                :title="t('common.catalogMenu.new.women.title')"
                :content="`${t('common.menuLabel.for')} ${t('common.gender.woman', 2)}`"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'NewWomenThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :title="t('common.catalogMenu.new.men.title')"
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
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :title="t('common.catalogMenu.women.bags.title')" :content="t('common.category.bags', 2)"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'WomenBagsThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :title="t('common.catalogMenu.women.shoes.title')" :content="t('common.category.shoes', 2)"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'WomenShoesThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :title="t('common.catalogMenu.women.accessories.title')" :content="t('common.category.accessories', 2)"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'WomenAccessoriesThirdLevel'" />
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeSubmenu === t('common.gender.man', 2)"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :title="t('common.catalogMenu.men.bags.title')" :content="t('common.category.bags', 2)"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'MenBagsThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :title="t('common.catalogMenu.men.shoes.title')" :content="t('common.category.shoes', 2)"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'MenShoesThirdLevel'" />
            </li>
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :title="t('common.catalogMenu.men.accessories.title')" :content="t('common.category.accessories', 2)"
                :openThirdLevelSubmenu="openThirdLevelSubmenu" :thirdLevelContent="'MenAccessoriesThirdLevel'" />
            </li>
          </ul>
        </nav>
      </div>
      <div v-if="activeSubmenu === t('common.category.jewelry', 2)"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :title="t('common.catalogMenu.jewelry.categories.title')" :content="t('common.menuLabel.category', 2)"
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
              <Link href="/products?category=watches" :title="t('common.catalogMenu.watches.all')"
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
      <div v-if="activeSubmenu === t('common.category.fragrances', 2)"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r md:w-[16rem] md:left-[16rem] lg:w-[30rem] lg:left-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <MenuLink :hoveredItem="hoveredItem" :activeItem="activeItem"
                :title="t('common.catalogMenu.fragrances.categories.title')"
                :content="t('common.menuLabel.category', 2)" :openThirdLevelSubmenu="openThirdLevelSubmenu"
                :thirdLevelContent="'FragrancesCategoriesThirdLevel'" />
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
              <Link href="/products?gender=women&category=bags" :title="t('common.catalogMenu.new.women.bags')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.category.bags', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=shoes" :title="t('common.catalogMenu.new.women.shoes')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.category.shoes', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=accessories"
                :title="t('common.catalogMenu.new.women.accessories')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.category.accessories', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=jewelry" :title="t('common.catalogMenu.new.women.jewelry')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.category.jewelry', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=watches" :title="t('common.catalogMenu.new.women.watches')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.category.watches', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=fragrances"
                :title="t('common.catalogMenu.new.women.fragrances')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.category.fragrances', 2) }}
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
              <Link href="/products?gender=men&category=bags" :title="t('common.catalogMenu.new.men.bags')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.category.bags', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=shoes" :title="t('common.catalogMenu.new.men.shoes')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.category.shoes', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=accessories"
                :title="t('common.catalogMenu.new.men.accessories')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.category.accessories', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=jewelry" :title="t('common.catalogMenu.new.men.jewelry')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.category.jewelry', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=watches" :title="t('common.catalogMenu.new.men.watches')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.category.watches', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=fragrances" :title="t('common.catalogMenu.new.men.fragrances')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.category.fragrances', 2) }}
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
              <Link href="/products?gender=women&category=bags" :title="t('common.catalogMenu.women.bags.all')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.menuLabel.all') }} {{ t('common.category.bags', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags&type=handbag"
                :title="t('common.catalogMenu.women.bags.handbags')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.bag.handbag', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags&type=bucket"
                :title="t('common.catalogMenu.women.bags.bucket')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.bag.bucket', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags&type=hobo"
                :title="t('common.catalogMenu.women.bags.hobo')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.bag.hobo', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=bags&type=envelope"
                :title="t('common.catalogMenu.women.bags.envelope')"
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
              <Link href="/products?gender=women&category=shoes" :title="t('common.catalogMenu.women.shoes.all')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.menuLabel.all') }} {{ t('common.category.shoes', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=shoes&type=sneakers"
                :title="t('common.catalogMenu.women.shoes.sneakers')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.shoe.sneaker', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=shoes&type=ankle-boots"
                :title="t('common.catalogMenu.women.shoes.ankleBoots')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.shoe.ankle', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=shoes&type=sandals"
                :title="t('common.catalogMenu.women.shoes.sandals')"
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
                :title="t('common.catalogMenu.women.accessories.scarves')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.accessory.scarf', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=accessories&type=sunglasses"
                :title="t('common.catalogMenu.women.accessories.sunglasses')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.accessory.sunglasses', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=accessories&type=belt"
                :title="t('common.catalogMenu.women.accessories.belts')"
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
              <Link href="/products?gender=men&category=bags" :title="t('common.catalogMenu.men.bags.all')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.menuLabel.all') }} {{ t('common.category.bags', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags&type=backpack"
                :title="t('common.catalogMenu.men.bags.backpack')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.bag.backpack', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags&type=satchel"
                :title="t('common.catalogMenu.men.bags.satchel')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.bag.satchel', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags&type=clutch"
                :title="t('common.catalogMenu.men.bags.clutch')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.bag.clutch', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=bags&type=fanny"
                :title="t('common.catalogMenu.men.bags.fannyPack')"
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
              <Link href="/products?gender=men&category=shoes" :title="t('common.catalogMenu.men.shoes.all')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.menuLabel.all') }} {{ t('common.category.shoes', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=shoes&type=sneakers"
                :title="t('common.catalogMenu.men.shoes.sneakers')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.shoe.sneaker', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=shoes&type=dress-boots"
                :title="t('common.catalogMenu.men.shoes.boots')"
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
              <Link href="/products?gender=men&category=accessories&type=scarf"
                :title="t('common.catalogMenu.men.accessories.scarves')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.accessory.scarf', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=accessories&type=sunglasses"
                :title="t('common.catalogMenu.men.accessories.sunglasses')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.accessory.sunglasses', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=accessories&type=belt"
                :title="t('common.catalogMenu.men.accessories.belts')"
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
      <div v-if="activeThirdLevelSubmenu === 'JewelryCategoriesThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?category=jewelry" :title="t('common.catalogMenu.jewelry.categories.all')"
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
                :title="t('common.catalogMenu.jewelry.categories.bracelets')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.jewelry.bracelet', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?category=jewelry&type=necklace"
                :title="t('common.catalogMenu.jewelry.categories.necklaces')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.jewelry.necklace', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?category=jewelry&type=earrings"
                :title="t('common.catalogMenu.jewelry.categories.earrings')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.subCategory.jewelry.earring', 2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?category=jewelry&type=ring"
                :title="t('common.catalogMenu.jewelry.categories.rings')"
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
      <div v-if="activeThirdLevelSubmenu === 'FragrancesCategoriesThirdLevel'"
        class="submenu absolute top-0 bg-white py-[4.5rem] px-6 z-50 h-dvh border-r overflow-y-auto pb-[10rem] md:left-[32rem] md:w-[16rem] lg:left-[60rem] lg:w-[30rem]">
        <nav class="flex flex-col w-full">
          <ul class="flex flex-col text-xl py-8">
            <li class="text-lg">
              <Link href="/products?category=fragrances" :title="t('common.catalogMenu.fragrances.categories.all')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.menuLabel.all', 2) }} {{ t('common.category.fragrances', 2)
                  }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=women&category=fragrances"
                :title="t('common.catalogMenu.fragrances.categories.women')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.gender.woman', 2) }} {{ t('common.category.fragrances',
                  2) }}
                  <span :class="{ 'scale-x-100': activeItem === content || hoveredItem === content }"
                    class="absolute bottom-0 left-0 w-full h-[1px] bg-black transform scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 ease-in-out"></span>
                </span>
              </Link>
            </li>
            <li class="text-lg">
              <Link href="/products?gender=men&category=fragrances"
                :title="t('common.catalogMenu.fragrances.categories.men')"
                class="flex justify-between w-full group py-3 relative overflow-hidden">
                <span class="relative">{{ t('common.gender.man', 2) }} {{ t('common.category.fragrances',
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
