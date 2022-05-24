<template>
    <div>
        <v-card flat>
            <v-card-text class="pb-0 pt-4">
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

        <v-card flat>
            <v-card-title >About Me</v-card-title>
            <v-card-text class="pb-0 pt-4">
                <p>
                    {{user.description}}
                </p>
            </v-card-text>
        </v-card>

        <v-card flat>
            <v-card-title>Education</v-card-title>
<!--            <v-card-title style="background-color: #2aa6fe;" class="mx-4 pb-2 title white&#45;&#45;text">Education</v-card-title>-->
            <v-card-text class="pb-0 pt-4">
                <v-combobox
                        label="Country Of Degree"
                        v-model="user.education.country"
                        :items="countryList"
                        item-name="Name"
                        item-value="Name"
                        readonly
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
                        v-model="user.education.university"
                        type="text"
                        readonly
                ></v-text-field>
                <v-row>
                    <v-col cols="4">
                        <v-combobox
                                label="Title"
                                v-model="user.education.title"
                                :items="educational_titles"
                                type="text"
                                readonly
                        ></v-combobox>
                    </v-col>
                    <v-col cols="8">
                        <v-text-field
                                label="Major"
                                v-model="user.education.major"
                                :items="yearList"
                                type="text"
                                readonly
                        ></v-text-field>
                    </v-col>
                </v-row>
                <v-combobox
                        label="Year Of Graduation"
                        v-model="user.education.year"
                        :items="yearList"
                        type="text"
                        readonly
                ></v-combobox>
            </v-card-text>
        </v-card>

        <v-card flat>
            <v-card-title>Languages</v-card-title>
            <v-card-text class="pb-0 pt-4">
                <v-chip v-for="(item, index) in user.languages"
                        class="ma-1"
                        :color="colors[Math.floor(Math.random() * colors.length)]"
                        style="font-size: 14px;"
                >
                    {{item}}
                </v-chip>
            </v-card-text>
        </v-card>

        <v-card flat>
            <v-card-title >Skills</v-card-title>
            <v-card-text class="pb-0 pt-4">
                <v-chip v-for="(item, index) in user.skills"
                            class="ma-1"
                            :color="colors[Math.floor(Math.random() * colors.length)]"
                    >
                        {{item.name}}
                </v-chip>
            </v-card-text>
        </v-card>

        <v-card flat>
            <v-card-title >Social Links</v-card-title>
            <v-card-text class="pb-0 pt-4">
                <v-chip v-for="(item, index) in user.social_links"
                        class="ma-1"
                        :color="colors[Math.floor(Math.random() * colors.length)]"
                        outlined
                        label
                >
                    <a target="_blank" :href="item" style="text-decoration: none; color: inherit;">
                        {{item}}
                    </a><br/>
                </v-chip>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        data: () => ({
            yearList: [],
            educational_titles: [
                'Associate', 'Certificate', 'B.A',
                'BArch', 'BM', 'BFA',
                'B.Sc', 'M.A.', 'M.B.A',
                'MFA', 'M.Sc', 'J.D.',
                'M.D.', 'Ph.D', 'LLB',
                'LLM', 'Other'],
            countryList : [],
            colors: [
                'primary',
                'green', 'violet',
                'blue', 'cyan',
                'orange'
            ]
        }),
        computed: mapGetters({
            auth: 'auth/user'
        }),

        methods: {
            getCountries() {
                let self = this;
                axios.get('/static/countries.json').then(function(resp){
                    self.countryList = resp.data;
                    self.preSetFields();
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
            preSetFields() {
                let self = this;
                if(self.user.education != null){
                    let indexEdu = self.countryList.findIndex(function (country) {
                        return country.Name == self.user.education.country;
                    });
                    if(indexEdu != -1) self.user.education.country = self.countryList[indexEdu];
                }else this.user.education = {};

            }
        },
        mounted() {
            this.getCountries();
            this.getYearList();
        }
    }
</script>
