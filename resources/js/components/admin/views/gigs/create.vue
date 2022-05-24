<template>
    <div id="new-gig">

        <v-container class="grey lighten-5">
            <v-row
                justify="center"
            >
                <v-col md="12">
                    <v-stepper v-model="e1">
                        <v-stepper-header>
                            <v-stepper-step
                                :complete="e1 > 1"
                                step="1"
                            >
                                Overview
                            </v-stepper-step>

                            <v-divider></v-divider>

                            <v-stepper-step
                                :complete="e1 > 2"
                                step="2"
                            >
                                Pricing
                            </v-stepper-step>

                            <v-divider></v-divider>

                            <v-stepper-step
                                :complete="e1 > 3"
                                step="3"
                            >
                                Descriptions
                            </v-stepper-step>

                            <v-divider></v-divider>

                            <v-stepper-step
                                :complete="e1 > 4"
                                step="4"
                            >
                                Requirements
                            </v-stepper-step>

                            <v-divider></v-divider>

                            <v-stepper-step
                                step="5"
                                :complete="e1 > 5"
                            >
                                Gallery
                            </v-stepper-step>

                            <v-divider></v-divider>

                            <v-stepper-step
                                :complete="e1 > 6"
                                step="6">
                                Publish
                            </v-stepper-step>

                        </v-stepper-header>

                        <v-stepper-items>
                            <v-stepper-content step="1">
                                <v-card
                                    class="mx-auto"
                                    elevation="0"
                                >
                                    <v-form

                                    >
                                        <ValidationProvider
                                            name="Gig Title"
                                            ref="validate1"
                                            rules="required"
                                            v-slot="{ errors }">
                                        <v-textarea
                                            clearable
                                            clear-icon="mdi-close-circle"
                                            label="GIG TITLE"
                                            v-model="gig.title"
                                            :error-messages="errors"
                                            required
                                        ></v-textarea>
                                        </ValidationProvider>
                                        <v-row>
                                            <v-col cols="6">
                                                <v-select
                                                    label="Category"
                                                    :items="categories"
                                                    item-value="id"
                                                    item-text="title"
                                                    v-model="gig.category_id"
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
                                                    v-model="gig.sub_category_id"
                                                >

                                                </v-select>
                                            </v-col>
                                        </v-row>
                                        <v-combobox multiple
                                                    v-model="gig.search_terms"
                                                    label="SEARCH TAGS"
                                                    append-icon
                                                    chips
                                                    deletable-chips
                                                    class="tag-input"
                                                    :search-input.sync="search"
                                                    @keyup.tab="updateTags"
                                                    @paste="updateTags">
                                        </v-combobox>
                                    </v-form>
                                </v-card>

                                <div class="text-right">
                                    <v-btn
                                        color="primary"
                                        @click="e1 = 2"
                                    >
                                        Continue
                                    </v-btn>

                                    <v-btn text disabled>
                                        Back
                                    </v-btn>
                                </div>
                            </v-stepper-content>

                            <v-stepper-content step="2">
                                <v-card
                                    class="mx-auto"
                                    elevation="0"
                                >
                                    <v-simple-table dense>
                                        <template v-slot:default>
                                            <thead>
                                            <tr class="grey lighten-4">
                                                <th class="pa-md-4 py-md-6 text-left" width="185px">

                                                </th>
                                                <th class="pa-md-4 py-md-6 text-left">
                                                    BASIC
                                                </th>
                                                <th class="pa-md-4 py-md-6 text-left">
                                                    STANDARD
                                                </th>
                                                <th class="pa-md-4 py-md-6 text-left">
                                                    PREMIUM
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td width="185px" class="pa-md-4 py-md-6"></td>
                                                    <td class="pa-md-4 py-md-6" v-for="(pkg,p) in gig.packages" :key="p">
                                                        <v-text-field
                                                            label="Name your package"
                                                            required
                                                            class="pa-md-4"
                                                            v-model="pkg.title"
                                                        ></v-text-field>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="185px" class="pa-md-4 py-md-8"></td>
                                                    <td class="pa-md-4 py-md-4" v-for="(pkg,p) in gig.packages" :key="p">
                                                        <v-textarea
                                                            name="input-7-1"
                                                            v-model="pkg.details"
                                                            filled
                                                            label="Describe the details of your offering"
                                                            auto-grow
                                                        ></v-textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="185px" class="pa-md-4"></td>
                                                    <td class="pa-md-4" v-for="(pkg,p) in gig.packages" :key="p">
                                                        <v-select
                                                            v-model="pkg.delivery"
                                                            label="Delivery Time"
                                                            solo
                                                            item-value="value"
                                                            item-text="text"
                                                            :items="delivery_times"
                                                        ></v-select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="185px" class="pa-md-4">
                                                        Revisions
                                                    </td>
                                                    <td class="py-md-4" v-for="(pkg,p) in gig.packages" :key="p">
                                                        <v-select
                                                            label="Select"
                                                            solo
                                                            v-model="pkg.revisions"
                                                            :items="revisions"
                                                        ></v-select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="185px" class="pa-md-4">
                                                        Price
                                                    </td>
                                                    <td class="py-md-4" v-for="(pkg,p) in gig.packages" :key="p">
                                                        <v-text-field
                                                            v-model="pkg.price"
                                                        >
                                                            <v-icon
                                                                slot="prepend"
                                                                color="green"
                                                            >
                                                                mdi-currency-usd
                                                            </v-icon>
                                                        </v-text-field>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </template>
                                    </v-simple-table>
                                </v-card>

                                <div class="text-right">
                                    <v-btn
                                        color="primary"
                                        @click="e1 = 3"
                                    >
                                        Continue
                                    </v-btn>

                                    <v-btn text
                                           @click="e1 = 1">
                                        Back
                                    </v-btn>
                                </div>

                            </v-stepper-content>

                            <v-stepper-content step="3">
                                <v-card
                                    class="mx-auto"
                                    elevation="0"
                                >
                                    <p class="pb-2">Briefly Describe Your Gig</p>
                                    <vue-editor v-model="gig.descriptions"></vue-editor>
                                </v-card>

                                <div class="text-right">
                                    <v-btn
                                        color="primary"
                                        @click="e1 = 4"
                                    >
                                        Continue
                                    </v-btn>

                                    <v-btn text
                                           @click="e1 = 2">
                                        Back
                                    </v-btn>
                                </div>

                            </v-stepper-content>

                            <v-stepper-content step="4">
                                <v-card
                                    class="mx-auto"
                                    elevation="0"
                                >
                                    <p class="pb-2">Add questions to help buyers provide you with exactly what you need to start working on their order.

                                    </p>
                                    <v-btn
                                        tile
                                        color="secondary"
                                        @click="newQuestion = !newQuestion"
                                    >
                                        <v-icon left>
                                            mdi-plus
                                        </v-icon>
                                        Add New Question
                                    </v-btn>

                                    <v-card outlined v-if="newQuestion">
                                        <v-card-title>Add a question</v-card-title>
                                        <v-card-text>
                                            <v-textarea
                                                v-model="question.question"
                                                counter
                                                label="Question"
                                                :rules="rules"
                                            ></v-textarea>
                                            <v-checkbox
                                                v-model="question.required"
                                                label="Required"
                                            ></v-checkbox>
                                        </v-card-text>
                                        <v-card-actions>
                                            <v-btn text
                                                   @click="newQuestion = false"
                                            >
                                                Cancel
                                            </v-btn>
                                            <v-btn
                                                color="primary"
                                                @click="addQuestion"
                                            >
                                                Add
                                            </v-btn>
                                        </v-card-actions>
                                    </v-card>
                                    <div v-else>
                                        <v-card
                                            outlined
                                            v-for="(question,q) in gig.questions" :key="q">
                                            <v-card-title>
                                                <span>Question</span>

                                                <v-spacer></v-spacer>
                                                <v-menu
                                                    bottom
                                                    left
                                                >
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-btn
                                                            icon
                                                            v-bind="attrs"
                                                            v-on="on"
                                                        >
                                                            <v-icon>mdi-dots-vertical</v-icon>
                                                        </v-btn>
                                                    </template>

                                                    <v-list>
                                                        <v-list-item
                                                        >
                                                            <v-list-item-title>Edit</v-list-item-title>
                                                        </v-list-item>
                                                        <v-list-item
                                                        >
                                                            <v-list-item-title>Remove</v-list-item-title>
                                                        </v-list-item>
                                                    </v-list>
                                                </v-menu>
                                            </v-card-title>
                                            <v-card-text class="mt-2">
                                                <strong>{{ question.question }}</strong>
                                            </v-card-text>
                                        </v-card>
                                    </div>

                                </v-card>

                                <div class="text-right">
                                    <v-btn
                                        color="primary"
                                        @click="e1 = 5"
                                    >
                                        Continue
                                    </v-btn>

                                    <v-btn text
                                           @click="e1 = 3"
                                    >
                                        Back
                                    </v-btn>
                                </div>

                            </v-stepper-content>

                            <v-stepper-content step="5">
                                <v-card
                                    class="mx-auto"
                                    elevation="0"
                                >
                                    <p class="pb-2">Add memorable content to your gallery to set yourself apart from competitors.</p>
                                    <ImagePicker v-model="images" :activeImageUploads="activeImageUploads">
                                        <v-flex xs4 md3>
                                            <img src="https://uploads.codesandbox.io/uploads/user/6f394a82-63cd-47a3-9c77-c8556683e44b/eGHz-placeholder-img.jpg" width="100%" height="100%">
                                        </v-flex>
                                    </ImagePicker>
                                </v-card>

                                <div class="text-right">
                                    <v-btn
                                        color="primary"
                                        @click="e1 = 6"
                                    >
                                        Continue
                                    </v-btn>

                                    <v-btn text
                                           @click="e1 = 4">
                                        Back
                                    </v-btn>
                                </div>

                            </v-stepper-content>

                            <v-stepper-content step="6">
                                <v-card
                                    class="mx-auto"
                                    elevation="0"
                                >
                                    <h2>Almost there...</h2>
                                    <p class="pb-2">Let's publish your Gig and get some buyers rolling in.</p>

                                </v-card>

                                <div class="text-right">
                                    <v-btn
                                        color="primary"
                                        @click="publishGig"
                                    >
                                        Publish
                                    </v-btn>

                                    <v-btn text
                                           @click="e1 = 5">
                                        Back
                                    </v-btn>
                                </div>

                            </v-stepper-content>

                        </v-stepper-items>
                    </v-stepper>
                </v-col>
            </v-row>
        </v-container>

    </div>
</template>

<script>
    import {VueEditor} from "vue2-editor";
    import { ImagePicker, imageUploadingStates } from "@nagoos/vue-image-picker"
    import { required, digits, email, max, regex } from 'vee-validate/dist/rules'
    import { extend, ValidationObserver, ValidationProvider, setInteractionMode } from 'vee-validate'

    extend('required', {
        ...required,
        message: '{_field_} can not be empty',
    })


    export default {
        name: "create",
        data () {
            return {
                e1: 1,
                rules: [v => v.length <= 400 || 'Max 400 characters'],
                newQuestion: false,
                question: {question: '', required: true},
                activeImageUploads: {},
                images: [],
                categories: [],
                sub_categories: [],
                delivery_times: [
                    {value: 1, text: '1 day Delivery'},
                    {value: 2, text: '2 day Delivery'},
                    {value: 3, text: '3 day Delivery'},
                    {value: 4, text: '4 day Delivery'},
                    {value: 5, text: '5 day Delivery'},
                ],
                revisions: [1,2,3,4,5,6,7,8,9,10],
                gig: {
                    title: '',
                    category_id: '',
                    sub_category_id: '',
                    search_terms: '',
                    packages: [
                        {title: '', details: '', delivery: '', revisions: '', price: ''},
                        {title: '', details: '', delivery: '', revisions: '', price: ''},
                        {title: '', details: '', delivery: '', revisions: '', price: ''}
                        ],
                    descriptions: '',
                    questions: [],
                }
            }
        },
        components: {VueEditor, ImagePicker, ValidationProvider, ValidationObserver},
        created() {
          this.getCategories();
        },
        methods: {
            updateTags() {
                this.$nextTick(() => {
                    this.select.push(...this.search.split(","));
                    this.$nextTick(() => {
                        this.search = "";
                    });
                });
            },
            getCategories: function() {
                let _this = this;
                axios.get(api.path('gig.categories'))
                    .then((response)=>{
                        _this.categories = response.data;
                    });
            },
            categoryChanged: function(event) {
                if(this.gig.category_id) {
                    let _this = this;
                    axios.get(api.path('gig.sub_categories', {id: this.gig.category_id}))
                        .then((response)=>{
                            _this.sub_categories = response.data;
                        });
                }
            },
            addQuestion: function() {
                this.gig.questions.push(this.question);
                this.question = {question: '', required: true};
                this.newQuestion = false;
            },
            publishGig: function () {
                const formData = new FormData();
                formData.append('gig',JSON.stringify(this.gig));

                // formData.append('yinyang.png', this.images[0].imageFile);
                this.images.map(function(value, key) {
                    formData.append('images[]',value.imageFile)
                });


                axios.post(api.path('gig.save'),formData)
                .then((response)=>{
                    console.log(response)
                });
            }
        }
    }
</script>

<style scoped>

</style>
