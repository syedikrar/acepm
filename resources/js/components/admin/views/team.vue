<template>
    <v-container
        id="users"
        fluid
        tag="section"
    >
        <base-v-component
            heading="My Team"
            subheading="Users connected to your ACE Account."
        />

        <base-material-card
            icon="group"
            title="Team"
            class="px-5 py-3"
        >
            <v-toolbar flat>
                <v-spacer></v-spacer>
                <v-dialog
                        v-model="inviteDialog"
                        max-width="340"
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn text outlined v-bind="attrs"
                               v-on="on">Invite a Member</v-btn>
                    </template>
                    <v-form ref="form" @submit.prevent="submit" lazy-validation v-model="valid">
                        <v-card>
                            <v-card-title>
                                <p class="text-h5"> Register Your Member</p>
                            </v-card-title>
                            <v-card-text>
                                <!-- <v-text-field
                                        :label="labels.name"
                                        v-model="form.name"
                                        :error-messages="errors.name"
                                        :rules="[rules.required('name')]"
                                        :disabled="loading"
                                        outlined
                                ></v-text-field> -->

                                <v-text-field
                                        :label="labels.email"
                                        v-model="form.email"
                                        type="email"
                                        :error-messages="errors.email"
                                        :rules="[rules.required('email')]"
                                        :disabled="loading"
                                        outlined
                                ></v-text-field>

                                <!-- <v-text-field
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
                                ></v-text-field> -->

                                <!-- <v-text-field
                                        :label="labels.password_confirmation"
                                        v-model="form.password_confirmation"
                                        :type="passwordHidden ? 'password' : 'text'"
                                        :error-messages="errors.password_confirmation"
                                        :disabled="loading"
                                        :rules="[rules.required('password_confirmation')]"
                                        outlined
                                ></v-text-field> -->

                                <v-layout row class="my-1 mx-0">
                                    <v-spacer></v-spacer>
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
                            </v-card-text>
                        </v-card>
                    </v-form>
                </v-dialog>
            </v-toolbar>
            <v-layout>
                <v-row>
                    <v-col cols="12">
                        <v-data-table
                        :headers="headers"
                        :items="users"
                        :items-per-page="10"
                        class="br-4"
                    >
                        <template v-slot:item.approved_at="{ item }">
                            <v-switch
                                v-model="item.approved_at"
                                hide-details
                                inset
                                class="mt-0 pt-0"
                                @change="updateUser(item)"
                                :disabled="item.id == auth.id"></v-switch>
                        </template>
                        <template v-slot:item.action="{ item }">
                            <confirmable :target="item" :name="'User'" @delete="deleteUser">
                                <v-btn icon text color="red">
                                    <v-icon>delete_outline</v-icon>
                                </v-btn>
                            </confirmable>
                        </template>
                    </v-data-table>
                    </v-col>
                </v-row>
            </v-layout>
        </base-material-card>
    </v-container>
</template>

<script>

    import axios from 'axios'
    import {mapGetters} from 'vuex'
    import {api} from '~/api'
    import Form from '~/mixins/form'
    import Confirmable from "../shared/confirmable";

    export default {
        mixins: [Form],
        data: () => ({
            loading     : false,
            headers     : [
                { text: 'Name',     value: 'name' },
                { text: 'Email',    value: 'email' },
                { text: 'Role',    value: 'role' },
                { text: 'Active', value: 'approved_at' },
                {
                    text: 'Actions',
                    align: 'center',
                    value: 'action',
                    width: 200
                }
            ],
            users: [],
            passwordHidden: true,
            form: {
                // name: null,
                email: null,
                // password: null,
                // password_confirmation: null
            },
            inviteDialog : false
        }),
        methods: {
            getUsers: function(){
                let self = this;
                axios
                    .get(api.path('users'))
                    .then(function(resp){
                        self.users = resp.data;
                    });
            },
            updateUser: function(user){
                let self = this;
                axios
                    .put(api.path('users.update', {id: user.id}), user)
                    .then(function(resp){
                        self.showSnack(resp.data.message);
                    });
            },
            deleteUser: function(user){
                let self = this;
                axios
                    .delete(api.path('users.delete', {id: user.id}), user)
                    .then(function(resp){
                        self.showSnack('User removed.');
                        self.users.splice(self.users.indexOf(user), 1);
                    });
            },
            submit() {
                let self = this;
                if (this.$refs.form.validate()) {
                    this.loading = true
                    axios.post(api.path('register')+'?invite=true', this.form)
                        .then(res => {
                            if(res.data.status == 'user found'){
                               self.showSnack(res.data.data +' '+ 'Already Exists');
                            }
                            else{
                                self.showSnack('User registered successfully!');
                                self.users.push(res.data.user);
                                self.inviteDialog = false;
                            }
                            
                        })
                        .catch(err => {
                            this.handleErrors(err.response.data.errors)
                        })
                        .then(() => {
                            this.loading = false
                        })
                }
            }
        },

        mounted() {
            this.getUsers();
        },
        components: {
            Confirmable
        },
        computed: mapGetters({
            auth: 'auth/user'
        })
    }
</script>
