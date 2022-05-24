import Vue from 'vue'
import Vuetify, { VSnackbar, VBtn, VIcon, VForm } from 'vuetify/lib'
import VuetifyToast from 'vuetify-toast-snackbar'
import VueCtkDateTimePicker from 'vue-ctk-date-time-picker';
import i18n from '@/i18n'

Vue.use(Vuetify, {
    components: {
        VSnackbar,
        VBtn,
        VIcon,
        VForm
    }
})

Vue.component('v-style', {
    render: function (createElement) {
        return createElement('style', this.$slots.default)
    }
});

Vue.component('VueCtkDateTimePicker', VueCtkDateTimePicker);

let colors = {
    primary     : '#4895EB',
    secondary   : '#D53B64',
    accent      : '#4252AF',
    info        : '#00CAE3',
    attention   : '#FF89AE'
};

let darkColors = {
    primary     : '#4895EB',
    secondary   : '#D53B64',
    accent      : '#4252AF',
    info        : '#00CAE3',
    attention   : '#FF89AE'
};

const VuetifyObj = new Vuetify({
    lang: {
        t: (key, ...params) => i18n.t(key, params),
    },
    theme   : {
        dark    : false,
        themes  : {
            light   : colors,
            dark    : darkColors
        }
    }
});

export default VuetifyObj;

Vue.use(VuetifyToast, {
    $vuetify    : VuetifyObj.framework,
    x           : 'right',
    y           : 'bottom',
    color       : 'info',
    icon        : 'info',
    iconColor   : '',
    classes     : [
        'body-1'
    ],
    timeout     : 50000,
    dismissable : true,
    multiLine   : false,
    vertical    : false,
    queueable   : false,
    showClose   : true,
    closeText   : 'Close',
    closeIcon   : 'close',
    closeColor  : '',
    slot        : [],
    shorts      : {
        custom: {
            color: 'purple'
        }
    },
    property    : '$toast' // default
});
