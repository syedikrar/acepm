<template>
    <v-data-table
        :headers="headers"
        :items="items"
        :no-data-text="busy ? 'Loading ...' : 'No '+formatted+'(s) defined yet.'"
        :search="search"
        :loading="busy"
    >
        <template v-slot:top>
            <v-toolbar flat dense class="settingListToolbar">
                <div class="flex-grow-1"></div>
                <v-flex class="md4 lg3">
                <v-text-field
                    v-model="search"
                    append-icon="search"
                    label="Search"
                    single-line
                    hide-details
                    class="mr-2"
                ></v-text-field>
                </v-flex>
                <v-dialog v-model="dialog" max-width="700px">
                    <template v-slot:activator="{ on }">
                        <v-btn color="info" depressed class="mb-2" v-on="on" @click="initEditedItem">
                            Add New {{formatted}}
                        </v-btn>
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="headline">{{editedItem.id != null ? 'Edit' : 'New'}} {{formatted}}</span>
                        </v-card-title>

                        <v-card-text>
                            <v-container>
                                <v-row>
                                    <v-col cols="12" :sm="longText ? 12 : 6">
                                        <v-text-field outlined v-model="editedItem.key" hide-details label="Key"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" :sm="longText ? 12 : 6">
                                        <v-text-field v-if="!longText" outlined v-model="editedItem.value" label="Property Value"></v-text-field>
                                        <v-textarea
                                            v-else
                                            outlined
                                            v-model="editedItem.value"
                                            label="Property Value"
                                        ></v-textarea>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-card-text>

                        <v-card-actions>
                            <div class="flex-grow-1"></div>
                            <v-btn outlined color="accent" @click="close" :width="100">Cancel</v-btn>
                            <v-btn depressed color="success" @click="save()" :width="100">Save</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-toolbar>
        </template>
        <template v-slot:item.key="{ item }">
            <b>{{item.key}}</b>
        </template>
        <template v-slot:item.action="{ item }">
            <v-btn icon small color="accent" @click="editItem(item)">
                <v-icon>edit</v-icon>
            </v-btn>
            <v-btn icon small color="error" @click="deleteItem(item)">
                <v-icon>delete_outline</v-icon>
            </v-btn>
        </template>
    </v-data-table>
</template>

<script>

    import axios from 'axios'
    import { api } from '~/config'
    import confirmable from "./confirmable";

    export default {
        name: 'settingList',
        data() {
            return {
                search      : '',
                headers: [
                    {
                        text: 'Key',
                        align: 'left',
                        value: 'key',
                        width: 200
                    },
                    {
                        text: 'Value',
                        align: 'left',
                        value: 'value',
                    },
                    {
                        text: 'Action',
                        align: 'center',
                        value: 'action',
                        width: 200
                    }
                ],
                busy    : false,
                dialog  : false,
                items   : [],
                editedIndex : -1,
                editedItem  : {
                    id              : null,
                    relation_id     : null,
                    area            : '',
                    key             : '',
                    value           : ''
                },
                defaultItem : {
                    id              : null,
                    relation_id     : null,
                    area            : '',
                    key             : '',
                    value           : ''
                }
            }
        },
        props: {
            entity      : String,
            relationId  : Number,
            area        : String
        },
        components: {
            'confirmable'    : confirmable
        },
        watch: {
            dialog (val) {
                val || this.close()
            },
            relationId: function(){
                this.pullData();
            },
            area: function(){
                this.pullData();
            }
        },
        computed: {
            formatted: function(){
                return this.area.replace('-', ' ');
            },
            longText: function(){
                return this.area == 'product-description'
            }
        },
        methods: {
            initEditedItem(){
                this.editedItem.relation_id = this.relationId;
                this.editedItem.area = this.area;
            },
            pullData(){
                let self = this;
                self.busy = true;

                if (self.relationId == null || self.area == null) {
                    self.busy = false;
                    self.items = [];
                    return true;
                }

                let path = api.path(self.entity+'.get',
                    {
                        relationId  : self.relationId,
                        area        : self.area
                    }
                );

                axios
                    .get(path)
                    .then(function(resp){
                        self.busy = false;
                        self.items = resp.data;
                    });
            },
            save (mode) {
                let self = this;
                if (self.editedItem.key == '' || self.editedItem.value == '') {
                    self.close();
                    return true;
                }

                self.busy = true;
                axios
                    .patch(api.path(self.entity+'.save'), self.editedItem)
                    .then(function(resp){
                        self.busy = false;
                        self.editedItem.id = resp.data.data.id;
                        if (self.editedIndex > -1) {
                            Object.assign(self.items[self.editedIndex], self.editedItem)
                        } else {
                            self.items.push(self.editedItem)
                        }
                        self.close();
                    });
            },
            deleteItem(item){
                let self = this;
                let index = self.items.indexOf(item);
                if (item.id != null) {
                    self.busy = true;
                    axios
                        .delete(api.path(self.entity+'.delete', {'id': item.id}))
                        .then(function(resp){
                            self.items.splice(index, 1);
                            self.busy = false
                        });
                } else {
                    self.items.splice(index, 1);
                }
            },
            editItem (item, mode) {
                this.editedIndex = this.items.indexOf(item);
                this.editedItem = Object.assign({}, item);
                this.dialog = true;
            },
            close () {
                this.dialog = false
                setTimeout(() => {
                    this.editedItem = Object.assign({}, this.defaultItem)
                    this.editedIndex = -1
                }, 300);
            }
        },
        mounted() {
            this.pullData();
        }
    }
</script>
