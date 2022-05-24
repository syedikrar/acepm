<template>
    <v-card flat class="px-8">
        <v-row flat>
            <v-col class="text-h6 pb-0" cols="8">
                Find the best services from around the world, anytime
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="4" class="pb-0">
                <v-text-field
                        v-model="search"
                        type="text"
                        placeholder="Search"
                        label="Search Services"
                        outlined
                ></v-text-field>
            </v-col>
            <v-col class="pt-0" cols="12">
                <v-card v-if="category.gigs.length > 0" flat v-for="(category,index) in categories" :key="index">
                    <v-row flat>
                        <v-col class="text-h6" cols="12">
                            {{category.title}}
                        </v-col>
                    </v-row>
                    <v-card-text class="pa-0">
                        <v-row align="center"
                               class="mx-0">
                            <v-col
                                    md="3"
                                    v-for="(gig,g) in category.gigs"
                                    :key="g"
                            >
                                <v-card
                                        :loading="loading"
                                        class="mx-auto"
                                        max-width="374"
                                        min-height="496"
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
                                                :to="{name: 'marketPlace.gigs.single',  params: {id: gig.id}}"
                                        >
                                            View Gig
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
    import BestSellingGigs from "../widgets/bestSellingGigs";
    import BestSellers from "../widgets/bestSellers";

    export default {
        name: "listing",
        data() {
            return {
                loading: false,
                allCategories: [],
                categories: [],
                search: ''
            }
        },
        components: {
            BestSellingGigs,
            BestSellers
        },
        created() {
            this.getCategories();
        },
        methods: {
            getCategories: function() {
                let self = this;
                axios.get(api.path('gig.categories')+'?gigs=true')
                    .then((response)=>{
                        self.categories = response.data;
                        self.allCategories = response.data;
                    });
            },
        }
    }
</script>
