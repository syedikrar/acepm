<template>
    <v-container
        id="users"
        fluid
        tag="section"
    >
        <base-v-component
            heading="Jobs"
            subheading="These are Owner Jobs. Apply to any job."
        />

        <base-material-card
            icon="mdi-briefcase-search"
            title="Jobs"
            class="px-5 py-3"
        >
            <v-toolbar flat>
                <v-spacer></v-spacer>
                <v-col
                    class="d-flex"
                    cols="12"
                    sm="3"
                >
                    <v-select
                        :items="items"
                        label="Filter Jobs"
                        outlined
                    ></v-select>
                </v-col>
            </v-toolbar>
            <v-layout>
                <v-row>
                    <v-col cols="12">
                        <v-data-table
                            :headers="headers"
                            :items="jobs"
                            :items-per-page="10"
                            class="br-4"
                        >
                            <template v-slot:item.details="{ item }">
                                {{ item.descriptions }}
                            </template>
                            <template v-slot:item.category="{ item }">
                                {{ item.category.title }} -> {{ item.sub_category.title }}
                            </template>
                            <template v-slot:item.action="{ item }">
                                <v-btn color="green">
                                    Apply
                                </v-btn>
                            </template>
                        </v-data-table>
                    </v-col>
                </v-row>
            </v-layout>
        </base-material-card>
    </v-container>
</template>

<script>
    import axios from "axios";

    export default {
        name: "jobs",
        data: () => ({
            busy        : false,
            headers     : [
                { text: 'Details',     value: 'details' },
                { text: 'Category',    value: 'category' },
                { text: 'Created',    value: 'created_at' },
                {
                    text: 'Actions',
                    align: 'center',
                    value: 'action',
                    width: 200
                }
            ],
            jobs: []
        }),
        mounted() {
            this.getJobs();
        },
        methods: {
            getJobs: function() {
                let self = this;
                axios.get(api.path('gig.seller_jobs'))
                    .then((response)=>{
                        self.jobs = response.data;
                    });
            }
        }
    }
</script>

<style scoped>

</style>
