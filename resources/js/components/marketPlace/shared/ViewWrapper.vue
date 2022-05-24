<template>
    <v-main class="pa-0">
        <transition name="fade" mode="out-in">
            <router-view></router-view>
        </transition>
    </v-main>
</template>

<script>
    export default {
        name: 'ViewWrapper',
    }
</script>