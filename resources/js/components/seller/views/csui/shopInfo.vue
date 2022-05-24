<template>
    <v-card>
        <v-toolbar color="info" dark dense flat>
            <v-toolbar-title class="font-weight-light title">Shopify Details</v-toolbar-title>
        </v-toolbar>
        <v-card-text style="height: 640px;" class="slimScroll overflow-y-auto py-0">
            <v-list dense>
                <template v-for="(val, key, indx) in info">
                    <v-list-item :key="indx">
                        <v-list-item-title style="flex: auto">
                            <span class="text-capitalize">{{key.replace(/_/g, ' ')}}</span>
                        </v-list-item-title>
                        <v-list-item-action>
                            <b>{{val}}</b>
                        </v-list-item-action>
                    </v-list-item>
                    <hr v-if="indx < Object.keys(info).length"/>
                </template>
            </v-list>
        </v-card-text>
    </v-card>
</template>

<script>
    export default {
        name: 'shopInfo',
        data() {
            return {
                info: {}
            };
        },
        methods: {
            start(){
                let self = this;
                self.busy = true;
            },
            get(){
                let self = this;
                self.start();
                axios.get(api.path('integrity.shopJson')+'?csui-shop='+self.searchKey).then(function (resp) {
                    Vue.set(self, 'info', resp.data);
                    self.done();
                }).catch(function (error) {
                    self.done();
                });
            },
            done(){
                let self = this;
                self.busy = false;
            },
            shouldShow: function(key){
                let self = this;
                return ((self.searchKey == '' && self.hidden.indexOf(key) == -1) || self.searchKey != '') && key != 'id';
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
                this.get();
            }
        },
        mounted() {
            let self = this;
            self.get();
        }
    }

</script>
