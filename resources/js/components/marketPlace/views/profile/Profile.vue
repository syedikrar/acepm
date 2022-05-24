<template>
    <v-card flat class="mr-4">
        <v-parallax
                dark
                height="250"
                src="https://cdn.vuetifyjs.com/images/backgrounds/vbanner.jpg"
        >
            <v-row class="mt-16">
                <v-col  cols="8">
                    <v-card-title class="white--text">
                        <v-avatar size="56">
                            <img
                                    alt="user"
                                    src="/images/caspian.jpg"
                            >
                        </v-avatar>
                        <p class="ml-3 mt-6">
                            {{capitalizeFirstLetter(auth.name)}}
                        </p>
                    </v-card-title>
                    <v-card-text>
                        <v-row
                                align="center"
                                class="mx-0 mt-4"
                        >
                            <v-rating
                                    :value="4.5"
                                    color="amber"
                                    dense
                                    half-increments
                                    readonly
                                    size="18"
                            ></v-rating>

                            <div class="amber--text ml-4">
                                4.5 (77)
                            </div>
                        </v-row>

                        <div class="my-4 subtitle-1">
                            Level 1 Seller
                        </div>
                    </v-card-text>
                </v-col>
                <v-col cols="4">
                    <v-list style="border-radius:10px; background: rgb(255 255 255 / 40%);">
                        <v-list-item-group>
                            <v-list-item>
                                <v-list-item-icon class="mr-0">
                                    <v-icon color="blue darken-1" v-text="'mdi-map-marker-outline'"></v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title >From: <b>{{ (user.country != null) ? user.country: 'N/A'}}</b>
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-icon class="mr-0">
                                    <v-icon color="orange" v-text="'mdi-account-circle-outline'"></v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>Member Since: <b>{{ago(user.created_at)}}</b>
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-icon class="mr-0">
                                    <v-icon color="pink accent-3" v-text="'mdi-truck-delivery-outline'"></v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title >Last Delivery: <b>{{ (user.updated_at != null) ? ago(user.updated_at) : 'N/A'}}</b>
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list-item-group>
                    </v-list>
                </v-col>
            </v-row>
        </v-parallax>
        <v-row>
            <v-col cols="4">
                <PersonalDetails></PersonalDetails>
            </v-col>
            <v-col cols="8">
                <Listing v-bind:userEmail="user.email"></Listing>
                <Reviews></Reviews>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
    import PersonalDetails from './PersonalDetails'
    import Reviews from './Reviews'
    import Listing from "../views/gigs/Listing";

    export default {
        name: "Profile",
        components: {
            Listing,
            PersonalDetails,
            Reviews
        },
        methods: {

        },
        mounted() {
            let self = this;

        }
    }
</script>