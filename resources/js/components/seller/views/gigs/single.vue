<template>
    <v-card flat>
        <v-row flat>
            <v-col class="text-h4 pt-6 pl-10" cols="12">
                {{ gig.title }}
            </v-col>
        </v-row>
        <v-card-text class="mt-4">
            <v-row class="mx-0 mb-4">

                <v-col cols="8">
                    <v-card-title class="">
                        <v-avatar size="56">
                            <img
                                alt="user"
                                src="/images/caspian.jpg"
                            >
                        </v-avatar>
                        <p class="ml-2 mt-6">
                            {{capitalizeFirstLetter(gig.user.name)}}
                            &nbsp; |
                        </p>

                        <div class="ml-3 mt-2">
                            <v-rating
                                :value="4.5"
                                color="amber"
                                dense
                                half-increments
                                readonly
                                size="18"
                                class="d-inline-block"
                            ></v-rating>

                            <span class="amber--text ml-4">
                                4.5 (77)
                            </span>
                        </div>
                    </v-card-title>

                    <v-carousel>
                        <v-carousel-item
                            v-for="(image,i) in gig.galleries"
                            :key="i"
                            :src="'/images/gigs/'+image.image"
                            reverse-transition="fade-transition"
                            transition="fade-transition"
                        ></v-carousel-item>
                    </v-carousel>

                    <div class="mt-5" >
                        <h1 >About This Gig</h1>
                        <div class="mt-3" v-html="gig.descriptions"></div>
                    </div>

                    <v-divider dark inset></v-divider>
                    <br>
                    {{ gig.category.title }} -> {{ gig.sub_category.title }}

                    <div class="mt-5">
                        <h1>Compare Packages</h1>
                        <v-simple-table dense class="mt-4">
                            <template v-slot:default>
                                <thead>
                                <tr class="grey lighten-4">
                                    <th class="pa-md-4 py-md-6 text-left" width="185px">

                                    </th>
                                    <th class="pa-md-4 py-md-6 text-left" v-for="(pkg,p) in gig.packages">
                                        <h2>${{ pkg.price }}</h2>
                                        <h3>{{ pkg.title }}</h3>
                                        <p class="mt-3">{{ pkg.details }}</p>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td width="185px" class="pa-md-4">Delivery Time</td>
                                    <td class="pa-md-4" align="center" v-for="(pkg,p) in gig.packages" :key="p">
                                        <p>{{ pkg.delivery }} days</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="185px" class="pa-md-4">
                                        Revisions
                                    </td>
                                    <td class="py-md-4" align="center" v-for="(pkg,p) in gig.packages" :key="p">
                                        <p>{{ pkg.revisions }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="185px" class="pa-md-4">
                                        Price
                                    </td>
                                    <td class="py-md-4" align="center" v-for="(pkg,p) in gig.packages" :key="p">
                                        <h3><strong>${{ pkg.price }}</strong></h3>
                                        <v-btn class="mt-3" depressed
                                               color="primary">Select</v-btn>
                                    </td>
                                </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </div>

                    <Reviews></Reviews>

                </v-col>

                <v-col cols="4">

                    <v-card
                        elevation="1"
                    >
                        <v-tabs v-model="tab">
                            <v-tab v-for="(pkg,p) in gig.packages" :key="p">{{ pkg.title }}</v-tab>
                        </v-tabs>

                        <v-tabs-items v-model="tab">
                            <v-tab-item
                                v-for="(pkg,p) in gig.packages"
                                :key="p"
                            >
                                <v-card
                                    color="basil"
                                    flat
                                >
                                    <v-card-text>
                                        <h3 align="right">${{ pkg.price }}</h3>
                                        <p class="mt-4">
                                            {{ pkg.details }}
                                        </p>
                                        <div>
                                            <v-icon
                                                left
                                            >
                                                mdi-alarm-check
                                            </v-icon> {{ pkg.delivery }} Day Delivery
                                            &nbsp;&nbsp;
                                            <v-icon
                                                left
                                            >
                                                mdi-reload
                                            </v-icon> {{ pkg.revisions }} Revision

                                        </div>
                                        <div align="center" v-if="user.role == 'owner'">
                                            <v-btn class="mt-3" :to="{name: 'gigs.order',  params: {id: gig.id, package: pkg.id}}" block depressed
                                                   color="success">Continue (${{ pkg.price }})</v-btn>
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </v-tab-item>
                        </v-tabs-items>

                    </v-card>
                </v-col>

            </v-row>
        </v-card-text>
    </v-card>
</template>

<script>
    import Reviews from "../../profile/Reviews";
    export default {
        name: "single",
        data(){
            return {
                gig         : null,
                tab: null,
            }
        },
        components: {
            Reviews
        },
        created() {
            this.getGig();
        },
        methods: {
            getGig: function() {
                let self = this;
                axios.get(api.path('gig.single', {id: self.$route.params.id}))
                    .then((response)=>{
                        self.gig = response.data;
                    });
            }
        }

    }
</script>

<style scoped>

</style>
