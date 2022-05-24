<template>
  <v-flex xs12 sm12 md12 lg8 class="px-2">
    <transition name="fade" mode="out-in">
      <router-view></router-view>
    </transition>
  </v-flex>
</template>

<script>
export default {}
</script>
