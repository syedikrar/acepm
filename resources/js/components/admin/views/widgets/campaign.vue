<template>
    <v-card color="transparent" flat dense v-show="upsellPopup">
        <v-card-text class="pa-0">
            <v-toolbar flat class="br-4" color="warning" dark>
                <v-toolbar-title class="font-weight-light title">Your Chance To Grow</v-toolbar-title>
            </v-toolbar>
            <v-container class="py-0 px-0">
                <v-card class="mb-8 mt-0 elevation-16 mx-auto">
                    <v-card-text class="pa-0">
                        <v-container fluid class="py-2">
                            <v-row dense>
                                <v-col cols="12" md="3">
                                    <v-row dense class="d-flex align-content-center">
                                        <v-col cols="12">
                                            <p class="text-center ma-0 mt-n3">
                                                <img height="100%" width="100%" class="pt-4" v-bind:src="(campaign.type=='upsell') ?'/images/grow.jpg':'/images/review.jpg'">
                                            </p>
                                        </v-col>
                                        <v-col cols="12" class="title mt-2">
                                            <p class="text-center ma-0 mt-n1" v-text="campaign.type=='upsell' ?'You Are Growing':'Give Us a Review!'"></p>
                                        </v-col>
                                    </v-row>
                                </v-col>
                                <v-col cols="12" md="9" class="px-3" style="display: flex; flex-direction: column;">
                                    <v-row dense class="mt-n1">
                                        <v-col cols="12">
                                            <p class="text-center ma-0 subtitle-1"> {{campaign.message}} </p>
                                        </v-col>
                                    </v-row>
                                    <v-row dense class="mt-3">
                                        <v-col cols="12" class="d-flex">
                                            <v-btn text class="mx-3 ml-auto" @click="manageActions(true, false)">Maybe Later</v-btn>
                                            <v-btn color="pink darken-1" depressed dark @click="manageActions(true, true)"
                                                   v-text="campaign.type=='upsell' ?'Let\'s upgrade':'Yes, Sure !'">
                                            </v-btn>
                                        </v-col>
                                    </v-row>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-container>
        </v-card-text>
    </v-card>
</template>


<script>
    import Helper from "../../../../helpers/Helper";

    export default {
        name: 'campaign',
        data() {
            return {
                upsellPopup     : false,
                skip : false,
                campaign        : {
                    title: '',
                    message: '',
                    type: ''
                },
                module: '',
                reviewPage  : 'https://apps.shopify.com/ace3-in-1#reviews'
            }
        },
        mounted(){
            let self = this;
            if(self.auth.role != 'owner') return;

            setTimeout(function () {
                self.initCampaign();
            },6000);
        },
        methods: {
            initCampaign: function () {
                let self = this;
                axios.get(api.path('upsellCampaign.active')).then(function(response){
                    let campaign = response.data.campaign;
                    if(campaign != null && moment(campaign.campaign_starts) <= moment()) {
                        if(self.accepted(campaign) || (campaign.type == 'upsell' && self.billing.plan == 'enterprise')) return;
                        self.campaign = campaign;
                        self.createPopup();
                    }
                });
            },
            createPopup: function(){
                let self = this;
                let plan = self.billing.plan.toUpperCase();
                //Preparing upsell message, replacing the wildcards
                let obj = {
                    '{current_plan}'   : (plan == 'BASIC') ? 'FREEMIUM' : plan,
                    '{higher_plan}'    : Helper.getHigherPlan(plan),
                    '{days_installed}' : Helper.daysInstalled(self.user)
                };
                self.campaign.message = Helper.replaceAll(self.campaign.message, obj);
                self.manageActions();
            },
            manageActions: function(interacted = false, accepted = false) {
                let self = this;
                let cookie = Helper.readCookie(self.campaign.title+'-'+self.campaign.id+'-'+self.shop, true);
                //Keeping track of how many times user has seen the campaign
                let count = (cookie != null) ? parseInt(cookie.count) : 0;

                if(!interacted){
                    //Keeping track of days last time upsell was shown
                    let lastShownDayCount = (cookie != null) ?
                        moment().diff(moment(cookie.lastShown).startOf('day'), 'days') : 0;
                    //Condition that runs based on configurations set by CS guy
                    if(cookie == null ||
                        (cookie != null && !cookie.done &&
                            count < self.campaign.max_tries &&
                            lastShownDayCount >= parseInt(self.campaign.repeat_after))
                    ){self.upsellPopup = true; self.persist('impressions');}
                }else{
                    self.upsellPopup = false;
                    if(self.skip && !accepted) {return;}

                    //Populating pixel for audience creation, based on user action
                    let obj = JSON.stringify({count : ++count, lastShown: moment(), done: accepted});
                    //Write cookie that expires when campaign expires
                    let expiry = moment(self.campaign.campaign_expires).diff(moment().startOf('day'), 'days');
                    Helper.writeCookie(self.campaign.title+'-'+self.campaign.id+'-'+self.shop, obj, expiry, true);
                    if(accepted) self.persist('conversions', accepted);
                }

            },
            persist: function (event, accepted = false) {
                let self = this;
                axios.post(api.path('upsellCampaign.stats'), {campaign_id: self.campaign.id, 'event': event}).then(function (res) {
                    if(accepted) self.done();
                }).catch(function (err) {
                    if(accepted) self.done();
                })
            },
            done: function(){
                let self = this;
                if(self.campaign.type == 'upsell'){this.$router.push({name: plans})}
                else{ self.requestCoupon(); window.open(self.reviewPage); }
            },
            requestCoupon: function () {
                let self = this;
                axios.patch(api.path('discountCoupon.save'), {decision: 'requested'}).then(function (res) {
                    self.showSnack('Coupon request submitted');
                }).catch(function (err) {

                })
            },
            accepted: function (campaign) {
                let cookie = Helper.readCookie(campaign.title+'-'+campaign.id+'-'+self.shop, true);
                return (cookie != null && cookie.done) ? true : false;
            }
        }
    }
</script>
