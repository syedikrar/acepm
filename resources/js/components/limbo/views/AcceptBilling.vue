<template>
    <div>
        <v-row>
            <v-col cols="12" class="d-flex justify-center" v-if="billing.state == 'declined'">
                <p class="text-h5 white--text font-weight-light ma-0">You declined the charge.</p>
            </v-col>
            <v-col cols="12" class="d-flex justify-center">
                <p class="text-h5 white--text font-weight-light">You need to accept billing to use {{slug.appName}}.</p>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" class="d-flex justify-center">
                <p class="text-body-2 white--text font-weight-light">You can either</p>
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-col cols="6" md="4" class="divider white--text">
                <v-row>
                    <v-col cols="12" class="d-flex justify-center">Click below to go back and preform the</v-col>
                    <v-col cols="12" class="d-flex justify-center">
                        <v-btn large color="info" :to="{name: 'limbo.plans'}">Billing Setup</v-btn>
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="6" md="4" class="white--text">
                <v-row>
                    <v-col cols="12" class="d-flex justify-center">Click below to go back to apps page and</v-col>
                    <v-col cols="12" class="d-flex justify-center">
                        <v-btn large color="error" outlined dark :href="appsPage">Uninstall App</v-btn>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </div>
</template>

<script>

    import {mapGetters, mapState} from "vuex"

    export default {
        data: () => ({
            slug : window.appSlug,
            busy : false
        }),

        computed: {
            ...mapGetters({
                auth    : 'auth/user',
                billing : 'auth/billing',
                shop    : 'auth/shop'
            }),
            appsPage : function(){
                return 'https://' + this.shop + '/admin/apps';
            }
        },
        methods: {

        },
        mounted() {

        }
    }
</script>
