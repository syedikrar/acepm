<template>
    <v-navigation-drawer
        id="core-navigation-drawer"
        v-model="drawer"
        :expand-on-hover="expandOnHover"
        :right="$vuetify.rtl"
        mobile-breakpoint="960"
        app
        width="260"
        class="slimScroll"
        :mini-variant="miniNav"
        mini-variant-width="80"
        v-bind="$attrs">
        <!--<template v-slot:img="props">
            <v-img :gradient="`to bottom, ${barColor}`" v-bind="props" />
        </template>-->

        <v-list class="py-0" color="transparent">
            <v-list-item>
                <v-list-item-content>
                    <v-list-item-title>
                        <v-img src="/images/logo-color.png" height="100" contain></v-img>
                        <div class="d-flex justify-center">
                            <span class="brand no-select" style="font-size: 20px">{{appName}}</span>
                        </div>
                        <!--<div class="loves">
                            <v-icon color="pink" small>mdi-heart-outline</v-icon>
                        </div>-->
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>

        <v-list expand nav color="transparent">
            <!-- Style cascading bug  -->
            <!-- https://github.com/vuetifyjs/vuetify/pull/8574 -->
            <div />

            <template v-for="(item, i) in computedItems">
                <h2 class="divider no-select" v-if="'section' in item">{{item['section']}}</h2>
                <base-item-group
                    v-if="item.children"
                    :key="`group-${i}`"
                    :item="item">
                    <!--  -->
                </base-item-group>

                <base-item
                    v-else
                    :key="`item-${i}`"
                    :item="item"
                    tooltip
                    class="font-weight-bold">
                    <div class="stripes" v-if="'plan' in item && item.plan != plan" title="Feature Locked !"></div>
                </base-item>
            </template>

            <!-- Style cascading bug  -->
            <!-- https://github.com/vuetifyjs/vuetify/pull/8574 -->
            <div/>
        </v-list>

        <template v-slot:append>
            <base-item
                v-if="plan != 'enterprise'"
                class="green lighten-1"
                :item="{
          title: $t('upgrade'),
          icon: 'mdi-package-up',
          to: {name: 'plans'},
        }"
            />
        </template>
    </v-navigation-drawer>
</template>

<script>
    // Utilities
    import {
        mapState, mapGetters
    } from 'vuex'

    export default {
        name: 'Drawer',

        props: {
            expandOnHover: {
                type: Boolean,
                default: false,
            },
        },

        data: () => ({
            items       : [],
            name        : null,
            miniNav     : true,
            appName     : appSlug.appName.split('-')[0].trim()
        }),

        computed: {
            ...mapState(['barColor', 'barImage']),
            ...mapGetters({ auth: 'auth/user' }),
            ...mapGetters({ plan: 'auth/plan' }),
            drawer: {
                get() {
                    return this.$store.state.drawer
                },
                set(val) {
                    this.$store.commit('SET_DRAWER', val)
                },
            },
            barColor() {
                return this.$vuetify.theme.dark ? 'rgba(0, 0, 0, .8), rgba(0, 0, 0, .8)' : 'rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)'
            },
            computedItems() {
                return this.items.map(this.mapItem)
            },
            profile() {
                return {
                    avatar: true,
                    title: this.$t('avatar'),
                }
            },
        },

        methods: {
            mapItem(item) {
                return {
                    ...item,
                    children: item.children ? item.children.map(this.mapItem) : undefined,
                    title: this.$t(item.title),
                }
            },
            getNavigation(){
                let self = this;
                self.items = [];
                self.items.push(
                    {
                        title   : 'nav.dashboard',
                        icon    : 'mdi-wall',
                        to      : {name: 'dashboard'}
                    }
                );
                let user = self.getUser();

                if (user.role == 'owner' || user.role == 'staff') {
                    axios.get(api.path('integrity.fieldTypes')).then(function(resp){
                        resp.data.forEach(function(field){

                            self.items.push({
                                title   : 'nav.'+field.slug,
                                icon    : field.icon,
                                to      : {name: 'ace_boards', params: {field: field.slug}}
                            });
                        });

                        self.items.push({
                            title   : 'Contracts',
                            icon    : 'mdi-clipboard-text',
                            to      : {name: 'gigs.contracts'},
                        });

                    });
                }

                this.items.push({
                    title   : 'nav.marketPlace',
                    icon    : 'mdi-bulletin-board',
                    to      : {name: 'marketPlace'},
                });

                let shop = self.getShop();
                if(shop == 'prospots.myshopify.com'){
                    self.items.push({
                        title   : 'CSUI',
                        section : 'CSUI',
                        icon    : 'mdi-headset',
                        to      : {name: 'csui'},
                    });
                }
            }
        },
        mounted() {
            this.name = this.auth.name;
            this.getNavigation();
        }
    }
</script>
