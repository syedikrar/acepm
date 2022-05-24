<template>
    <v-main fluid class="fullBG limbo">
        <v-layout class="limboTop">
            <v-row>
                <v-col cols="12" class="d-flex justify-center">
                    <v-img src="/images/logo.png" :width="80" :max-width="80" />
                    <span class="brand ml-4 no-select">{{appName}}</span>
                </v-col>
                <v-col cols="12" class="d-flex justify-center">
                    <hr style="width: 800px" />
                </v-col>
            </v-row>
        </v-layout>
        <v-container>
            <transition name="fade" mode="out-in">
                <router-view></router-view>
            </transition>
        </v-container>
    </v-main>
</template>

<script>
    import {mapGetters, mapState} from "vuex";

    export default {
        data: () => ({
            appName     : appSlug.appName
        }),

        computed: {
            ...mapGetters({
                auth    : 'auth/user',
                billing : 'auth/billing'
            })
        },

        methods: {

        },
        mounted() {
            let self = this;
            // ----- check and redirect to appropriate limbo route

            if (self.$router.currentRoute.path == '/limbo') {
                switch (self.billing.state) {
                    case 'billing_not_set':
                    case 'declined':
                    case 'pending':
                        this.$router.push({name: 'limbo.accept-billing'})
                        break;
                    case 'choose_plan':
                        this.$router.push({name: 'limbo.plans'})
                        break;
                }
            }
        }
    }
</script>
