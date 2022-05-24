import Vue from 'vue';
import {
    mapState, mapGetters
} from 'vuex'
import Helper from "../helpers/Helper";

const globals = {
    snack   : {
        text    : '',
        show    : false,
        color   : 'info'
    },
    alert   : {
        show    : false,
        text    : ''
    }
};

Vue.mixin({
    data() {
        return { globals }
    },
    computed: {
        ...mapGetters({
            auth        : 'auth/user',
            user        : 'auth/user',
            authCheck   : 'auth/check',
            billing     : 'auth/billing',
            shop        : 'auth/shop',
        }),
    },
    methods: {
        showSnack: function(resp){
            let self = this;
            self.globals.snack.show = false

            let text = typeof resp == 'object' ? resp.message : resp;
            let color = typeof resp == 'object' && resp.status == false ? 'warning darken-2' : 'info';

            setTimeout(() => {
                self.globals.snack.text = text;
                self.globals.snack.color = color;
                self.globals.snack.show = true;
            }, 250)
        },
        showAlert: function(resp){
            let self = this;

            self.globals.alert.show = true;
            self.globals.alert.text = resp;
        },
        localTime: function(dateTime, cooked){
            let stillUtc = moment.utc(dateTime).toDate();
            let local = moment(stillUtc).local();
            if (cooked != undefined) return local.format('MMMM DD, YYYY hh:mm A')
            else return local;
        },
        getUser: function(){
            return typeof this.user == 'string' ? JSON.parse(this.user) : this.user
        },
        getShop: function(){
            return Helper.readCookie('ace-shop');
        },
        getShopId: function(){
            return Helper.readCookie('ace-shop-id');
        },
        showCloak: function(c){
            $('body').addClass('no-scroll');
            $('.veil').fadeIn('medium', c);
        },
        hideCloak: function(c){
            $('body').removeClass('no-scroll');
            $('.veil').fadeOut('medium', c);
        },
        ago: function(date) {
            let localDate = this.localTime(date,true);
            return moment(localDate).fromNow();
        },
        capitalizeFirstLetter: function (str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        },

    }
});

export default globals;
