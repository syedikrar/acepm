<template>
    <v-app id="spa">
        <v-snackbar v-model="globals.snack.show" :color="globals.snack.color" :timeout="5000" bottom right>
            {{ globals.snack.text }}
            <template v-slot:action="{ attrs }">
                <v-btn color="black" text @click="globals.snack.show = false">Close</v-btn>
            </template>
        </v-snackbar>
        <v-dialog v-model="globals.alert.show" width="500" persistent>
            <v-card>
                <v-card-title class="headline py-3 quicksand">
                    <v-icon large left class="primary--text">mdi-message-alert-outline</v-icon>
                    <span>{{appName}}</span>
                </v-card-title>
                <v-card-text class="mt-2">
                    {{globals.alert.text}}
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn depressed color="primary"
                           @click="globals.alert.show = false" :width="70" class="mr-0">Ok</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <transition name="fade" mode="out-in">
            <router-view></router-view>
        </transition>
    </v-app>
</template>

<script>
    import {mapGetters, mapState} from "vuex";

    export default {
        data(){
            return {
                appName : appSlug.appName
            }
        },
        computed: {
            ...mapGetters({
                auth    : 'auth/user',
                billing : 'auth/billing'
            })
        },
        watch: {
            billing(oldval, newval){
                let self = this;
                // ----- remove cloak

            }
        },
        created() {
            let self = this;
            self.$root.$el.classList.remove('cloaked')
        }
    }
</script>
