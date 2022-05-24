<template>
    <v-card class="mx-auto my-auto"
            min-width="374">
        <v-card-title class="justify-center">
            Select your Shop
        </v-card-title>
        <v-card-text>
            <v-list>
                <template v-for="shop in user.shops">
                    <v-list-item
                            :key="shop.name"
                            @click="goto(shop)"
                    >
                        <v-list-item-avatar>
                            <v-icon color="info"> mdi-bulletin-board </v-icon>
                        </v-list-item-avatar>
                        <v-list-item-title v-text="shop.name"></v-list-item-title>
                    </v-list-item>
                </template>
            </v-list>
        </v-card-text>
    </v-card>
</template>

<script>
    import {mapGetters} from "vuex";
    import Helper from "../helpers/Helper"

    export default {
        data(){
            return {}
        },
        computed: {
            ...mapGetters({
                user    : 'auth/user',
            })
        },
        methods: {
            goto(shop){
                Helper.writeCookie('ace-shop', shop.domain, 0.11,);
                Helper.writeCookie('ace-shop-id', shop.id, 0.11,);
                this.$router.push({name: 'index'});
            }
        },
        mounted() {
            let self = this;

            if(Helper.readCookie('ace-shop') &&
                Helper.readCookie('ace-shop-id') &&
                this.$route.params.cookie == undefined) {this.$router.push({name: 'index'}); return;}

            if(self.user.shops.length == 1) self.goto(self.user.shops[0]);
        }
    }
</script>
