const apiUrl = appSlug.apiUrl;

export const settings = {
    appName: appSlug.appName,
    appURL: appSlug.siteUrl
}

class URL {
    constructor(base) {
        this.base = base
    }

    path(path, args) {
        path = path.split('.')
        let obj = this,
            url = this.base

        for (let i = 0; i < path.length && obj; i++) {
            if (obj.url) {
                url += '/' + obj.url
            }

            obj = obj[path[i]]
        }
        if (obj) {
            url = url + '/' + (typeof obj === 'string' ? obj : obj.url)
        }

        if (args) {
            for (let key in args) {
                url = url.replace(':' + key, args[key])
            }
        }

        return url
    }
}

export const api = Object.assign(new URL(apiUrl), {
    url             : '',

    login: {
        url         : 'login',
        refresh     : 'refresh'
    },

    logout: 'logout',

    register: 'register',

    password: {
        url         : 'password',
        forgot      : 'email',
        reset       : 'reset'
    },

    me: 'me',

    users: {
        url         : 'users',
        teamTasks   : 'teamTasks',
        bestSellers : 'best-sellers',
        activate    : ':id/activate',
        single      : ':id',
        restore     : ':id/restore',
        update      : ':id/update-status',
        delete      : ':id/delete'
    },

    shopify: {
        url             : 'shopify',

        install         : 'install/:shop',
        uninstall       : 'clean-uninstall',
        integrity       : 'integrity/:shopId',
        planSelected    : 'plan-selected/:plan',
        cleanUninstall  : 'clean-uninstall'
    },

    static_files    : {
        url         : 'static',
        countries   : 'countries.json'
    },

    integrity: {
        url         : 'integrity',

        check       : 'check',
        fix         : 'fix',
        shopJson    : 'shop-info.json',
        fieldTypes  : 'field-types.json'
    },

    resource: {
        url     : 'resource',

        getProducts     : 'get-products',
        getOrders       : 'get-orders',
    },

    profile: {
        url: 'profile'
    },

    discountCoupon: {
        url : 'discount-coupons',
        get : 'get.json',
        save: 'save',
        check: 'check/:code',
        delete: 'delete/:id'
    },

    upsellCampaign: {
        url: 'upsell-campaign',
        get : 'get.json',
        active: 'active.json',
        save: 'save',
        toggle: 'toggle',
        delete: 'delete/:id',
        stats : 'stats',
    },

    templates:{
        url : 'templates',

        all : 'all',
        byField : 'field/:field',
        get : ':id',
        save: 'save',
        delete: 'delete/:id'
    },

    boards:{
        url : 'boards',

        all : 'all',
        byField : 'field/:field',
        fromTemplate: 'from-template',
        get : ':id',
        save: 'save',
        delete: 'delete/:id',
        restore: 'restore/:id',
        backgroundImage: 'background-image',
        deleteBackgroundImage: ':id/background-image'
    },

    cards:{
        url : 'cards',

        get : ':id',
        save: 'save',
        delete: 'delete/:id'
    },

    subTasks:{
        url : 'sub-tasks',

        get : ':id',
        save: 'save',
        delete: 'delete/:id'
    },

    shopifyIntegration:{
        url : 'shopify-integration',

        get : ':id',
        save: 'save',
        delete: 'delete/:id'
    },

    dropBoxIntegration:{
        url : 'dropbox-integration',
        getSavedDataByCardId        : 'getSavedDataByCardId/:id',
        get : ':id',
        save: 'save',
        delete: 'delete/:id'
    },


    googleDriveIntegration:{
        url : 'google-drive-integration',

        socialLogin     : 'socialPlatformLogin',
        socialLogout    : 'socialPlatformLogout',
        getSavedDataByCardId        : 'getSavedDataByCardId/:id',
        get : ':id',
        save: 'save',
        delete: 'delete/:id'
    },

    socialLogin:{
        url : 'social-login',

        socialLogin     : 'socialPlatformLogin',
        socialLogout    : 'socialPlatformLogout',
    },
    comments:{
        url : 'comments',

        get : ':id',
        save: 'save',
        delete: 'delete/:id'
    },

    gig: {
        url: 'gigs',
        save: 'save',
        categories: 'categories',
        sub_categories: 'sub_categories/:id',
        all: "all",
        single: 'show/:id',
        save_order: 'order',
        contracts: 'contracts',
        seller_contracts: 'contracts-seller',
        contract_delete: 'contracts/:id/delete',
        save_job: 'job/save',
        seller_jobs: 'jobs'
    },

    timeTracker:{
        url : 'time-trackers',
        save: 'save',
    },

    attachments:{
        url : 'attachments',
        save: 'save',
        delete : 'delete/:id'
    }

})
