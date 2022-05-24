<template>
    <v-card flat>
        <v-row flat>
            <v-col class="text-h4 pt-6 pl-10" cols="12">
                Gigs as Seller
            </v-col>
            <!--            <v-spacer></v-spacer>-->
            <!--            <v-col cols="4">-->
            <!--                <v-autocomplete-->
            <!--                        v-model="search"-->
            <!--                        :items="items"-->
            <!--                        outlined-->
            <!--                ></v-autocomplete>-->
            <!--            </v-col>-->
        </v-row>
        <v-card-text>
            <v-row align="center"
                   class="mx-0">
                <v-col
                        md="4"
                        v-for="(gig,g) in gigs"
                        :key="g"
                >
                    <v-card
                            :loading="loading"
                            class="mx-auto"
                            max-width="374"
                    >
                        <template slot="progress">
                            <v-progress-linear
                                    color="deep-purple"
                                    height="10"
                                    indeterminate
                            ></v-progress-linear>
                        </template>

                        <v-img
                                height="250"
                                :src="gig.galleries[0] ? '/images/gigs/'+gig.galleries[0].image : '/images/grow.jpg'"
                        ></v-img>

                        <v-card-title>{{ gig.title }}</v-card-title>

                        <v-card-text>
                            <v-row
                                    align="center"
                                    class="mx-0"
                            >
                                <v-rating
                                        :value="3.5"
                                        color="amber"
                                        dense
                                        half-increments
                                        readonly
                                        size="14"
                                ></v-rating>

                                <div class="grey--text ml-4">
                                    3.5 (17)
                                </div>
                            </v-row>

                            <div class="my-4 subtitle-1">
                                {{ gig.category.title }}, {{ gig.sub_category.title }}
                            </div>

                            <div class="my-4 subtitle-1">
                                Price start: ${{ gig.packages[0]['price'] }}
                            </div>
                        </v-card-text>

                        <v-divider class="mx-4"></v-divider>

                        <v-card-actions>
                            <v-btn
                                    color="deep-purple "
                                    :to="{name: 'seller.gigs.single',  params: {id: gig.id}}"
                            >
                                View Gig
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
</template>

<script>
    export default {
        name: "listing",
        data() {
            return {
                loading: false,
                gigs: [],
                items: ['Most Recent','Negative Reviews','Positive Reviews'],
                search: 'Most Recent'
            }
        },
        created() {
            this.getGigs();
        },
        methods: {
            getGigs: function() {
                let self = this;
                axios.get(api.path('gig.all'))
                    .then((response)=>{
                        self.gigs = response.data;
                    });
            }
        }
    }
</script>
