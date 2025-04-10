<script setup>
import Header from "@/Components/Header.vue";
import Sidebar from "@/Components/Sidebar.vue";
</script>

<template>
  <Header />
  <div class="flex">
    <Sidebar />
    <section class="flex flex-col container py-8 px-6 w-full">
      <slot />
    </section>
  </div>
</template>
