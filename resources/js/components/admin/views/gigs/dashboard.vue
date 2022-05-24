<template>
    <div id="gigs">
        <v-container class="grey lighten-5" fluid>
            <v-row>
                <v-col>
                    <v-btn
                        depressed
                        color="primary"
                        :to="{name: 'gigs.new'}"
                    >
                        Create A New Gig
                    </v-btn>
                </v-col>
            </v-row>
            <v-row
                justify="center"
            >
                <v-col
                    md="2"
                    v-for="(gig,g) in gigs"
                    :key="g"
                >
                    <v-card
                        :loading="loading"
                        class="mx-auto my-12"
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
                            :src="'/images/gigs/'+gig.galleries[0].image"
                        ></v-img>

                        <v-card-title>{{ gig.title }}</v-card-title>

                        <v-card-text>
                            <v-row
                                align="center"
                                class="mx-0"
                            >
                                <v-rating
                                    :value="4.5"
                                    color="amber"
                                    dense
                                    half-increments
                                    readonly
                                    size="14"
                                ></v-rating>

                                <div class="grey--text ml-4">
                                    4.5 (413)
                                </div>
                            </v-row>

                            <div class="my-4 subtitle-1">
                                {{ gig.category.title }} > {{ gig.sub_category.title }}
                            </div>

                            <div class="my-4 subtitle-1">
                                Price start: ${{ gig.packages[0].price }}
                            </div>


                        </v-card-text>

                        <v-divider class="mx-4"></v-divider>

                        <v-card-actions>
                            <v-btn
                                color="deep-purple "
                            >
                                View Gig
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
    export default {
        name: "dashboard",
        data() {
            return {
                gigs: []
            }
        },
        created() {
            this.getGigs();
        },
        methods: {
            getGigs: function() {
                let _this = this;
                axios.get(api.path('gig.all'))
                    .then((response)=>{
                        _this.gigs = response.data;
                    });
            }

        }
    }
</script>

<style scoped>

</style>
