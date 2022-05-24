<template>
    <v-card
            flat
            class="mx-auto mr-4 my-12"
    >
        <v-row flat>
            <v-col class="text-h4 pt-6 pl-10" cols="8">
                Reviews
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="4">
                <v-autocomplete
                        v-model="search"
                        :items="items"
                        outlined
                ></v-autocomplete>
            </v-col>
        </v-row>
        <v-card-text>
            <v-row
                    align="center"
                    class="mx-0"
            >
                <v-card-title>
                    <v-avatar size="40">
                        <img
                                alt="user"
                                src="/images/caspian.jpg"
                        >
                    </v-avatar>
                    <p class="mt-3 ml-3">
                        {{capitalizeFirstLetter(auth.name)}}
                    </p>
                </v-card-title>
                <v-rating
                        :value="3.5"
                        color="amber"
                        dense
                        half-increments
                        readonly
                        size="14"
                ></v-rating>
                <div class="grey--text ml-1">3.5</div>
            </v-row>

            <div class="ml-16">
                <div class="subtitle-4 mx-auto">
                    <v-img class="mr-4" lazy-src="https://api.backendless.com/2F26DFBF-433C-51CC-FF56-830CEA93BF00/473FB5A9-D20E-8D3E-FF01-E93D9D780A00/files/CountryFlagsPng/pak.png"
                           max-height="20"
                           max-width="35"
                           color="primary"
                           title="Pakistan"
                           src="https://api.backendless.com/2F26DFBF-433C-51CC-FF56-830CEA93BF00/473FB5A9-D20E-8D3E-FF01-E93D9D780A00/files/CountryFlagsPng/pak.png"> </v-img>
                </div>
                <div class="mt-4 mb-2">
                    Small plates, salads & sandwiches - an intimate setting with 12 indoor seats plus patio seating.
                    Small plates, salads & sandwiches - an intimate setting with 12 indoor seats plus patio seating.
                    Small plates, salads & sandwiches - an intimate setting with 12 indoor seats plus patio seating.
                </div>
                <div class="grey--text subtitle-4">
                    Published {{ago(auth.updated_at)}}
                </div>
            </div>
        </v-card-text>
        <v-card-text>
            <v-row
                    align="center"
                    class="mx-0"
            >
                <v-card-title>
                    <v-avatar size="40">
                        <img
                                alt="user"
                                src="/images/globe.png"
                        >
                    </v-avatar>
                    <p class="mt-3 ml-3">
                        {{capitalizeFirstLetter('John')}}
                    </p>
                </v-card-title>
                <v-rating
                        :value="5"
                        color="amber"
                        dense
                        half-increments
                        readonly
                        size="14"
                ></v-rating>
                <div class="grey--text ml-1">5</div>
            </v-row>
            <div class="ml-16">
                <div class="subtitle-4 mx-auto">
                    <v-img class="mr-4" lazy-src="https://api.backendless.com/2F26DFBF-433C-51CC-FF56-830CEA93BF00/473FB5A9-D20E-8D3E-FF01-E93D9D780A00/files/CountryFlagsPng/phl.png"
                           max-height="20"
                           max-width="35"
                           color="primary"
                           title="Philippines"
                           src="https://api.backendless.com/2F26DFBF-433C-51CC-FF56-830CEA93BF00/473FB5A9-D20E-8D3E-FF01-E93D9D780A00/files/CountryFlagsPng/phl.png"> </v-img>

                </div>
                <div class="mt-4 mb-2">
                    Small plates, salads & sandwiches - an intimate setting with 12 indoor seats plus patio seating.
                    Small plates, salads & sandwiches - an intimate setting with 12 indoor seats plus patio seating.
                </div>
                <div class="grey--text subtitle-4">
                    Published {{ago(auth.updated_at)}}
                </div>
            </div>
        </v-card-text>
        <v-card-text>
            <v-row
                    align="center"
                    class="mx-0"
            >
                <v-card-title>
                    <v-avatar size="40">
                        <img
                                alt="user"
                                src="/images/banner.jpg"
                        >
                    </v-avatar>
                    <p class="mt-3 ml-3">
                        {{capitalizeFirstLetter('Yoyo')}}
                    </p>
                </v-card-title>
                <v-rating
                        :value="5"
                        color="amber"
                        dense
                        half-increments
                        readonly
                        size="14"
                ></v-rating>
                <div class="grey--text ml-1">5</div>
            </v-row>
            <div class="ml-16">
                <div class="subtitle-4 mx-auto">
                    <v-img class="mr-4" lazy-src="https://api.backendless.com/2F26DFBF-433C-51CC-FF56-830CEA93BF00/473FB5A9-D20E-8D3E-FF01-E93D9D780A00/files/CountryFlagsPng/sau.png"
                           max-height="20"
                           max-width="35"
                           color="primary"
                           title="Saudi Arabia"
                           src="https://api.backendless.com/2F26DFBF-433C-51CC-FF56-830CEA93BF00/473FB5A9-D20E-8D3E-FF01-E93D9D780A00/files/CountryFlagsPng/sau.png"> </v-img>
                </div>
                <div class="mt-4 mb-2">
                    Small plates, salads & sandwiches - an intimate setting with 12 indoor seats plus patio seating.
                    Small plates, salads & sandwiches - an intimate setting with 12 indoor seats plus patio seating.
                    Small plates, salads & sandwiches - an intimate setting with 12 indoor seats plus patio seating.
                    Small plates, salads & sandwiches - an intimate setting with 12 indoor seats plus patio seating.
                    Small plates, salads & sandwiches - an intimate setting with 12 indoor seats plus patio seating.
                </div>
                <div class="grey--text subtitle-4">
                    Published {{ago(auth.updated_at)}}
                </div>
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
    import {mapGetters} from 'vuex'
    export default {
        name: "Reveiws",
        data: () => ({
            name: null,
            email : null,
            loading: false,
            items: ['Most Recent','Negative Reviews','Positive Reviews'],
            search: 'Most Recent'
        }),
        computed: mapGetters({
            auth: 'auth/user'
        }),
        methods: {
            reserve() {
                this.loading = true,
                    setTimeout(() => (this.loading = false), 2000);
            }
        },
    }
</script>

<style scoped>

</style>