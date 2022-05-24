<template>
    <v-main>
        <transition name="fade" mode="out-in">
            <router-view :key="$route.path"></router-view>
        </transition>
        <div class="my-10"></div>
        <foot />
    </v-main>
</template>

<script>
    import Foot from "./Foot";
    export default {
        name: 'ViewWrapper',

        components: {
            'foot': Foot
        },
    }
</script>
