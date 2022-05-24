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

                            <v-row dense>
                                <v-col cols="12">
                                    <count-down-timer v-if="sendSearch != ''" v-bind:searchKey="sendSearch"></count-down-timer>
                                </v-col>
                            </v-row>
                            <v-row dense>
                                <v-col cols="12">
                                    <money-bar v-if="sendSearch != ''" v-bind:searchKey="sendSearch"></money-bar>
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12">
                                    <sticky-cart v-if="sendSearch != ''" v-bind:searchKey="sendSearch"></sticky-cart>
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12" sm="6">
                                    <integrity v-if="sendSearch != ''" v-bind:searchKey="sendSearch"></integrity>
                                </v-col>
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
    import CountDownTimer from "./countDownTimer/listing";
    import MoneyBar from "./moneyBar/listing";
    import StickyCart from "./stickyCart/listing";
    import DiscountCoupons from "./discountCoupons";
    import Integrity from "./integrity";
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
            Integrity,
            DiscountCoupons,
            StickyCart,
            CountDownTimer,
            MoneyBar,
            UpsellCampaign
        },
        methods: {
            getStoreData(){
                this.sendSearch = this.search;
            }
        }
    }


</script>
