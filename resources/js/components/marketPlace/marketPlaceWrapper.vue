<template>
    <v-app>
        <app-bar></app-bar>
        <settings></settings>
        <v-row flat>
            <v-col cols="9">
                <view-wrapper></view-wrapper>
            </v-col>
            <v-col cols="3">
                <best-selling-gigs></best-selling-gigs>
                <best-sellers></best-sellers>
            </v-col>
        </v-row>
    </v-app>
</template>

<script>
    import AppBar from "./shared/AppBar";
    import Settings from "./shared/Settings";
    import ViewWrapper from "./shared/ViewWrapper";
    import BestSellingGigs from "./widgets/bestSellingGigs";
    import BestSellers from "./widgets/bestSellers";

    export default {
        components: {
            'app-bar'       : AppBar,
            'settings'      : Settings,
            'view-wrapper'  : ViewWrapper,
            BestSellingGigs,
            BestSellers
        },
        mounted() {
            document.querySelector('html').style.overflow = 'scroll';
            document.querySelector('html').style.overflowX = 'hidden';
        }
    }
</script>