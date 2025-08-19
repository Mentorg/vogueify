<script setup>
import { defineProps, ref } from 'vue';
import {
  PhX,
  PhCaretRight,
} from "@phosphor-icons/vue";
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { capitalize } from '@/utils/capitalize';

const props = defineProps({
  toggleMenu: Function,
  isMenuOpen: Boolean,
  closeMenu: Function
})

const { t } = useI18n();

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
      <div class="locale-changer flex justify-end px-4 py-2">
        <select name="locale-changer" id="locale-changer" v-model="$i18n.locale"
          class="py-0 cursor-pointer border-none text-sm">
          <option v-for="locale in $i18n.availableLocales" :key="`locale-${locale}`" :value="locale">{{
            capitalize(locale) }}
          </option>
        </select>
      </div>
    </div>
    <ul>
      <li class="relative">
        <button @click="toggleSubMenu('categoryNew')" class="flex justify-between w-full text-left px-4 py-2">
          {{ t('common.category.new') }}
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryNew')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('newWomen')" class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.menuLabel.for') }} {{ t('common.gender.woman', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('newWomen')" class="ml-2">
              <li>
                <Link href="/products?gender=women&category=bags" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.category.bags', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=shoes" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.category.shoes', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=accessories"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.category.accessories', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=jewelry" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.category.jewelry', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=watches" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.category.watches', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=perfumes" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.category.perfumes', 2) }}
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('newMen')" class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.menuLabel.for') }} {{ t('common.gender.man', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('newMen')" class="ml-2">
              <li>
                <Link href="/products?gender=men&category=bags" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.category.bags', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=shoes" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.category.shoes', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=accessories" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.category.accessories', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=jewelry" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.category.jewelry', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=watches" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.category.watches', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=perfumes" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.category.perfumes', 2) }}
                </Link>
              </li>
            </ul>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryWomen')" class="flex justify-between w-full text-left px-4 py-2">
          {{ t('common.gender.woman', 2) }}
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryWomen')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('womenBags')" class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.category.bags', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('womenBags')" class="ml-2">
              <li>
                <Link href="/products?gender=women&category=bags" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.menuLabel.all') }} {{ t('common.category.bags', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=bags&type=handbag"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.bag.handbag', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=bags&type=bucket"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.bag.bucket', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=bags&type=hobo"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.bag.hobo', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=bags&type=envelope"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.bag.envelope', 2) }}
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('womenShoes')" class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.category.shoes', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('womenShoes')" class="ml-2">
              <li>
                <Link href="/products?gender=women&category=shoes" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.menuLabel.all') }} {{ t('common.category.shoes', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=shoes&type=sneakers"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.shoe.sneaker', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=shoes&type=ankle-boots"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.shoe.ankle', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=shoes&type=sandals"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.shoe.sandal', 2) }}
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('womenAccessories')" class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.category.accessories', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('womenAccessories')" class="ml-2">
              <li>
                <Link href="/products?gender=women&category=accessories&type=scarf"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.accessory.scarf', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=accessories&type=sunglasses"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.accessory.sunglasses', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=accessories&type=belt"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.accessory.belt', 2) }}
                </Link>
              </li>
            </ul>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryMen')" class="flex justify-between w-full text-left px-4 py-2">
          {{ t('common.gender.man', 2) }}
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryMen')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('menBags')" class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.category.bags', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('menBags')" class="ml-2">
              <li>
                <Link href="/products?gender=men&category=bags" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.menuLabel.all') }} {{ t('common.category.bags', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=bags&type=backpack"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.bag.backpack', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=bags&type=satchel"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.bag.satchel', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=bags&type=clutch"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.bag.clutch', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=bags&type=fanny"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.bag.fanny', 2) }}
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('menShoes')" class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.category.shoes', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('menShoes')" class="ml-2">
              <li>
                <Link href="/products?gender=men&category=shoes" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.menuLabel.all') }} {{ t('common.category.shoes', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=shoes&type=sneakers"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.shoe.sneaker', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=shoes&type=dress-boots"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.shoe.boot', 2) }}
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('menAccessories')" class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.category.accessories', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('menAccessories')" class="ml-2">
              <li>
                <Link href="/products?gender=men&category=accessories&type=scarf"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.accessory.scarf', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=accessories&type=sunglasses"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.accessory.sunglasses', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=accessories&type=belt"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.accessory.belt', 2) }}
                </Link>
              </li>
            </ul>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryJewelry')" class="flex justify-between w-full text-left px-4 py-2">
          {{ t('common.category.jewelry', 2) }}
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryJewelry')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('jewelryCategories')" class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.menuLabel.category', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('jewelryCategories')" class="ml-2">
              <li>
                <Link href="/products?category=jewelry" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.menuLabel.all', 2) }} {{ t('common.menuLabel.fine', 2) }} {{
                  t('common.category.jewelry', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?category=jewelry&type=bracelet" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.jewelry.bracelet', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?category=jewelry&type=necklace" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.jewelry.necklace', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?category=jewelry&type=earrings" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.jewelry.earring', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?category=jewelry&type=ring" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.subCategory.jewelry.ring', 2) }}
                </Link>
              </li>
            </ul>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryWatches')" class="flex justify-between w-full text-left px-4 py-2">
          {{ t('common.category.watches', 2) }}
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryWatches')" class="ml-2">
          <li>
            <Link href="/products?category=watches" class="flex gap-y-4 w-full text-left px-4 py-2">
            {{ t('common.menuLabel.all') }} {{ t('common.category.watches', 2) }}
            </Link>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryPerfumes')" class="flex justify-between w-full text-left px-4 py-2">
          {{ t('common.category.perfumes', 2) }}

          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryPerfumes')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('jewelryCategories')" class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.menuLabel.category', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('jewelryCategories')" class="ml-2">
              <li>
                <Link href="/products?category=perfumes" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.menuLabel.all', 2) }} {{ t('common.category.perfumes', 2)
                }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=perfumes" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.gender.woman', 2) }} {{ t('common.category.perfumes',
                  2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=perfumes" class="flex gap-y-4 w-full text-left px-4 py-2">
                {{ t('common.gender.man', 2) }} {{ t('common.category.perfumes',
                  2) }}
                </Link>
              </li>
            </ul>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</template>
