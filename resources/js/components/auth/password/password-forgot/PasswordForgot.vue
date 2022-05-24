<template>
    <v-card>
        <v-toolbar dark color="primary" flat>
            <v-toolbar-title>Password reset</v-toolbar-title>
        </v-toolbar>
        <v-card-text>
            <p>Just login to the app via Shopify apps page and reset your password from there.</p>

            <password-forgot-form @success="success"></password-forgot-form>
        </v-card-text>
    </v-card>
</template>

<script>
    import PasswordForgotForm from './PasswordForgotForm'

    export default {
        components: {
            PasswordForgotForm
        },

        methods: {
            success(data) {
                this.$router.push({name: 'login'})
            }
        }
    }
</script>
