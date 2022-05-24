<template>
    <v-card>
        <v-toolbar color="#696cff" dark dense flat>
            <v-toolbar-title class="font-weight-light title">Health Check</v-toolbar-title>
        </v-toolbar>
        <v-list dense>
            <template v-for="(val, key, index) in integritySetting">
                <v-list-item>
                    <v-list-item-content>
                        <v-list-item-title v-text="key.replace(/_/g, ' ')" class="text-capitalize"></v-list-item-title>
                    </v-list-item-content>

                    <v-list-item-action :title="key">
                        <v-icon v-if="val == true " color="green">done</v-icon>
                        <v-icon v-if="val == false || val == null && key != 'checkout'" color="blue">mdi-check</v-icon>
                        <span v-if="key == 'plan' || key == 'theme_name' || key == 'shopify_plan'" class="text-capitalize">
                            <span v-if="key == 'plan'">{{val}}</span>
                            <span v-else>{{val.replace(/_/g, ' ')}}</span>
                        </span>
                        <span v-if="key == 'shop_created_date' || key == 'app_install_date'" :title="val"> {{ localTime(val).fromNow() }}</span>
                        <v-icon v-if="key == 'checkout' && val == null" color="blue">mdi-block-helper</v-icon>
                    </v-list-item-action>
                </v-list-item>

                <hr v-if="index+1 != Object.keys(integritySetting).length" />
            </template>
            <v-layout align-end justify-end>
                <v-btn
                        :loading="busy"
                        class="primary mr-2"
                        @click="handleFixResponse"
                        depressed
                >
                    Run
                </v-btn>
            </v-layout>

        </v-list>
    </v-card>
</template>

<script>
    import HookIntegrity from "../../../../helpers/HookIntegrity";

    export default {
        name: 'integrity',
        data() {
            return {
                busy: false,
                integritySetting: '',
                integrity: new HookIntegrity(),
            }
        },
        props: {
            searchKey: {
                type: String,
                default: ''
            }
        },
        watch:{
            searchKey: function () {
                this.handleCheckResponse();
            }
        },
        mounted(){
            this.handleCheckResponse();
        },
        methods: {
            start(){
                let self = this;
                self.busy = true;
            },
            handleCheckResponse(){
                let self = this;
                self.start();
                self.integrity.check(function(response){
                    self.done(response);
                }, self.searchKey);

            },
            handleFixResponse(){
                let self = this;
                self.start();
                self.integrity.fix(self.integritySetting ,function(response){
                    response['shop_created_date'] = self.integritySetting.shop_created_date;
                    self.showSnack("Integrity run successful");
                    self.done(response);
                }, self.searchKey);

            },
            done(response){
                let self = this;
                self.integritySetting = response;
                self.busy = false;
            }
        }
    }
</script>
