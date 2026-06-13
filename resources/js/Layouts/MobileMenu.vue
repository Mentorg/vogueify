<script setup>
import { ref } from 'vue';
import {
  PhX,
  PhCaretRight,
} from "@phosphor-icons/vue";
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { capitalize } from '@/utils/capitalize';

defineProps({
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
      <button @click="closeMenu" :title="t('common.button.closeCategoryMenuTitle')">
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
        <button @click="toggleSubMenu('categoryNew')" :title="t('common.catalogMenu.new.title')"
          class="flex justify-between w-full text-left px-4 py-2">
          {{ t('common.category.new') }}
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryNew')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('newWomen')" :title="t('common.catalogMenu.new.women.title')"
              class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.menuLabel.for') }} {{ t('common.gender.woman', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('newWomen')" class="ml-2">
              <li>
                <Link href="/products?gender=women&category=bags" :title="t('common.catalogMenu.new.women.bags')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.category.bags', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=shoes" :title="t('common.catalogMenu.new.women.shoes')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.category.shoes', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=accessories"
                  :title="t('common.catalogMenu.new.women.accessories')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.category.accessories', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=jewelry" :title="t('common.catalogMenu.new.women.jewelry')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.category.jewelry', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=watches" :title="t('common.catalogMenu.new.women.watches')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.category.watches', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=fragrances"
                  :title="t('common.catalogMenu.new.women.fragrances')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.category.fragrances', 2) }}
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('newMen')" :title="t('common.catalogMenu.new.men.title')"
              class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.menuLabel.for') }} {{ t('common.gender.man', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('newMen')" class="ml-2">
              <li>
                <Link href="/products?gender=men&category=bags" :title="t('common.catalogMenu.new.men.bags')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.category.bags', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=shoes" :title="t('common.catalogMenu.new.men.shoes')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.category.shoes', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=accessories"
                  :title="t('common.catalogMenu.new.men.accessories')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.category.accessories', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=jewelry" :title="t('common.catalogMenu.new.men.jewelry')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.category.jewelry', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=watches" :title="t('common.catalogMenu.new.men.watches')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.category.watches', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=fragrances"
                  :title="t('common.catalogMenu.new.men.fragrances')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.category.fragrances', 2) }}
                </Link>
              </li>
            </ul>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryWomen')" :title="t('common.catalogMenu.women.title')"
          class="flex justify-between w-full text-left px-4 py-2">
          {{ t('common.gender.woman', 2) }}
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryWomen')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('womenBags')" :title="t('common.catalogMenu.women.bags.title')"
              class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.category.bags', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('womenBags')" class="ml-2">
              <li>
                <Link href="/products?gender=women&category=bags" :title="t('common.catalogMenu.women.bags.all')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.menuLabel.all') }} {{ t('common.category.bags', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=bags&type=handbag"
                  :title="t('common.catalogMenu.women.bags.handbags')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.bag.handbag', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=bags&type=bucket"
                  :title="t('common.catalogMenu.women.bags.bucket')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.bag.bucket', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=bags&type=hobo"
                  :title="t('common.catalogMenu.women.bags.hobo')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.bag.hobo', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=bags&type=envelope"
                  :title="t('common.catalogMenu.women.bags.envelope')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.bag.envelope', 2) }}
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('womenShoes')" :title="t('common.catalogMenu.women.shoes.title')"
              class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.category.shoes', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('womenShoes')" class="ml-2">
              <li>
                <Link href="/products?gender=women&category=shoes" :title="t('common.catalogMenu.women.shoes.all')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.menuLabel.all') }} {{ t('common.category.shoes', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=shoes&type=sneakers"
                  :title="t('common.catalogMenu.women.shoes.sneakers')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.shoe.sneaker', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=shoes&type=ankle-boots"
                  :title="t('common.catalogMenu.women.shoes.ankleBoots')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.shoe.ankle', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=shoes&type=sandals"
                  :title="t('common.catalogMenu.women.shoes.sandals')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.shoe.sandal', 2) }}
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('womenAccessories')" :title="t('common.catalogMenu.women.accessories.title')"
              class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.category.accessories', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('womenAccessories')" class="ml-2">
              <li>
                <Link href="/products?gender=women&category=accessories&type=scarf"
                  :title="t('common.catalogMenu.women.accessories.scarves')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.accessory.scarf', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=accessories&type=sunglasses"
                  :title="t('common.catalogMenu.women.accessories.sunglasses')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.accessory.sunglasses', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=accessories&type=belt"
                  :title="t('common.catalogMenu.women.accessories.belts')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.accessory.belt', 2) }}
                </Link>
              </li>
            </ul>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryMen')" :title="t('common.catalogMenu.men.title')"
          class="flex justify-between w-full text-left px-4 py-2">
          {{ t('common.gender.man', 2) }}
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryMen')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('menBags')" :title="t('common.catalogMenu.men.bags.title')"
              class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.category.bags', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('menBags')" class="ml-2">
              <li>
                <Link href="/products?gender=men&category=bags" :title="t('common.catalogMenu.men.bags.all')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.menuLabel.all') }} {{ t('common.category.bags', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=bags&type=backpack"
                  :title="t('common.catalogMenu.men.bags.backpack')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.bag.backpack', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=bags&type=satchel"
                  :title="t('common.catalogMenu.men.bags.satchel')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.bag.satchel', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=bags&type=clutch"
                  :title="t('common.catalogMenu.men.bags.clutch')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.bag.clutch', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=bags&type=fanny"
                  :title="t('common.catalogMenu.men.bags.fannyPack')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.bag.fanny', 2) }}
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('menShoes')" :title="t('common.catalogMenu.men.shoes.title')"
              class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.category.shoes', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('menShoes')" class="ml-2">
              <li>
                <Link href="/products?gender=men&category=shoes" :title="t('common.catalogMenu.men.shoes.all')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.menuLabel.all') }} {{ t('common.category.shoes', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=shoes&type=sneakers"
                  :title="t('common.catalogMenu.men.shoes.sneakers')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.shoe.sneaker', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=shoes&type=dress-boots"
                  :title="t('common.catalogMenu.men.shoes.boots')" class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.shoe.boot', 2) }}
                </Link>
              </li>
            </ul>
          </li>
          <li>
            <button @click="toggleSubMenu('menAccessories')" :title="t('common.catalogMenu.men.accessories.title')"
              class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.category.accessories', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('menAccessories')" class="ml-2">
              <li>
                <Link href="/products?gender=men&category=accessories&type=scarf"
                  :title="t('common.catalogMenu.men.accessories.scarves')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.accessory.scarf', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=accessories&type=sunglasses"
                  :title="t('common.catalogMenu.men.accessories.sunglasses')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.accessory.sunglasses', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=accessories&type=belt"
                  :title="t('common.catalogMenu.men.accessories.belts')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.accessory.belt', 2) }}
                </Link>
              </li>
            </ul>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryJewelry')" :title="t('common.catalogMenu.jewelry.title')"
          class="flex justify-between w-full text-left px-4 py-2">
          {{ t('common.category.jewelry', 2) }}
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryJewelry')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('jewelryCategories')"
              :title="t('common.catalogMenu.jewelry.categories.title')"
              class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.menuLabel.category', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('jewelryCategories')" class="ml-2">
              <li>
                <Link href="/products?category=jewelry" :title="t('common.catalogMenu.jewelry.categories.all')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.menuLabel.all', 2) }} {{ t('common.menuLabel.fine', 2) }} {{
                    t('common.category.jewelry', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?category=jewelry&type=bracelet"
                  :title="t('common.catalogMenu.jewelry.categories.bracelets')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.jewelry.bracelet', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?category=jewelry&type=necklace"
                  :title="t('common.catalogMenu.jewelry.categories.necklaces')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.jewelry.necklace', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?category=jewelry&type=earrings"
                  :title="t('common.catalogMenu.jewelry.categories.earrings')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.jewelry.earring', 2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?category=jewelry&type=ring"
                  :title="t('common.catalogMenu.jewelry.categories.rings')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.subCategory.jewelry.ring', 2) }}
                </Link>
              </li>
            </ul>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryWatches')" :title="t('common.catalogMenu.watches.title')"
          class="flex justify-between w-full text-left px-4 py-2">
          {{ t('common.category.watches', 2) }}
          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryWatches')" class="ml-2">
          <li>
            <Link href="/products?category=watches" :title="t('common.catalogMenu.watches.all')"
              class="flex gap-y-4 w-full text-left px-4 py-2">
              {{ t('common.menuLabel.all') }} {{ t('common.category.watches', 2) }}
            </Link>
          </li>
        </ul>
        <button @click="toggleSubMenu('categoryFragrances')" :title="t('common.catalogMenu.fragrances.title')"
          class="flex justify-between w-full text-left px-4 py-2">
          {{ t('common.category.fragrances', 2) }}

          <PhCaretRight size="24" />
        </button>
        <ul v-if="isSubMenuOpen('categoryFragrances')" class="ml-2">
          <li>
            <button @click="toggleSubMenu('jewelryCategories')"
              :title="t('common.catalogMenu.fragrances.categories.title')"
              class="flex justify-between w-full text-left px-4 py-2">
              {{ t('common.menuLabel.category', 2) }}
              <PhCaretRight size="24" />
            </button>
            <ul v-if="isSubMenuOpen('jewelryCategories')" class="ml-2">
              <li>
                <Link href="/products?category=fragrances" :title="t('common.catalogMenu.fragrances.categories.all')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.menuLabel.all', 2) }} {{ t('common.category.fragrances', 2)
                  }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=women&category=fragrances"
                  :title="t('common.catalogMenu.fragrances.categories.women')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.gender.woman', 2) }} {{ t('common.category.fragrances',
                    2) }}
                </Link>
              </li>
              <li>
                <Link href="/products?gender=men&category=fragrances"
                  :title="t('common.catalogMenu.fragrances.categories.men')"
                  class="flex gap-y-4 w-full text-left px-4 py-2">
                  {{ t('common.gender.man', 2) }} {{ t('common.category.fragrances',
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
