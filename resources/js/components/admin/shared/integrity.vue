<template>
    <div v-show="auth.role == 'owner'" class="integrity-wrap integrity" v-bind:style="{ left: positionLeft, right: positionRight }">
        <v-tooltip
            :right="!$vuetify.rtl"
            :left="$vuetify.rtl"
            color="base" v-model="show">
            <template v-slot:activator="{ on }">
                <div class="spinner-wrap" v-on="on">
                    <span class="spinner-glow spinner-glow-primary"></span>
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
            </template>
            <span>{{status}}</span>
        </v-tooltip>
    </div>
</template>


<script>
    import HookIntegrity from "../../../helpers/HookIntegrity";

    export default {
        name: 'integrity',
        data() {
            return {
                show: true,
                status: 'App health check started ...',
                integrity: new HookIntegrity(),
                checkoutInfo: this.$t('integrity.not_found1') + "checkout.liquid" + ', ' + this.$t('integrity.not_found2')
            }
        },
        mounted(){
            let self = this;
            if(self.auth.role != 'owner') {self.show = false; return;}

            self.integrity.check(function(response){
                self.handleCheckResponse(response);
            });
        },
        computed: {
            positionLeft(){
                return this.$vuetify.rtl ? 'auto' : '110px';
            },
            positionRight(){
                return !this.$vuetify.rtl ? 'auto' :'110px';
            }
        },
        methods: {
            handleCheckResponse(response){
                let self = this;
                let isPlus = response.plan == 'shopify_plus';

                if (
                    response.theme == false ||
                    (isPlus && response.checkout == false) ||
                    response.snippet == false
                ) {
                    self.status = 'Performing optimization steps ...';

                    self.integrity.fix(response, function(fixResp){
                        self.handleFixResponse(fixResp);
                    });
                    return true;
                }

                if (isPlus && response.checkout == null) {
                    self.status = 'App health check finished.';
                    self.done();
                } else {
                    self.status = 'App health check finished.';
                    self.done();
                }

                if (response.theme == null) {
                    self.status = self.$t('integrity.error');
                }

            },
            handleFixResponse(response){
                let self = this;

                // ----- basic plan, integrity done
                if (response.plan != 'shopify_plus' && response.theme == true) {
                    self.status = appSlug.appName + ' health check finished.';
                    self.done();
                }

                // ----- plus plan, checkout not found.
                if (response.plan == 'shopify_plus' && response.theme == true && response.checkout == false) {
                    self.status = appSlug.appName + self.$t('integrity.finished') + self.checkoutInfo;
                    self.done();
                }

                if (response.plan == 'shopify_plus' && response.theme == true && response.checkout == true) {
                    self.status = 'App health check finished.';
                    self.done();
                }
            },
            done(){
                let self = this;
                $('.integrity-wrap .spinner-glow').addClass('loading-done');
                $('.integrity-wrap .checkmark').fadeIn('fast', function(){
                    $('.integrity-wrap .checkmark__circle').addClass('circle_start');
                    $('.integrity-wrap .checkmark').addClass('checkmark_start');
                    $('.integrity-wrap .checkmark__check').addClass('check_start');
                    setTimeout(function(){
                        self.show = false;
                        $('.integrity').fadeOut('fast');
                    }, 3000);
                });
            },
            resync(){
                let self = this;

                delete axios.defaults.headers.common['CSUI-SHOP'];
                self.status = self.$t('integrity.checking');

                $('.integrity-wrap .spinner-glow').removeClass('loading-done');

                $('.checkmark').fadeOut('fast').end().find('.integrity-wrap:not(.upsell)').fadeIn('medium', function(){
                    self.show = true;
                    $('.integrity-wrap .checkmark__circle').removeClass('circle_start');
                    $('.integrity-wrap .checkmark').removeClass('checkmark_start');
                    $('.integrity-wrap .checkmark__check').removeClass('check_start');

                    self.integrity.check(function(response){
                        self.handleCheckResponse(response);
                    });
                });
            }
        }
    }
</script>
