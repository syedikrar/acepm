<template>
    <div id="settings-wrapper">
        <v-card
            id="frontend"
            class="py-2 px-4"
            color="pink"
            dark
            flat
            link
            min-width="80"
            style="position: fixed; top: 180px; right: -40px; border-radius: 8px; z-index: 2"
            title="Shop Frontend"
            :href="'https://'+shop"
            target="_blank"
        >
            <v-icon class="ml-n2">
                mdi-monitor-screenshot
            </v-icon>
        </v-card>

        <v-card
            id="settings"
            class="py-2 px-4"
            color="info"
            dark
            flat
            link
            min-width="80"
            style="position: fixed; top: 115px; right: -40px; border-radius: 8px;"
        >
            <v-icon class="ml-n2">
                mdi-cog-outline
            </v-icon>
        </v-card>

        <v-menu
            v-model="menu"
            :close-on-content-click="false"
            activator="#settings"
            bottom
            content-class="v-settings"
            left
            nudge-left="8"
            offset-x
            origin="top right"
            transition="scale-transition"
        >
            <v-card
                class="text-center mb-0"
                width="300"
            >
                <v-card-text>
                    <strong class="mb-3 d-inline-block">SIDEBAR FILTERS</strong>

                    <v-item-group v-model="color">
                        <v-item
                            v-for="color in colors"
                            :key="color"
                            :value="color"
                        >
                            <template v-slot="{ active, toggle }">
                                <v-avatar
                                    :class="active && 'v-settings__item--active'"
                                    :color="color"
                                    class="v-settings__item"
                                    size="25"
                                    @click="toggle"
                                />
                            </template>
                        </v-item>
                    </v-item-group>

                    <v-divider class="my-4 secondary"/>

                    <v-row
                        align="center"
                        no-gutters
                    >
                        <v-col cols="auto">
                            Dark Mode
                        </v-col>

                        <v-spacer/>

                        <v-col cols="auto">
                            <v-switch
                                v-model="$vuetify.theme.dark"
                                class="ma-0 pa-0"
                                color="secondary"
                                hide-details
                                inset
                            />
                        </v-col>
                    </v-row>

                    <v-divider class="my-4 secondary"/>

                    <v-row
                        align="center"
                        no-gutters
                    >
                        <v-col cols="auto">
                            Sidebar Image
                        </v-col>

                        <v-spacer/>

                        <v-col cols="auto">
                            <v-switch
                                v-model="showImg"
                                class="ma-0 pa-0"
                                color="secondary"
                                hide-details
                                inset
                            />
                        </v-col>
                    </v-row>

                    <v-divider class="my-4 secondary"/>

                    <strong class="mb-3 d-inline-block">IMAGES</strong>

                    <v-item-group
                        v-model="image"
                        class="d-flex justify-space-between mb-3"
                    >
                        <v-item
                            v-for="image in images"
                            :key="image"
                            :value="image"
                            class="mx-1"
                        >
                            <template v-slot="{ active, toggle }">
                                <v-sheet
                                    :class="active && 'v-settings__item--active'"
                                    class="d-inline-block v-settings__item"
                                    @click="toggle"
                                >
                                    <v-img
                                        :src="image"
                                        height="100"
                                        width="50"
                                    />
                                </v-sheet>
                            </template>
                        </v-item>
                    </v-item-group>

                    <div class="my-4"/>

                    <v-btn
                        class="ma-1"
                        color="#3b5998"
                        dark
                        depressed
                        rounded
                        href="https://www.facebook.com/groups/310779136234677"
                        target="_blank">
                        <v-icon left>mdi-facebook</v-icon>
                        EOE Community
                    </v-btn>
                </v-card-text>
            </v-card>
        </v-menu>
        <v-navigation-drawer
            fixed
            :right="!$vuetify.rtl"
            floating
            :mini-variant="emailMini"
            hide-overlay
            width="fit-content"
            mini-variant-width="50px"
            class="rightbar emailSupport elevation-2"
            v-bind:class="$vuetify.rtl ? 'right-curve' : 'left-curve'"
        >
            <template v-slot:prepend>
                <v-tooltip
                    :left="!$vuetify.rtl"
                    :right="$vuetify.rtl"
                >
                    <template v-slot:activator="{ on }">
                        <v-list-item v-on="on" two-line class="head px-0">
                            <v-list-item-avatar style="margin: 5px !important;" width="34px" min-width="34px" height="34px" min-height="34px">
                                <v-btn icon text  color="white" @click="emailMini = !emailMini">
                                    <v-icon>{{ emailMini ? 'mdi-email-plus-outline' : 'mdi-chevron-double-right' }}</v-icon>
                                </v-btn>
                            </v-list-item-avatar>
                            <v-list-item-title>
                                <v-btn text small class="whiteLink mr-2" :href="supportLink" max-width="180px">Email Support</v-btn>
                            </v-list-item-title>
                        </v-list-item>
                    </template>
                    <span>Need Help ? just drop us an email and we will get right back to you soon.</span>
                </v-tooltip>
            </template>
        </v-navigation-drawer>
    </div>
</template>

<script>
    // Mixins
    import Proxyable from 'vuetify/lib/mixins/proxyable'
    import {mapMutations, mapState, mapGetters} from 'vuex'

    export default {
        name: 'Settings',
        mixins: [Proxyable],

        data: () => ({
            color: '#E91E63',
            colors: [
                '#9C27b0',
                '#00CAE3',
                '#4CAF50',
                '#ff9800',
                '#E91E63',
                '#FF5252',
            ],
            image: 'https://wallpaperaccess.com/full/1288107.jpg',
            images: [
                'https://wallpaperaccess.com/full/1288107.jpg',
                'https://wallpaperaccess.com/full/1288084.jpg',
                'https://wallpaperaccess.com/full/1288091.jpg',
                'https://wallpaperaccess.com/full/1288220.jpg',
            ],
            menu        : false,
            saveImage   : '',
            showImg     : true,
            emailMini   : true
        }),

        computed: {
            ...mapState(['barImage']),
            ...mapGetters({
                shop : 'auth/shop'
            }),
            supportLink : function(){
                return 'mailto:support@eraofecom.org?subject=Need help with '+appSlug.appName+' -- '+this.shop
            }
        },

        watch: {
            color(val) {
                this.$vuetify.theme.themes[this.isDark ? 'dark' : 'light'].primary = val
            },
            showImg(val) {
                if (!val) {
                    this.saveImage = this.barImage
                    this.setBarImage('')
                } else if (this.saveImage) {
                    this.setBarImage(this.saveImage)
                    this.saveImage = ''
                } else {
                    this.setBarImage(val)
                }
            },
            image(val) {
                this.setBarImage(val)
            },
        },

        methods: {
            ...mapMutations({
                setBarImage: 'SET_BAR_IMAGE',
            }),
        },
    }
</script>
