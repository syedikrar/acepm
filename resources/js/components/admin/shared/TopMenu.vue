<template>
    <v-app-bar dark app flat class="spaBG">
        <v-app-bar-nav-icon @click.stop="navToggle"></v-app-bar-nav-icon>
        <v-toolbar-title class="white--text">{{ appName }}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon large
               :color="$vuetify.theme.dark ? 'black' : 'white'"
               title="Toggle Night Mode" @click="toggleNight">
            <v-icon>invert_colors</v-icon>
        </v-btn>
        <v-toolbar-items>
            <v-divider class="white ml-2" inset vertical></v-divider>
            <v-menu offset-y left>
                <template v-slot:activator="{ on }">
                    <v-btn
                        text
                        depressed
                        v-on="on"
                    >
                        {{username}}
                        <v-avatar class="mx-2">
                            <v-icon>account_circle</v-icon>
                        </v-avatar>
                        <i class="material-icons">arrow_drop_down</i>
                    </v-btn>
                </template>

                <v-list>
                    <v-list-item @click="logout">
                        <v-list-item-title>Logout</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-toolbar-items>
    </v-app-bar>
</template>

<script>
    import {settings} from '~/api'
    import {mapGetters} from 'vuex'

    export default {
        data: () => ({
            appName: settings.appName,
            avatar: '',
            username: ''
        }),
        computed: mapGetters({
            auth: 'auth/user'
        }),
        methods: {
            toggleNight() {
                this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
            },
            navToggle() {
                this.$emit('nav-toggle')
            },
            async logout() {
                let self = this;
                await this.$store.dispatch('auth/logout');
                self.showSnack('You are logged out.');
                self.$router.push({name: 'login'});
            }
        },
        mounted() {
            this.username = this.auth.name
        }
    }
</script>
