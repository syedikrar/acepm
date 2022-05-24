import Helper from "../helpers/Helper";

export let seller = [
    {
        path: '/seller',
        name: 'seller',
        component: require('$comp/seller/SellerWrapper').default,
        children: [
            {
                path: 'limbo',
                name: 'seller.limbo',
                component: require('$comp/seller/views/limbo').default
            },
            ...Helper.applyRules(['approved'], [
                {
                    path: 'dashboad',
                    name: 'seller.dashboard',
                    component: require('$comp/seller/views/dashboard').default
                },
                {
                    path: 'profile',
                    component: require('$comp/seller/profile/ProfileWrapper').default,
                    children: [
                        {
                            path: '',
                            name: 'seller.profile',
                            component: require('$comp/seller/profile/Profile').default
                        },
                        {
                            path: 'edit',
                            name: 'seller.profile-edit',
                            component: require('$comp/seller/profile/edit/ProfileEdit').default
                        }
                    ]
                },
                {
                    path: '/gigs',
                    name: 'seller.gigs',
                    component: require('$comp/seller/views/gigs/dashboard').default
                },
                {
                    path: '/gigs/new',
                    name: 'seller.gigs.new',
                    component: require('$comp/seller/views/gigs/create').default
                },
                {
                    path: '/gigs/show/:id',
                    name: 'seller.gigs.single',
                    component: require('$comp/seller/views/gigs/single').default
                },
                {
                    path: '/contracts',
                    name: 'seller.contracts',
                    component: require('$comp/seller/views/gigs/contracts').default
                },
                {
                    path: '/jobs',
                    name: 'seller.jobs',
                    component: require('$comp/seller/views/gigs/jobs').default
                },
            ])
        ]
    }
];

export default seller;
