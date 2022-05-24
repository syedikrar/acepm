<template>
    <div>
        <v-main>
            <section id="hero">
                <v-row no-gutters>
                    <v-img max-height="100vh" src="/images/bg.svg">
                        <v-container fill-height fluid>
                            <v-row class="align-self-stretch mt-15 pt-15">
                                <v-col cols="12" sm="5" md="7" class="d-flex flex-column justify-center">
                                    <div style="margin: 20px auto 0px;" :style="{width : $vuetify.breakpoint.smAndDown ? '380px' : '550px'}">
                                        <v-form ref="installForm" @submit.prevent="submit" lazy-validation>
                                            <v-row dense>
                                                <v-col cols="12" class="d-flex justify-center mb-5">
                                                    <v-img src="/images/logo.png" max-height="60" max-width="60" />
                                                    <span class="brand ml-4" style="font-size: 40px">{{appName}}</span>
                                                </v-col>
                                                <v-col cols="8">
                                                    <v-text-field
                                                        dense
                                                        filled
                                                        color="yellow"
                                                        dark
                                                        label="Shop URL to install the app"
                                                        id="shopName"
                                                        :rules="[rules.required('shop')]"
                                                        :loading="busy"
                                                        name="shop"
                                                        v-model="shopName">
                                                    </v-text-field>
                                                </v-col>
                                                <v-col cols="4" class="d-flex justify-center">
                                                    <v-btn color="primary" large depressed height="55px" @click="install">Install</v-btn>
                                                </v-col>
                                            </v-row>
                                            <v-row>
                                                <v-col cols="12" class="d-flex justify-center">
                                                    <v-btn text color="black" :to="{name: 'guest.plans'}">Plans</v-btn>
                                                    <v-btn text color="black" target="_blank" href="https://eraofecom.org/pages/about-us">About Us</v-btn>
                                                    <v-btn text color="black" target="_blank" href="https://eraofecom.org/pages/contact-us">Contact Us</v-btn>
                                                </v-col>
                                            </v-row>
                                        </v-form>
                                    </div>
                                </v-col>
                                <v-col cols="12" sm="7" md="5" lg="5" class="d-flex justify-center align-self-center"
                                       v-bind:class="$vuetify.breakpoint.mdAndDown ? 'auth-mobile-pad' : ''">
                                    <v-divider class="mx-3 homeDivider" inset vertical dark v-if="$vuetify.breakpoint.mdAndUp"></v-divider>
                                    <transition name="fade" mode="out-in">
                                        <router-view></router-view>
                                    </transition>
                                </v-col>
                            </v-row>
                            <v-row
                                align="center"
                                class="white--text mx-auto"
                                justify="center"
                            >
                                <v-col
                                    class="white--text text-center"
                                    cols="12"
                                    tag="h1"
                                >
                                    <v-btn
                                        color="white"
                                        class="align-self-end"
                                        fab
                                        outlined
                                        @click="$vuetify.goTo('#about-me')"
                                    >
                                        <v-icon>mdi-chevron-double-down</v-icon>
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-img>
                </v-row>
            </section>

            <section id="about-me">
                <div class="py-12"></div>

                <v-container class="text-center">
                    <h2 class="display-2 font-weight-bold mb-3">ABOUT US</h2>

                    <v-responsive
                        class="mx-auto mb-8"
                        width="56"
                    >
                        <v-divider class="mb-1"></v-divider>

                        <v-divider></v-divider>
                    </v-responsive>

                    <v-responsive
                        class="mx-auto title font-weight-light mb-8"
                        max-width="720"
                    >
                        Vuetify is the #1 component library for Vue.js and has been in active development since 2016. The goal of the project is to provide users with everything that is needed to build rich and engaging web applications using the Material Design specification. It accomplishes that with a consistent update cycle, Long-term Support (LTS) for previous versions, responsive community engagement, a vast ecosystem of resources and a dedication to quality components.
                    </v-responsive>

                    <v-avatar
                        class="elevation-12 mb-12"
                        size="128"
                    >
                        <v-img src="https://cdn.vuetifyjs.com/images/john.png"></v-img>
                    </v-avatar>

                    <div></div>

                    <v-btn
                        color="grey"
                        href="https://vuetifyjs.com"
                        outlined
                        large
                    >
            <span class="grey--text text--darken-1 font-weight-bold">
              Vuetify Documentation
            </span>
                    </v-btn>
                </v-container>

                <div class="py-12"></div>
            </section>

            <section
                id="features"
                class="grey lighten-3"
            >
                <div class="py-12"></div>

                <v-container class="text-center">
                    <h2 class="display-2 font-weight-bold mb-3">VUETIFY FEATURES</h2>

                    <v-responsive
                        class="mx-auto mb-12"
                        width="56"
                    >
                        <v-divider class="mb-1"></v-divider>

                        <v-divider></v-divider>
                    </v-responsive>

                    <v-row>
                        <v-col
                            v-for="({ icon, title, text }, i) in features"
                            :key="i"
                            cols="12"
                            md="4"
                        >
                            <v-card
                                class="py-12 px-4"
                                color="grey lighten-5"
                                flat
                            >
                                <v-theme-provider dark>
                                    <div>
                                        <v-avatar
                                            color="primary"
                                            size="88"
                                        >
                                            <v-icon
                                                large
                                                v-text="icon"
                                            ></v-icon>
                                        </v-avatar>
                                    </div>
                                </v-theme-provider>

                                <v-card-title
                                    class="justify-center font-weight-black text-uppercase"
                                    v-text="title"
                                ></v-card-title>

                                <v-card-text
                                    class="subtitle-1"
                                    v-text="text"
                                >
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>

                <div class="py-12"></div>
            </section>

            <section id="stats">
                <v-parallax
                    :height="$vuetify.breakpoint.smAndDown ? 700 : 500"
                    src="https://townshipcanada.com/static/pattern.svg"
                >
                    <v-container fill-height>
                        <v-row class="mx-auto">
                            <v-col
                                v-for="[value, title] of stats"
                                :key="title"
                                cols="12"
                                md="3"
                            >
                                <div class="text-center">
                                    <div
                                        class="display-3 font-weight-black mb-4"
                                        v-text="value"
                                    ></div>

                                    <div
                                        class="title font-weight-regular text-uppercase"
                                        v-text="title"
                                    ></div>
                                </div>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-parallax>
            </section>

            <section id="blog">
                <div class="py-12"></div>

                <v-container>
                    <h2 class="display-2 font-weight-bold mb-3 text-uppercase text-center">Blog</h2>

                    <v-responsive
                        class="mx-auto mb-12"
                        width="56"
                    >
                        <v-divider class="mb-1"></v-divider>

                        <v-divider></v-divider>
                    </v-responsive>

                    <v-row>
                        <v-col
                            v-for="({ src, text, title }, i) in articles"
                            :key="i"
                            cols="12"
                            md="4"
                        >
                            <v-img
                                :src="src"
                                class="mb-4"
                                height="275"
                                max-width="100%"
                            ></v-img>

                            <h3
                                class="font-weight-black mb-4 text-uppercase"
                                v-text="title"
                            ></h3>

                            <div
                                class="title font-weight-light mb-5"
                                v-text="text"
                            ></div>

                            <v-btn
                                class="ml-n4 font-weight-black"
                                text
                            >
                                Continue Reading
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-container>

                <div class="py-12"></div>
            </section>

            <v-sheet
                id="contact"
                color="#333333"
                dark
                tag="section"
                tile
            >
                <div class="py-12"></div>
            </v-sheet>
        </v-main>

        <v-footer
            class="justify-center"
            color="#292929"
            height="100"
        >
            <div class="title font-weight-light grey--text text--lighten-1 text-center">
                &copy; {{ (new Date()).getFullYear() }} — Made with 💜 by {{appVendor}}
            </div>
        </v-footer>
    </div>
</template>

<script>
    import axios from 'axios'
    import {api} from '~/api'
    import Form from '~/mixins/form'

    export default {
        mixins: [Form],
        data: () => ({
            appName     : appSlug.appName,
            appVendor   : appSlug.appVendor,
            shopName    : null,
            apiKey      : appSlug.shopifyKey,
            busy        : false,
            articles: [
                {
                    src: 'https://images.unsplash.com/photo-1423784346385-c1d4dac9893a?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1950&q=80',
                    title: 'Mobile first & Responsive',
                    text: 'Phasellus lorem enim, luctus ut velit eget, convallis egestas eros. Sed ornare ligula eget tortor tempor, quis porta tellus dictum.',
                },
                {
                    src: 'https://images.unsplash.com/photo-1475938476802-32a7e851dad1?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1950&q=80',
                    title: 'Think outside the box',
                    text: 'Nam ut leo ipsum. Maecenas pretium aliquam feugiat. Aenean vel tempor est, vitae tincidunt risus. Sed sodales vestibulum nibh.',
                },
                {
                    src: 'https://images.unsplash.com/photo-1416339442236-8ceb164046f8?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1892&q=80',
                    title: 'Small changes, big difference',
                    text: 'Vestibulum in dictum velit, in rhoncus nibh. Maecenas neque libero, interdum a dignissim in, aliquet vitae lectus. Phasellus lorem enim, luctus ut velit eget.',
                },
            ],
            features: [
                {
                    icon: 'mdi-account-group-outline',
                    title: 'Vibrant Community',
                    text: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Iusto cupiditate sint possimus quidem atque harum excepturi nemo velit tempora! Enim inventore fuga, qui ipsum eveniet facilis obcaecati corrupti asperiores nam',
                },
                {
                    icon: 'mdi-update',
                    title: 'Frequent Updates',
                    text: 'Sed ut elementum justo. Suspendisse non justo enim. Vestibulum cursus mauris dui, a luctus ex blandit. Lorem ipsum dolor sit amet consectetur adipisicing elit. qui ipsum eveniet facilis obcaecati corrupti consectetur adipisicing elit.',
                },
                {
                    icon: 'mdi-shield-outline',
                    title: 'Long-term Support',
                    text: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Iusto cupiditate sint possimus quidem atque harum excepturi nemo velit tempora! Enim inventore fuga, qui ipsum eveniet facilis obcaecati corrupti asperiores nam',
                },
            ],
            stats: [
                ['24k', 'Github Stars'],
                ['330+', 'Releases'],
                ['1m', 'Downloads/mo'],
                ['5m', 'Total Downloads'],
            ]
        }),
        mounted() {

        },
        methods: {
            install: function(){
                let self = this;
                if (this.$refs.installForm.validate()) {
                    axios
                        .get(api.path('shopify.install', {'shop': self.shopName}))
                        .then(function(resp){
                            window.location.href = resp.data.url;
                        });
                }
            }
        }
    }
</script>
