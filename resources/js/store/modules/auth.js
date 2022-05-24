import axios from 'axios'
import {api} from '~/api'
import * as types from '../mutation-types'
import Helper from "../../helpers/Helper";
import {SET_BILLING} from "../mutation-types";

/**
 * Initial state
 */
let token = Helper.readCookie('ace-token') != null ?
    Helper.readCookie('ace-token') : sessionStorage.getItem('ace-token');
let shop = Helper.readCookie('ace-shop') != null ? Helper.readCookie('ace-shop') : null;

export const state = {
    user        : null,
    shop        : shop,
    token       : token,
    approved    : false,
    billing     : null
}

/**
 * Mutations
 */
export const mutations = {
    [types.SET_USER](state, {user}) {
        state.user = user
    },

    [types.SET_SHOP](state, {shop}) {
        state.shop = shop
    },

    [types.SET_BILLING](state, {billing}) {
        state.billing = billing
    },

    [types.LOGOUT](state) {
        state.user = null
        state.token = null
        sessionStorage.removeItem('ace-token')
        Helper.deleteCookie('ace-token');
        Helper.deleteCookie('ace-user');
        Helper.deleteCookie('ace-shop');
        Helper.deleteCookie('ace-shop-id');
    },

    [types.FETCH_USER_FAILURE](state) {
        state.user = null
        sessionStorage.removeItem('ace-token')
    },

    [types.SET_TOKEN](state, {token}) {
        state.token = token
        sessionStorage.setItem('ace-token', token)
    }
}

/**
 * Actions
 */
export const actions = {
    saveToken({commit}, payload) {
        commit(types.SET_TOKEN, payload)
    },

    async fetchUser({commit}) {
        try {
            const {data} = await axios.get(api.path('me'))
            commit(types.SET_USER, data)
            commit(types.SET_BILLING, data)
        } catch (e) {
            commit(types.FETCH_USER_FAILURE)
        }
    },

    setUser({commit}, payload) {
        commit(types.SET_USER, payload)
    },

    setShop({commit}, payload) {
        commit(types.SET_SHOP, payload)
    },

    setBilling({commit}, payload){
        commit(types.SET_BILLING, payload);
    },

    async logout({commit}) {
        await axios.post(api.path('logout'))
        commit(types.LOGOUT)
    },

    destroy({commit}) {
        commit(types.LOGOUT)
    }
}

/**
 * Getters
 */
export const getters = {
    user    : state => state.user,
    check   : state => state.user !== null,
    owner   : state => state.user && (state.user.role == 'owner' || state.user.role == 'staff'),
    staff   : state => state.user &&  state.user.role == 'staff',
    seller  : state => state.user && state.user.role == 'seller',
    approved: state => state.user.approved_at != null,
    billing : state => state.billing,
    paid    : state => state.billing.status == true,
    plan    : state => state.billing ? state.billing.plan : 'basic',
    limbo   : state => state.billing == null || state.billing.status == false,
    token   : state => state.token,
    shop    : state => state.shop,
}
