export let guest = [
    {
        path: '', component: require('$comp/auth/AuthWrapper').default, redirect: {name: 'login'}, children:
            [
                {
                    path: '/login',
                    name: 'login',
                    component: require('$comp/auth/login/Login').default
                },
                {
                    path: '/register',
                    name: 'register',
                    component: require('$comp/auth/register/Register').default
                },
                {
                    path: '/register/:email',
                    name: 'register',
                    component: require('$comp/auth/register/Register').default
                },
                {
                    path: '/password',
                    component: require('$comp/auth/password/PasswordWrapper').default,
                    children: [
                        {
                            path: '',
                            name: 'forgot',
                            component: require('$comp/auth/password/password-forgot/PasswordForgot').default
                        },
                        {
                            path: 'reset/:token',
                            name: 'reset',
                            component: require('$comp/auth/password/password-reset/PasswordReset').default
                        }
                    ]
                }
            ],
    },
    {
        path: '/plans',
        name: 'guest.plans',
        component: require('$comp/limbo/views/Plans').default
    },
];

export default guest;
