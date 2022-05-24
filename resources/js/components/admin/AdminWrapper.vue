<template>
    <v-app>
        <app-bar></app-bar>
        <drawer></drawer>
        <settings></settings>
        <view-wrapper></view-wrapper>
        <integrity></integrity>
    </v-app>
</template>

<script>
    import AppBar from "./shared/AppBar";
    import Drawer from "./shared/Drawer";
    import Settings from "./shared/Settings";
    import ViewWrapper from "./shared/ViewWrapper";
    import Integrity from "./shared/integrity";

    export default {
        components: {
            Integrity,
            'app-bar'       : AppBar,
            'drawer'        : Drawer,
            'settings'      : Settings,
            'view-wrapper'  : ViewWrapper
        },
        mounted() {
            document.querySelector('html').style.overflow = 'scroll';
            document.querySelector('html').style.overflowX = 'hidden';
        }
    }
</script>
