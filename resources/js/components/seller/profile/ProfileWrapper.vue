<template>
    <v-container id="course" fluid tag="section">
        <v-row justify="center">
            <v-col cols="12">
                <base-material-card>
                    <template v-slot:heading>
                        <div class="display-2 font-weight-light mt-n3">Profile Overview</div>
                        <div class="subtitle-1 font-weight-light">Basic info about you.</div>
                    </template>
                    <v-container fluid>
                        <v-layout row justify-center wrap>
                            <v-flex lg9 sm8 xs12>
                                <transition name="fade" mode="out-in">
                                    <router-view></router-view>
                                </transition>
                            </v-flex>
                            <v-flex lg3 sm4 xs12 mt-12>
                                <sidemenu :threshold="67" :offset="90" :items="items"></sidemenu>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </base-material-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    import Sidemenu from '$comp/ui/Sidemenu'
    import {mapGetters} from 'vuex'

    export default {
        components: {
            Sidemenu
        },

        data: () => ({
            items: []
        }),

        computed: mapGetters({
            auth: 'auth/user'
        }),

        mounted() {
            this.navigation()
        },

        methods: {
            navigation() {
                this.items = [
                    {title: 'View', icon: 'visibility', to: {name: 'seller.profile'}},
                    {title: 'Edit', icon: 'edit', to: {name: 'seller.profile-edit'}},
                ]
            }
        }
    }
</script>
