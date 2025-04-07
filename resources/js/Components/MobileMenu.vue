<script setup>
import { defineProps, ref } from 'vue';
import {
  PhX,
  PhGlobe,
  PhCaretRight,
} from "@phosphor-icons/vue";
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  toggleMenu: Function,
  isMenuOpen: Boolean,
  closeMenu: Function
})

const subMenus = ref({
  level1: false,
  level2: false,
});

const toggleSubMenu = (level) => {
  subMenus.value[level] = !subMenus.value[level];
}

const isSubMenuOpen = (level) => {
  return subMenus.value[level];
}
</script>

<template>
  <div v-if="isMenuOpen" class="absolute z-20 left-0 top-0 w-full h-full bg-white shadow-lg mt-2 rounded-lg px-4 py-2">
    <div>
      <button @click="closeMenu">
        <PhX size="24" />
      </button>
    </div>
    <ul>
      <li class="relative">
        <button @click="toggleSubMenu('categoryNew')" class="flex justify-between w-full text-left px-4 py-2">
          New
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryNew')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('newWomen')" class="flex justify-between w-full text-left px-4 py-2">
              For Women
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('newWomen')" class="ml-2">
              <li>
                <Link href="/products?gender=women&category=bags" class="flex gap-y-4 w-full text-left px-4 py-2">
                Bags
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=shoes" class="flex gap-y-4 w-full text-left px-4 py-2">
                Shoes
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=accessories"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Accessories
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=jewelry" class="flex gap-y-4 w-full text-left px-4 py-2">
                Jewelry
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=watches" class="flex gap-y-4 w-full text-left px-4 py-2">
                Watches
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=perfumes" class="flex gap-y-4 w-full text-left px-4 py-2">
                Perfumes
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('newMen')" class="flex justify-between w-full text-left px-4 py-2">
              For Men
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('newMen')" class="ml-2">
              <li>
                <Link href="/products?gender=men&category=bags" class="flex gap-y-4 w-full text-left px-4 py-2">
                Bags
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=shoes" class="flex gap-y-4 w-full text-left px-4 py-2">
                Shoes
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=accessories" class="flex gap-y-4 w-full text-left px-4 py-2">
                Accessories
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=jewelry" class="flex gap-y-4 w-full text-left px-4 py-2">
                Jewelry
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=watches" class="flex gap-y-4 w-full text-left px-4 py-2">
                Watches
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=perfumes" class="flex gap-y-4 w-full text-left px-4 py-2">
                Perfumes
                </Link>
              </li>
            </ul>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryWomen')" class="flex justify-between w-full text-left px-4 py-2">
          Women
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryWomen')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('womenBags')" class="flex justify-between w-full text-left px-4 py-2">
              Bags
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('womenBags')" class="ml-2">
              <li>
                <Link href="/products?gender=women&category=bags" class="flex gap-y-4 w-full text-left px-4 py-2">
                All Bags
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=bags&type=handbag"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Handbags
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=bags&type=bucket"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Bucket
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=bags&type=hobo"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Hobo
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=bags&type=envelope"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Envelope
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('womenShoes')" class="flex justify-between w-full text-left px-4 py-2">
              Shoes
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('womenShoes')" class="ml-2">
              <li>
                <Link href="/products?gender=women&category=shoes" class="flex gap-y-4 w-full text-left px-4 py-2">
                All Shoes
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=shoes&type=sneakers"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Sneakers
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=shoes&type=ankle-boots"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Ankle Boots
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=shoes&type=sandals"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Sandals
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('womenAccessories')" class="flex justify-between w-full text-left px-4 py-2">
              Accessories
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('womenAccessories')" class="ml-2">
              <li>
                <Link href="/products?gender=women&category=accessories&type=scarf"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Scarves
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=accessories&type=sunglasses"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Sunglasses
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=accessories&type=belt"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Belts
                </Link>
              </li>
            </ul>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryMen')" class="flex justify-between w-full text-left px-4 py-2">
          Men
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryMen')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('menBags')" class="flex justify-between w-full text-left px-4 py-2">
              Bags
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('menBags')" class="ml-2">
              <li>
                <Link href="/products?gender=men&category=bags" class="flex gap-y-4 w-full text-left px-4 py-2">
                All Bags
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=bags&type=backpack"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Backpacks
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=bags&type=satchel"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Satchels
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=bags&type=clutch"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Clutches
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=bags&type=fanny"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Fanny Packs
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('menShoes')" class="flex justify-between w-full text-left px-4 py-2">
              Shoes
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('menShoes')" class="ml-2">
              <li>
                <Link href="/products?gender=men&category=shoes" class="flex gap-y-4 w-full text-left px-4 py-2">
                All Shoes
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=shoes&type=sneakers"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Sneakers
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=shoes&type=dress-boots"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Boots
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('menAccessories')" class="flex justify-between w-full text-left px-4 py-2">
              Accessories
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('menAccessories')" class="ml-2">
              <li>
                <Link href="/products?gender=men&category=accessories&type=scarf"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Scarves
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=accessories&type=sunglasses"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Sunglasses
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=accessories&type=belt"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                Belts
                </Link>
              </li>
            </ul>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryJewelry')" class="flex justify-between w-full text-left px-4 py-2">
          Jewelry
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryJewelry')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('jewelryCategories')" class="flex justify-between w-full text-left px-4 py-2">
              Categories
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('jewelryCategories')" class="ml-2">
              <li>
                <Link href="/products?category=jewelry" class="flex gap-y-4 w-full text-left px-4 py-2">
                All Fine Jewelry
                </Link>
              </li>
              <li>
                <Link href="/products?category=jewelry&type=bracelet" class="flex gap-y-4 w-full text-left px-4 py-2">
                Bracelets
                </Link>
              </li>
              <li>
                <Link href="/products?category=jewelry&type=necklace" class="flex gap-y-4 w-full text-left px-4 py-2">
                Necklaces
                </Link>
              </li>
              <li>
                <Link href="/products?category=jewelry&type=earrings" class="flex gap-y-4 w-full text-left px-4 py-2">
                Earrings
                </Link>
              </li>
              <li>
                <Link href="/products?category=jewelry&type=ring" class="flex gap-y-4 w-full text-left px-4 py-2">
                Rings
                </Link>
              </li>
            </ul>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryWatches')" class="flex justify-between w-full text-left px-4 py-2">
          Watches
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryWatches')" class="ml-2">
          <li>
            <Link href="/products?category=watches" class="flex gap-y-4 w-full text-left px-4 py-2">
            All Watches
            </Link>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryPerfumes')" class="flex justify-between w-full text-left px-4 py-2">
          Perfumes
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryPerfumes')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('jewelryCategories')" class="flex justify-between w-full text-left px-4 py-2">
              Categories
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('jewelryCategories')" class="ml-2">
              <li>
                <Link href="/products?category=perfumes" class="flex gap-y-4 w-full text-left px-4 py-2">
                All Perfumes
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=perfumes" class="flex gap-y-4 w-full text-left px-4 py-2">
                Women's Perfumes
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=perfumes" class="flex gap-y-4 w-full text-left px-4 py-2">
                Men's Perfumes
                </Link>
              </li>
            </ul>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</template>
