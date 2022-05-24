<template>
    <v-card flat>
        <v-card-title class="pt-0">Your Profile</v-card-title>
        <v-card-text>
            <v-text-field
                label="Name"
                v-model="user.name"
                filled
                readonly
                hide-details
                class="mb-4"
            ></v-text-field>

            <v-text-field
                label="Email"
                v-model="user.email"
                filled
                readonly
                hide-details
            ></v-text-field>
        </v-card-text>
    </v-card>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        data: () => ({
        }),

        computed: mapGetters({
            auth: 'auth/user'
        }),

        mounted() {
        }
    }
</script>
