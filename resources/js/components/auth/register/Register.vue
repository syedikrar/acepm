<template>
    <v-flex xs12 sm12 md12 lg8 mb-10 class="px-2">
        <v-card>
            <v-toolbar dark color="primary" flat>
                <v-toolbar-title>Register as Freelancer</v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <register-form @success="success"></register-form>
            </v-card-text>
        </v-card>
    </v-flex>
</template>

<script>
    import RegisterForm from './RegisterForm'
    import {mapGetters} from 'vuex'

    export default {
        components: {
            RegisterForm
        },
        computed: mapGetters({
            user: 'auth/user'
        }),

        methods: {
            success(data) {
                this.$store.dispatch('auth/saveToken', data)
                this.$store.dispatch('auth/setUser', data)
                let destination = this.user.approved_at == null ? 'seller.limbo' : 'seller.dashboard';
                this.$router.push({name: destination})
            }
        }
    }
</script>
