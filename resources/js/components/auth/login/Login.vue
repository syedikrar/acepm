<template>
    <v-flex xs12 sm12 md12 lg8 class="px-2">
        <v-card>
            <v-toolbar dark color="primary" flat>
                <v-toolbar-title>Login</v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <login-form @success="success"></login-form>
            </v-card-text>
        </v-card>

        <div class="text-center mt-4 white--text" style="position: relative">Don't have an account?
            <router-link :to="{ name: 'register' }" class="font-weight-bold black--text text-decoration-none">Register</router-link>
        </div>
    </v-flex>
</template>

<script>
    import LoginForm from './LoginForm'
    import {mapGetters} from 'vuex'

    export default {
        components: {
            LoginForm
        },
        computed: mapGetters({
            user: 'auth/user'
        }),

        methods: {
            success(data) {
                this.$store.dispatch('auth/saveToken', data)
                this.$store.dispatch('auth/setUser', data)
                this.$store.dispatch('auth/setBilling', data)
                // ----- distinguish between seller and owner
                let destination = 'index';
                if (this.user.role == 'seller')
                    destination = this.user.approved_at == null ? 'seller.limbo' : 'seller.dashboard';
                else
                    destination = this.user.approved_at == null ? 'limbo' : 'select-shop';

                this.$router.push({name: destination})
            }
        }
    }
</script>
