import 'babel-polyfill'
import _ from 'lodash'

import Vue from 'vue'
import router from '~/router/index'
import store from '~/store/index'
import App from '$comp/App'
import '~/plugins/index'
import vuetify from '~/plugins/vuetify'
import './plugins/base'
import './plugins/chartist'
import './plugins/vee-validate'
import moment from "moment"
import i18n from "./i18n";
import globals from "./mixins/mixin"
import Helper from "./helpers/Helper";
import jquery from 'jquery';
import axios from "axios";
import {api} from './api'
import VueAxios from 'vue-axios'
import VueSocialAuth from 'vue-social-auth'

window._ = _
window.moment = moment;
window.$ = jquery;
window.jQuery = jquery;
window.axios = axios;
window.api = api;

Vue.config.productionTip = false;
Vue.use(VueAxios, axios);

//---------Social logins support plugin
Vue.use(VueSocialAuth, {
    providers: {
        facebook: {
            clientId: process.env.MIX_FACEBOOK_APP_ID,
            redirectUri: process.env.MIX_DEVELOPMENT_URL+'/auth/facebook/callback'
        },
        google: {
            clientId: process.env.MIX_GOOGLE_CLIENT_ID,
            redirectUri: process.env.MIX_DEVELOPMENT_URL+'/auth/google/callback'
        }
    }
});

window.spa = new Vue({
    router,
    store,
    vuetify,
    i18n,
    render  : h => h(App)
}).$mount('#app');

Vue.component('v-style', {
    render: function (createElement) {
        return createElement('style', this.$slots.default)
    }
});

export const app = spa;
