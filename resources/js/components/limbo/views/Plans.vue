<template>
    <v-container fluid class="plans">
        <v-layout class="limboTop" v-if="!authCheck">
            <v-row>
                <v-col cols="12" class="d-flex justify-center">
                    <router-link to="/" class="d-flex justify-center text-decoration-none">
                        <v-img src="/images/logo.png" :width="80" :max-width="80" />
                        <span class="brand ml-4">{{appName}}</span>
                    </router-link>
                </v-col>
            </v-row>
        </v-layout>
        <v-layout>
            <v-row dense>
                <v-col cols="12" class="d-flex justify-center info--text text-subtitle-1">
                    Paid plan includes 20 days of free trial
                </v-col>
                <v-col v-if="showCouponField" cols="12" class="d-flex justify-center attention--text text-subtitle-1">
                    Give us a review and get a free discount coupon for paid plan!
                </v-col>
                <v-col v-if="showCouponField" offset="4" cols="4" lg-4 md-6 sm-12 class="d-flex justify-center white--text text-subtitle-1">
                    <v-text-field
                            v-model="couponCode"
                            placeholder="Discount Code"
                            outlined
                            class="fsb-textfield"
                            dark
                    ></v-text-field>
                    <v-btn style="height: 64%; font-size: 16px;" width="auto" class="ml-2 primary" @click="checkCoupon()">Apply</v-btn>
                </v-col>

                <v-col cols="12" class="d-flex justify-center mb-3">
                    <hr style="width: 70%" />
                </v-col>
            </v-row>
        </v-layout>
        <div class="background">
            <div class="container">
                <v-progress-linear
                        :active="busy"
                        :indeterminate="busy"
                        color="info"
                        indeterminate
                        rounded
                        height="6"
                ></v-progress-linear>
                <div class="panel pricing-table">
                    <v-card flat class="pricing-plan">
                        <img src="/images/basic-plan.png" alt="" class="pricing-img">
                        <h2 class="pricing-header attention--text">Starter</h2>
                        <ul class="pricing-features">
                            <li class="pricing-features-item">Thorray Features</li>
                            <li class="pricing-features-item">Sassta aur Myaari Plan</li>
                            <li class="pricing-features-item">Perfect for Ghareeb awaam</li>
                        </ul>
                        <span class="pricing-price">Free</span>
                        <v-btn x-large outlined color="primary"
                               depressed class="mt-5"
                               :loading="busy"
                               :disabled="busy"
                               @click="billingSetup('basic')">Sign up</v-btn>
                    </v-card>

                    <v-card flat class="pricing-plan">
                        <img src="/images/pro-plan.png" alt="" class="pricing-img">
                        <h2 class="pricing-header attention--text">Enterprise</h2>
                        <ul class="pricing-features">
                            <li class="pricing-features-item">Ziada Features</li>
                            <li class="pricing-features-item">For People with haram ka Paisa</li>
                            <li class="pricing-features-item">Kharcha Kersos</li>
                            <li class="pricing-features-item">Ye Mai Hu</li>
                            <li class="pricing-features-item">Ye Maira Plan Hi</li>
                            <li class="pricing-features-item">Aur yaha Pawri ho ri hai</li>
                        </ul>
                        <div v-show="couponValidated" class="old-price">
                            <span style="margin-right: 5px">was</span>
                            <span>$</span>
                            <span>{{defaultPrice}}</span>
                            <span>/month</span>
                        </div>
                        <span :class="(couponValidated)? 'pricing-price green--text text--accent-4':'pricing-price'">$ {{(couponValidated) ? discountedPrice : defaultPrice}}</span>
                        <span v-show="couponValidated" class="percentage-off">{{discountPercentage}}%off</span>
                        <v-btn x-large outlined color="primary"
                               depressed class="mt-5"
                               :loading="busy"
                               :disabled="busy"
                               @click="billingSetup('enterprise')">Free Trial</v-btn>
                    </v-card>

                </div>
            </div>
        </div>
    </v-container>
</template>

<script>

    import axios from "axios"
    import {api} from '~/api'

    export default {
        data: () => ({
            busy: false,
            defaultPrice: 9.95,
            discountedPrice: 0.00,
            couponCode: '',
            couponValidated: false,
            discountPercentage: 0,
            showCouponField: false,
            appName : appSlug.appName
        }),
        methods: {
            billingSetup: function(plan){
                let self = this;
                if (!self.authCheck) {
                    self.$router.push('/');
                    return true;
                }

                self.busy = true;
                axios
                    .get(api.path('shopify.planSelected', {plan : plan})+'?couponCode='+self.couponCode.trim())
                    .then(function (response) {

                        if (response.data.status == 'charged') location.reload();
                        else if (response.data.redirect != null) window.location = response.data.redirect;
                    });
            },
            checkCoupon: function() {
                let self = this;
                if (!self.authCheck) {
                    self.$router.push('/');
                    return true;
                }

                if(self.couponCode.trim() == "") return;
                self.busy = true;

                axios
                    .post(api.path('discountCoupon.check', {code : self.couponCode.trim()}))
                    .then(function (response) {
                        if(response.data.coupon != null) self.applyCoupon(response.data.coupon);
                        else self.removeCoupon();
                        self.busy=false;
                    }).catch(function (error) {
                    console.info('%c --------------','color: #bada55', {error});
                    self.busy = false;
                });

            },
            applyCoupon: function(coupon) {
                let self= this;
                self.couponValidated = true;
                self.discountedPrice =  (self.defaultPrice - ((coupon.percentage/100) * self.defaultPrice)).toFixed(2);
                self.discountPercentage = coupon.percentage;
                window.scrollBy(0, 600);
            },

            removeCoupon: function(){
                let self = this;

                self.couponValidated = false;
                self.discountedPrice = 0.00;
                self.discountPercentage = 0;
                alert('Invalid coupon code!');
            }
        },
        mounted() {
            let self = this;
            if (!self.authCheck) {
                document.querySelector('.container.plans').classList.add('fullBG');
            }

            self.showCouponField = (self.billing.plan != 'enterprise') ? true : false;
        }
    }
</script>


