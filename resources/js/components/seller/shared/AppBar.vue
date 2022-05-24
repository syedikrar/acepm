<template>
    <v-app-bar id="app-bar" absolute app color="info" flat height="75">
        <v-toolbar-title class="hidden-sm-and-down font-weight-light">Seller Area</v-toolbar-title>
        <v-spacer/>
        <v-menu bottom left offset-y origin="top right" transition="scale-transition" v-if="true == false">
            <template v-slot:activator="{ attrs, on }">
                <v-btn class="ml-2" min-width="0" text v-bind="attrs" v-on="on">
                    <v-badge color="red" overlap bordered>
                        <template v-slot:badge>
                            <span>5</span>
                        </template>
                        <v-icon>mdi-bell</v-icon>
                    </v-badge>
                </v-btn>
            </template>

            <v-list :tile="false" nav>
                <div>
                    <app-bar-item v-for="(n, i) in notifications" :key="`item-${i}`">
                        <v-list-item-title v-text="n"/>
                    </app-bar-item>
                </div>
            </v-list>
        </v-menu>

        <v-menu offset-y left>
            <template v-slot:activator="{ on }">
                <v-btn
                    text
                    depressed
                    v-on="on"
                >
                    {{user != null ? user.name : 'User'}}
                    <v-avatar :size="32" class="mx-2">
                        <v-icon :size="32">account_circle</v-icon>
                    </v-avatar>
                    <i class="material-icons">arrow_drop_down</i>
                </v-btn>
            </template>
            <v-list>
                <v-list-item @click="navigate">
                    <v-list-item-title>Profile</v-list-item-title>
                </v-list-item>
                <v-list-item @click="logout">
                    <v-list-item-title>Logout</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-menu>
    </v-app-bar>
</template>

<script>
    // Components
    import {VHover, VListItem} from 'vuetify/lib'
    import {settings, api} from '~/api'
    import {mapState, mapMutations, mapGetters} from 'vuex'

    export default {
        name: 'AppBar',

        components: {
            AppBarItem: {
                render(h) {
                    return h(VHover, {
                        scopedSlots: {
                            default: ({hover}) => {
                                return h(VListItem, {
                                    attrs: this.$attrs,
                                    class: {
                                        'black--text': !hover,
                                        'white--text secondary elevation-12': hover,
                                    },
                                    props: {
                                        activeClass: '',
                                        dark: hover,
                                        link: true,
                                        ...this.$attrs,
                                    },
                                }, this.$slots.default)
                            },
                        },
                    })
                },
            },
        },

        props: {
            value: {
                type: Boolean,
                default: false,
            },
        },

        data: () => ({
            notifications: [
                'Mike John Responded to your email',
                'You have 5 new tasks',
                'You\'re now friends with Andrew',
                'Another Notification',
                'Another one',
            ],
            username : null,
            appName: settings.appName,
            avatar: '',
            askUninstall : false
        }),

        computed: {
            ...mapState(['drawer']),
            ...mapGetters({
                user: 'auth/user'
            })
        },

        methods: {
            ...mapMutations({
                setDrawer: 'SET_DRAWER',
            }),
            async logout() {
                let self = this;
                await this.$store.dispatch('auth/logout');
                self.showSnack('You are logged out.');
                self.$router.push({name: 'login'});
            },
            cleanUninstall(){
                let self = this;
                self.showCloak(function(){
                    axios
                        .patch(api.path('shopify.cleanUninstall'), {clean : true})
                        .then(function(resp){
                            if (resp.data.status == true) {
                                self.hideCloak();
                                self.logout();
                                self.showAlert('App Uninstalled, please close this window.');
                            }
                        });
                });
            },
            navigate(){
                this.$router.push({ name: (this.auth.role == 'seller' ? 'seller.' : '')+'profile'});
            }
        },
        mounted() {
            this.username = this.user.name
        }
    }
</script>
