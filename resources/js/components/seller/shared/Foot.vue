<template>
    <v-footer
        id="dashboard-core-footer"
        absolute
    >
        <v-container>
            <v-row
                align="center"
                no-gutters
            >
                <v-col
                    v-if="$vuetify.breakpoint.mdAndUp"
                    v-for="(link, i) in links"
                    :key="i"
                    class="text-center mb-sm-0 mb-5"
                    cols="auto"
                >
                    <a
                        :href="link.href"
                        class="mr-0 grey--text text--darken-3"
                        rel="noopener"
                        target="_blank"
                        v-text="link.text"
                    />
                </v-col>

                <v-spacer class="hidden-sm-and-down"/>

                <v-col
                    cols="12"
                    md="auto"
                >
                    <div class="body-1 font-weight-light pt-6 pt-md-0 text-center">
                        &copy; {{moment().year()}}, made with
                        <v-icon size="18" color="red">
                            mdi-heart
                        </v-icon>
                        by <a href="https://eraofecom.org" target="_blank">Era of Ecom</a>.
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </v-footer>
</template>

<script>
    export default {
        name: 'Foot',
        data: () => ({
            moment: window.moment,
            links: [
                {
                    href: 'https://eraofecom.org/pages/about-us',
                    text: 'About Us',
                },
                {
                    href: 'https://eraofecom.org/pages/contact-us',
                    text: 'Contact Us',
                },
                {
                    href: 'https://help.eraofecom.org/en/collections/2749250-ace-3-in-1',
                    text: 'FAQs'
                }
            ],
        }),
    }
</script>
