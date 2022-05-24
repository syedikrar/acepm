<template>
    <v-card flat>
        <v-card-title class="pt-0">Edit Profile</v-card-title>
        <v-card-text>
            <profile-edit-form @success="success"></profile-edit-form>
        </v-card-text>
    </v-card>
</template>

<script>
    import ProfileEditForm from './ProfileEditForm'

    export default {
        components: {
            ProfileEditForm
        },

        methods: {
            success(data) {
                this.$store.dispatch('auth/setUser', data)
                this.$router.push({name: 'profile'})
            }
        }
    }
</script>
