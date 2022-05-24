<template>
    <v-form ref="form" @submit.prevent="submit" lazy-validation v-model="valid">
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

        <h3 class="headline mb-4 mt-12">Personal Information</h3>

        <v-card>
            <v-card-text>
                <v-textarea
                        label="Describe Yourself"
                        v-model="form.description"
                        type="text"
                        :disabled="loading"
                        outlined
                        :error-messages="errors.description"
                        :rules="[rules.required('description'), customRule]"
                ></v-textarea>

                <v-combobox
                        v-model="form.languages"
                        :items="languagesList"
                        label="Languages You Speak"
                        multiple
                        outlined
                        dense
                        small-chips
                        :error-messages="errors.languages"
                        :rules="[rules.required('languages')]"
                ></v-combobox>

                <v-combobox
                    label="Select Your Country"
                    v-model="form.country"
                    :items="countryList"
                    item-name="Name"
                    item-value="Name"
                    :disabled="loading"
                    outlined
                    clearable
                    :error-messages="errors.country"
                    :rules="[rules.required('country')]"
                >
                    <template slot="selection" slot-scope="data">
                        <v-img class="mr-4" :lazy-src="data.item.FlagPng"
                               max-height="25"
                               max-width="40"
                               color="primary"
                               :src="data.item.FlagPng"> </v-img> {{ data.item.Name }}
                    </template>
                    <template slot="item" slot-scope="data">
                        <v-img class="mr-4" :lazy-src="data.item.FlagPng"
                               max-height="25"
                               max-width="40"
                               :src="data.item.FlagPng"> </v-img> {{ data.item.Name }}
                    </template>
                </v-combobox>
            </v-card-text>
        </v-card>

        <h3 class="headline mb-4 mt-12">Education</h3>

        <v-card>
            <v-card-text>
                <v-combobox
                        label="Country Of Degree"
                        v-model="form.education.country"
                        :items="countryList"
                        item-name="Name"
                        item-value="Name"
                        outlined
                        clearable
                        :error-messages="errors.country"
                        :rules="[rules.required('country')]"
                >
                    <template slot="selection" slot-scope="data">
                        <v-img class="mr-4" :lazy-src="data.item.FlagPng"
                               max-height="25"
                               max-width="40"
                               color="primary"
                               :src="data.item.FlagPng"> </v-img> {{ data.item.Name }}
                    </template>
                    <template slot="item" slot-scope="data">
                        <v-img class="mr-4" :lazy-src="data.item.FlagPng"
                               max-height="25"
                               max-width="40"
                               :src="data.item.FlagPng"> </v-img> {{ data.item.Name }}
                    </template>
                </v-combobox>

                <v-text-field
                        label="Country/University Name"
                        v-model="form.education.university"
                        type="text"
                        :disabled="loading"
                        outlined
                        :error-messages="errors.university"
                        :rules="[rules.required('university')]"
                ></v-text-field>
                <v-row>
                    <v-col cols="4">
                        <v-combobox
                                label="Title"
                                v-model="form.education.title"
                                :items="educational_titles"
                                type="text"
                                :disabled="loading"
                                outlined
                                :error-messages="errors.title"
                                :rules="[rules.required('title')]"
                        ></v-combobox>
                    </v-col>
                    <v-col cols="8">
                        <v-text-field
                                label="Major"
                                v-model="form.education.major"
                                :items="yearList"
                                type="text"
                                :disabled="loading"
                                outlined
                                :error-messages="errors.major"
                                :rules="[rules.required('major')]"
                        ></v-text-field>
                    </v-col>
                </v-row>
                <v-combobox
                        label="Year Of Graduation"
                        v-model="form.education.year"
                        :items="yearList"
                        type="text"
                        :disabled="loading"
                        outlined
                        :error-messages="errors.year"
                        :rules="[rules.required('year')]"
                ></v-combobox>

            </v-card-text>
        </v-card>

        <h3 class="headline mb-4 mt-12">Skills</h3>

        <v-card>
            <v-card-text>
                <v-combobox
                        v-model="form.skills"
                        :items="skillsList"
                        label="Skills Set"
                        multiple
                        outlined
                        dense
                        small-chips
                        :rules="[rules.required('skills')]"
                ></v-combobox>

            </v-card-text>
        </v-card>

        <h3 class="headline mb-4 mt-12">Social Links</h3>

        <v-card>
            <v-card-text>
                <v-combobox
                        v-model="form.social_links"
                        :items="form.social_links"
                        label="Social Links"
                        multiple
                        outlined
                        dense
                        small-chips
                        :error-messages="errors.social_links"
                        :rules="[rules.required('social_links')]"
                ></v-combobox>
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
    import langList from '~/config/languages'
    import skilList from '~/config/skills'

    export default {
        mixins: [Form],

        data: () => ({
            passwordHidden: true,
            languagesList : langList.languages,
            countryList: [],
            skillsList : skilList.skills,
            educational_titles: ['Associate', 'Certificate', 'B.A', 'BArch', 'BM', 'BFA', 'B.Sc', 'M.A.', 'M.B.A', 'MFA', 'M.Sc', 'J.D.', 'M.D.', 'Ph.D', 'LLB', 'LLM', 'Other'],
            yearList: [],
            label: {
                password: 'New Password',
                password_confirmation: 'Confirm New Password',
            },
            form: {
                name: null,
                email: null,
                password: null,
                password_confirmation: null,
                description: null,
                education: {
                    country: null,
                    university: null,
                    title: null,
                    major: null,
                    year: null
                },
                country: null,
                skills : [],
                social_links   : [],
                languages: []
            },
            customRule : v => (v || '' ).length <= 300 || 'Description must be less than 300 characters',
        }),

        computed: mapGetters({
            auth: 'auth/user'
        }),
        mounted() {
            if(this.auth.social_links == null) this.auth.social_links = [];
            self.form = Object.assign(this.form, this.auth);
            this.getCountries();
            this.getYearList();
        },
        methods: {
            submit() {
                let self = this;
                if (this.$refs.form.validate()) {
                    this.loading = true
                    self.setSubForm();
                    axios.put(api.path('profile'), self.form)
                        .then(res => {
                            self.showSnack('Your profile is successfully updated.')
                            this.$emit('success', res.data)
                        })
                        .catch(err => {
                            this.handleErrors(err.response.data.errors)
                        })
                        .then(() => {
                            this.loading = false;
                            self.setDisForm(false);
                        });
                }
            },
            getCountries() {
                let self = this;
                axios.get('/static/countries.json').then(function(resp){
                    self.countryList = resp.data;
                    self.setDisForm();
                });
            },
            getYearList() {
                let self = this;
                let Start = new Date("June 26, 1950 11:13:00");
                let End = new Date();
                let yearsDiff = moment(End).diff(Start, 'years');

                for(let year = 0; year < yearsDiff; year++)
                    self.yearList.push(Start.getFullYear() + year);
            },
            setDisForm(initializeSkills = true) {
                let self = this;

                if(initializeSkills) {
                    let skills = [];
                    self.form.skills.forEach( function (item, index) {
                        skills.push(item.name);
                    });
                    self.form.skills = skills;
                }

                if(self.form.country != null){
                    let index = self.countryList.findIndex(function (country) {
                        return country.Name == self.form.country;
                    });
                    if (index != -1) self.form.country = self.countryList[index];
                }

                if(self.form.education != null){
                    let indexEdu = self.countryList.findIndex(function (country) {
                        return country.Name == self.form.education.country;
                    });
                    if(indexEdu != -1) self.form.education.country = self.countryList[indexEdu];
                }else self.form.education = {};
            },
            setSubForm() {
                let self = this;
                self.form.country = (self.form.country != null) ? self.form.country.Name : null;
                self.form.education.country = (self.form.education.country != null) ? self.form.education.country.Name : null;
                delete self.form.approved_at;
                delete self.form.role;
                delete self.form.created_at;
                delete self.form.updated_at;
            }
        }
    }
</script>
