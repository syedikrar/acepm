<template>
    <v-container
        id="users"
        fluid
        tag="section"
    >
        <base-v-component
            heading="Users"
            subheading="Users connected to ROI Manager."
        />

        <base-material-card
            icon="group"
            title="Users"
            class="px-5 py-3"
        >
            <v-layout row wrap>
                <v-flex xs12>
                    <v-data-table
                        :headers="headers"
                        :items="users"
                        :items-per-page="10"
                        class="br-4"
                    >
                        <template v-slot:item.admin="{ item }">
                            <v-switch
                                v-model="item.admin"
                                hide-details
                                class="mt-0 pt-0"
                                inset
                                @change="updateUser(item)"
                                :disabled="item.id == auth.id"
                            ></v-switch>
                        </template>
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
                                <v-btn icon color="error">
                                    <v-icon>delete_outline</v-icon>
                                </v-btn>
                            </confirmable>
                        </template>
                    </v-data-table>
                </v-flex>
            </v-layout>
        </base-material-card>
    </v-container>
</template>

<script>

    import axios from 'axios'
    import {mapGetters} from 'vuex'
    import { api } from '~/config'
    import Form from '~/mixins/form'
    import Confirmable from "../shared/confirmable";

    export default {
        mixins: [Form],
        data: () => ({
            busy        : false,
            headers     : [
                { text: 'Name',     value: 'name' },
                { text: 'Email',    value: 'email' },
                { text: 'Admin',    value: 'admin' },
                { text: 'Active', value: 'approved_at' },
                {
                    text: 'Actions',
                    align: 'center',
                    value: 'action',
                    width: 200
                }
            ],
            users: []
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
