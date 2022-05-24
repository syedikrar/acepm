<template>
    <v-form ref="form" @submit.prevent="submit"  enctype="multipart/form-data">
        <v-card>
            <v-card-text>
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
                <v-col cols="12" sm="6">
                    <image-input v-model="file">
                        <div slot="activator">
                            <v-card @click="" flat class="px-10 py-4 mt-5" color="background">
                                <v-row no-gutters align="center" justify="center">
                                    <v-col lg="8">
                                        <div class="faan-font-medium faan-subheading">
                                            Your Picture
                                        </div>
                                        <div class="faan-font-regular faan-caption my-2">
                                            Update your profile picture by clicking here.
                                        </div>
                                        <div class="faan-font-light text--grey faan-sm-caption"> Size should be less than 1MB </div>
                                    </v-col>
                                    <v-col lg="4">
                                        <v-avatar size="145px" v-ripple v-if="!file.imageURL" tile>
                                            <img src="/images/company_placeholder.png">
                                        </v-avatar>
                                        <v-avatar size="145px" v-else tile>
                                            <v-img
                                                    class="white--text align-end"
                                                    :src="file.imageURL"
                                                    alt="avatar">
                                            </v-img>
                                        </v-avatar>
                                    </v-col>
                                </v-row>
                            </v-card>
                        </div>
                    </image-input>
                </v-col>
            </v-card-text>
        </v-card>

        <h3 class="headline mb-4 mt-12">Password</h3>

        <v-card>
            <v-card-text>
                <v-text-field
                    :label="labels.password"
                    v-model="form.password"
                    :append-icon="passwordHidden ? 'visibility_off' : 'visibility'"
                    @click:append="() => (passwordHidden = !passwordHidden)"
                    :type="passwordHidden ? 'password' : 'text'"
                    :error-messages="errors.password"
                    :disabled="loading"
                    hint="At least 6 characters"
                    autocomplete="new-password"
                    outlined
                ></v-text-field>

                <v-text-field
                    :label="labels.password_confirmation"
                    v-model="form.password_confirmation"
                    :type="passwordHidden ? 'password' : 'text'"
                    :error-messages="errors.password_confirmation"
                    :disabled="loading"
                    autocomplete="new-password"
                    outlined
                ></v-text-field>
            </v-card-text>
        </v-card>

        <v-layout mt-12 mx-0>
            <v-spacer></v-spacer>

            <v-btn
                text
                :disabled="loading"
                :to="{ name: 'profile' }"
                color="grey darken-2"
                exact
            >
                Cancel
            </v-btn>

            <v-btn
                type="submit"
                :loading="loading"
                :disabled="loading"
                color="primary"
                class="ml-4"
            >
                Save
            </v-btn>
        </v-layout>
    </v-form>
</template>

<script>
    import axios from 'axios'
    import {mapGetters} from 'vuex'
    import {api} from '~/api'
    import Form from '~/mixins/form'
    import ImageInput from '../../shared/ImageInput';

    export default {
        mixins: [Form],
        components: {
            ImageInput: ImageInput
        },
        data: () => ({
            passwordHidden: true,

            label: {
                password: 'New Password',
                password_confirmation: 'Confirm New Password',
            },
            file: {
                imageFile: null,
                imageURL: null
            },
            form: {
                name: null,
                email: null,
                password: null,
                password_confirmation: null,
            }
        }),

        computed: mapGetters({
            auth: 'auth/user'
        }),

        mounted() {
            this.form = Object.assign(this.form, this.auth)
            this.getLogo();
        },

        methods: {
            submit() {
                let self = this;
                if (this.$refs.form.validate()) {
                    this.loading = true;
                    let formData = new FormData();
                    formData.append("data", JSON.stringify(this.form));
                    formData.append("logo", this.file.imageFile);
                    axios.post(api.path('profile'), formData)
                        .then(res => {
                            self.showSnack('Your profile successfully updated.')
                            this.$emit('success', res.data)
                        })
                        .catch(err => {
                            this.handleErrors(err.response.data.errors)
                        })
                        .then(() => {
                            this.loading = false
                        })
                }
            },
            getLogo() {
                if (this.form.profile_picture != null) {
                    this.file = Object.assign(this.file, {"imageURL": this.form.profile_picture});
                }

            }
        }
    }
</script>
