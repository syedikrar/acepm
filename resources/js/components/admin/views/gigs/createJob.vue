<template>
    <v-card v-if="card != null">
        <v-toolbar
            dark
            flat
            color="info"
        >
            <v-btn
                icon
                dark
                @click="done"
                v-if="$vuetify.breakpoint.smAndDown"
                small
            >
                <v-icon>mdi-close</v-icon>
            </v-btn>
            <v-toolbar-title style="width: 80%">
                Create Job for Card: {{ card.title }}
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-toolbar-items>
                <v-btn
                    dark
                    text
                    v-if="$vuetify.breakpoint.mdAndDown"
                    @click="done"
                >
                    Save
                </v-btn>
            </v-toolbar-items>
        </v-toolbar>
        <v-card-text style="max-height: 68vh; overflow-y: scroll" class="slimScroll">
            <v-row dense>
                <v-col cols="8" class="pr-4">
                    <v-row>
                        <v-col cols="12">
                            <v-textarea v-model="job.descriptions"
                                        label="Description"
                                        filled
                                        auto-grow
                                        hide-details
                                        placeholder="Describe the service you're looking to purchase - please be as detailed as possible"></v-textarea>
                        </v-col>
                        <v-col cols="12">
                            <v-file-input
                                label="Attach a File"
                                hide-details
                                filled
                            ></v-file-input>
                        </v-col>
                        <v-col cols="12" class="d-flex justify-end pt-1">
                            <v-row>
                                <v-col cols="6">
                                    <v-select
                                        label="Category"
                                        :items="categories"
                                        item-value="id"
                                        item-text="title"
                                        v-model="job.category_id"
                                        @change="categoryChanged($event)"
                                    >
                                    </v-select>
                                </v-col>
                                <v-col cols="6">
                                    <v-select
                                        label="Sub Category"
                                        :items="sub_categories"
                                        item-value="id"
                                        item-text="title"
                                        v-model="job.sub_category_id"
                                    >

                                    </v-select>
                                </v-col>
                            </v-row>
                        </v-col>
                        <v-col cols="12" class="py-1">

                        </v-col>
                    </v-row>
                </v-col>
                <v-col cols="4">
                    <v-row>
                        <v-col cols="12">
                            <v-text-field
                            label="Delivery"
                            v-model="job.delivery"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row class="pt-3">
                        <v-col cols="12">
                            <v-text-field
                                label="Price"
                                v-model="job.price"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions class="d-flex justify-end px-2">
            <v-btn text color="info" @click="close">Close</v-btn>
            <v-btn depressed color="success" @click="done">Create Job</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
    import axios from "axios";

    export default {
        name: "createJob",
        data () {
            return {
                categories: [],
                sub_categories: [],
                job: {descriptions: '', category_id: '', sub_category_id: '', delivery: '', price: ''}
            }
        },
        props: {
            card    : Object,
            board   : Object
        },
        created() {
            this.getCategories();
        },
        methods: {
            done(){
                let self = this;
                this.job['card'] = this.card;
                axios.post(api.path('gig.save_job'), this.job)
                    .then((response)=>{
                        self.$emit('done', this.card);
                    });
            },
            close(){
                this.$emit('close');
            },
            getCategories: function() {
                let _this = this;
                axios.get(api.path('gig.categories'))
                    .then((response)=>{
                        _this.categories = response.data;
                    });
            },
            categoryChanged: function(event) {
                if(this.job.category_id) {
                    let _this = this;
                    axios.get(api.path('gig.sub_categories', {id: this.job.category_id}))
                        .then((response)=>{
                            _this.sub_categories = response.data;
                        });
                }
            },
        }
    }
</script>

<style scoped>

</style>
