<template>
    <v-container fluid>
        <v-row dense>
            <v-col cols="12">
                <v-subheader class="px-0 py-2 display-1 tx-headline">
                    <v-icon large class="blue--text">mdi-headset</v-icon> Customer Support
                    <v-spacer></v-spacer>
                    <v-avatar class="mr-2" :size="35">
                        <img src="/images/caspian.jpg" alt="Caspian">
                    </v-avatar>
                </v-subheader>
            </v-col>
            <v-col cols="12">
                <v-card flat color="transparent">
                    <v-card-text class="basicSettings px-0 pt-1">
                        <p class="font-weight-medium mb-0">The Swiss army knife !!!</p>

                        <v-container class="px-0" fluid>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                            v-model="search"
                                            append-icon="mdi-search-web"
                                            label="Search by Shop Name"
                                            hide-details
                                            outlined
                                            @change="getStoreData"
                                    ></v-text-field>
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12" sm="6">
                                    <shop-info v-if="sendSearch != ''" v-bind:searchKey="sendSearch"></shop-info>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12">
                                    <discount-coupons></discount-coupons>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12">
                                    <upsell-campaign></upsell-campaign>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    import DiscountCoupons from "./discountCoupons";
    import ShopInfo from "./shopInfo";
    import UpsellCampaign from "./upsellCampaign";

    export default {
        data() {
            return {
                search      : '',
                sendSearch  : '',
            };
        },
        components: {
            ShopInfo,
            DiscountCoupons,
            UpsellCampaign
        },
        methods: {
            getStoreData(){
                this.sendSearch = this.search;
            }
        }
    }


</script>
