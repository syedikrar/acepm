import Helper from "../helpers/Helper";

export let limbo = [
    {
        path: '/limbo',
        name: 'limbo',
        component: require('$comp/limbo/LimboWrapper').default,
        children: [
            {
                path: 'accept-billing',
                name: 'limbo.accept-billing',
                component: require('$comp/limbo/views/AcceptBilling').default
            },
            {
                path: 'affiliate',
                name: 'limbo.affiliate',
                component: require('$comp/limbo/views/Affiliates').default
            },
            {
                path: 'free-pass',
                name: 'limbo.free-pass',
                component: require('$comp/limbo/views/FreePass').default
            },
            {
                path: 'plans',
                name: 'limbo.plans',
                component: require('$comp/limbo/views/Plans').default
            }
        ]
    }
];

export let owner = [
    {
        path: '',
        component: require('$comp/admin/AdminWrapper').default,
        children: [
            {
                path: '',
                name: 'index',
                redirect: {name: 'dashboard'}
            },
            {
                path: 'profile',
                component: require('$comp/admin/profile/ProfileWrapper').default,
                children: [
                    {
                        path: '',
                        name: 'profile',
                        component: require('$comp/admin/profile/Profile').default
                    },
                    {
                        path: 'edit',
                        name: 'profile-edit',
                        component: require('$comp/admin/profile/edit/ProfileEdit').default
                    }
                ]
            },
            {
                path: 'dashboard',
                name: 'dashboard',
                component: require('$comp/admin/views/dashboard').default
            },
            ...Helper.applyRules(['enterprise'], [
                {
                    path: 'boards/:field',
                    name: 'ace_boards',
                    component: require('$comp/admin/views/boards').default,
                    props: true
                },
                {
                    path: 'board/:mode/:field/:id',
                    name: 'ace_board',
                    component: require('$comp/admin/views/boardWrapper').default,
                    props: (route) => {
                        const id = Number.parseInt(route.params.id, 10)
                        if (Number.isNaN(id)) {
                            return 0
                        }
                        return { id: id, mode : route.params.mode, field : route.params.field }
                    }
                },
                {
                    path: '/gigs/show/:id',
                    name: 'gigs.single',
                    component: require('$comp/seller/views/gigs/single').default
                },
                {
                    path: '/gigs/order/:id/:package',
                    name: 'gigs.order',
                    component: require('$comp/seller/views/gigs/order').default
                },
                {
                    path: '/gigs/contracts',
                    name: 'gigs.contracts',
                    component: require('$comp/admin/views/gigs/contracts').default
                },
            ]),
            {
                path: 'marketplace',
                component: require('$comp/marketPlace/marketPlaceWrapper').default,
                children: [
                    {
                        path: '',
                        name: 'marketPlace',
                        component: require('$comp/marketPlace/views/Listing').default
                    },
                    {
                        path: '/gigs/show/:id',
                        name: 'marketPlace.gigs.single',
                        component: require('$comp/marketPlace/views/gigs/single').default
                    },
                ]
            },
            {
                path: 'change-plan',
                name: 'plans',
                component: require('$comp/limbo/views/Plans').default
            },
            {
                path: '/csui',
                name: 'csui',
                component: require('$comp/admin/views/csui/dashboard').default
            },
            {
                path      : '/team',
                name      : 'team',
                component : require('$comp/admin/views/team').default
            }

        ]
    },
    {
        path: '/shopSelection',
        name: 'select-shop',
        component: require('$comp/SelectShop').default,
    }
];

export default {limbo, owner};
