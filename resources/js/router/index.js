import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '~/store/index'
import routes from './routes'
import {api} from '~/api'

Vue.use(VueRouter)

const router = new VueRouter({
    base: process.env.APP_URL,
    mode: process.env.NODE_ENV == 'production' ? 'history' : 'hash',
    routes
})

router.beforeEach(async (to, from, next) => {
    if (store.getters['auth/token'] && !store.getters['auth/check']) {
        try {
            await store.dispatch('auth/fetchUser')
        } catch (e) {

        }
    }

    let route = reroute(to)
    if (route) {
        next(route)
    } else {
        next()
    }
})

const rules = {
    guest       : {fail: 'select-shop', check: () => (!store.getters['auth/check'])},
    auth        : {fail: 'login', check: () => (store.getters['auth/check'])},
    owner       : {fail: 'login', check: () => (store.getters['auth/owner'])},
    seller      : {fail: 'login', check: () => (store.getters['auth/seller'])},
    limbo       : {fail: 'login', check: () => (store.getters['auth/limbo'])},
    approved    : {fail: 'login', check: () => (store.getters['auth/approved'])},
    paid        : {fail: 'limbo', check: () => (store.getters['auth/owner'] && store.getters['auth/paid'])},
    enterprise  : {fail: 'dashboard', check: () => (store.getters['auth/plan'] == 'enterprise')},
}

function reroute(to) {
    let failRoute = false,
        checkResult = false

    to.meta.rules && to.meta.rules.forEach(rule => {
        let check = false
        if (Array.isArray(rule)) {
            let checks = []
            for (let i in rule) {
                checks[i] = rules[rule[i]].check()
                check = check || checks[i]
            }
            if (!check && !failRoute) {
                failRoute = rules[rule[checks.indexOf(false)]].fail
            }
        } else {
            check = rules[rule].check()
            if (!check && !failRoute) {
                failRoute = rules[rule].fail
            }
        }

        checkResult = checkResult && check
    })

    if (!checkResult && failRoute) {
        return {name: failRoute}
    }

    return false
}

export default router
