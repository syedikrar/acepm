<template>
    <v-form ref="form" @submit.prevent="submit" lazy-validation v-model="valid">
        <v-text-field
            :label="labels.name"
            v-model="form.name"
            :error-messages="errors.name"
            :rules="[rules.required('name')]"
            :disabled="loading"
            outlined
        ></v-text-field>

        <v-text-field
            :label="labels.email"
            v-model="form.email"
            type="email"
            :error-messages="errors.email"
            :rules="[rules.required('email')]"
            :disabled="loading"
            outlined
        ></v-text-field>

        <v-text-field
            :label="labels.password"
            v-model="form.password"
            :append-icon="passwordHidden ? 'visibility_off' : 'visibility'"
            @click:append="() => (passwordHidden = !passwordHidden)"
            :type="passwordHidden ? 'password' : 'text'"
            :error-messages="errors.password"
            :disabled="loading"
            :rules="[rules.required('password')]"
            hint="At least 6 characters"
            outlined
        ></v-text-field>

        <v-text-field
            :label="labels.password_confirmation"
            v-model="form.password_confirmation"
            :type="passwordHidden ? 'password' : 'text'"
            :error-messages="errors.password_confirmation"
            :disabled="loading"
            :rules="[rules.required('password_confirmation')]"
            outlined
        ></v-text-field>

        <v-layout row class="my-1 mx-0">
            <v-spacer></v-spacer>

            <v-btn
                text
                :disabled="loading"
                :to="{ name: 'loggedin' }"
                color="grey darken-2"
                exact
            >
                Back to login
            </v-btn>

            <v-btn
                type="submit"
                :loading="loading"
                :disabled="loading || !valid"
                color="primary"
                class="ml-4"
                depressed
            >
                Register
            </v-btn>
        </v-layout>
    </v-form>
</template>

<script>
    import axios from 'axios'
    import {api} from '~/api'
    import Form from '~/mixins/form'

    export default {
        mixins: [Form],

        data: () => ({
            passwordHidden: true,

            form: {
                name: null,
                email: null,
                password: null,
                password_confirmation: null
            }
        }),

        methods: {
            submit() {
                let self = this;
                if (this.$refs.form.validate()) {
                    this.loading = true
                    axios.post(api.path('register'), this.form)
                        .then(res => {
                            if(res.status == 200){
                                self.showSnack('You have been successfully registered!')
                                this.$emit('success', res.data)
                            }
                            
                            
                        })
                        .catch(err => {
                            this.handleErrors(err.response.data.errors)
                        })
                        .then(() => {
                            this.loading = false
                        })
                }
            },
            emailInitialize(){
                let self = this;
                self.form.email = this.$router.currentRoute.params.email;
            }
        },
        mounted(){
            this.emailInitialize();
        }
    }
</script>
