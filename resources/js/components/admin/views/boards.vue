<template>
    <v-container fluid>
        <v-card outlined>
            <v-toolbar color="cyan" dark flat>
                <v-toolbar-title class="capitalize">{{field}}</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-toolbar-items v-show="user.role == 'owner'">
                    <v-btn text color="white" :to="{name: 'ace_board',
                    params: {id: 0, mode: 'load', field: field}}">Start From Scratch</v-btn>
                </v-toolbar-items>

                <template v-slot:extension>
                    <v-tabs v-model="tab" align-with-title>
                        <v-tabs-slider color="yellow"></v-tabs-slider>
                        <v-tab>Saved Boards</v-tab>
                        <v-tab>Board Templates</v-tab>
                    </v-tabs>
                </template>

            </v-toolbar>
            <v-progress-linear indeterminate color="yellow darken-1"
                               absolute height="4" :active="loading"></v-progress-linear>

            <v-tabs-items v-model="tab">
                <v-tab-item>
                    <v-card flat class="px-3" id="boards">
                        <v-alert
                                v-model="alert"
                                dense
                                dark
                                outlined
                                type="error"
                                max-width="450"
                                dismissible
                        >
                            <v-row align="center">
                                <v-col class="grow">
                                    Board deleted successfully
                                </v-col>
                                <v-col class="shrink">
                                    <v-btn @click="restore">Undo</v-btn>
                                </v-col>
                            </v-row>
                        </v-alert>
                        <v-data-iterator :items="boards" hide-default-footer>
                            <template v-slot:default="props">
                                <v-row>
                                    <v-col
                                        v-for="board in props.items"
                                        :key="board.id"
                                        cols="12"
                                        sm="6"
                                        md="4">
                                        <v-card color="blue lighten-2" dark class="mt-0">
                                            <div class="d-flex flex-no-wrap justify-space-between">
                                                <div>
                                                    <v-card-title class="text-h6" v-text="board.name"></v-card-title>
                                                    <v-card-subtitle class="mt-1">
                                                        {{board.panes.length}} Stages,
                                                        {{board.cards.length}} Cards
                                                        <!--<span v-if="countSubTasks(board) > 0">, {{countSubTasks(board)}} Sub-tasks</span>-->
                                                    </v-card-subtitle>

                                                    <v-card-actions class="mt-5">
                                                        <v-btn class="ml-2"
                                                               :to="{name: 'ace_board', params: {id: board.id, mode: 'board', field: field}}"
                                                               rounded small color="success" depressed>
                                                            Open Board
                                                        </v-btn>
                                                        <confirmable :target="board" name="Board" @delete="deleteBoard">
                                                            <v-btn v-show="user.id == board.creator[0]['id']" fab x-small depressed dark color="pink lighten-1" class="ml-2">
                                                                <v-icon>mdi-delete-empty-outline</v-icon>
                                                            </v-btn>
                                                        </confirmable>
                                                    </v-card-actions>
                                                </div>

                                                <v-avatar class="ma-3" size="135" tile>
                                                    <v-row dense>
                                                        <v-col cols="12">
                                                            <v-icon size="90">{{board.field_type.icon}}</v-icon>
                                                        </v-col>
                                                    </v-row>
                                                </v-avatar>
                                            </div>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:no-data>
                                <v-row v-show="user.role == 'owner'">
                                    <v-col cols="12" class="d-flex justify-center">
                                        No boards created yet,
                                        <router-link class="px-1" :to="{name: 'ace_board', params: {id: 0, mode: 'load', field: field}}">start from scratch</router-link>
                                        instead ?
                                    </v-col>
                                </v-row>
                            </template>
                        </v-data-iterator>
                    </v-card>
                </v-tab-item>
                <v-tab-item>
                    <v-card flat class="px-3" id="templates">
                        <v-data-iterator :items="templates" hide-default-footer>
                            <template v-slot:default="props">
                                <v-row>
                                    <v-col
                                        v-for="template in props.items"
                                        :key="template.id"
                                        cols="12"
                                        sm="6"
                                        md="4">
                                        <v-card color="blue lighten-2" dark class="mt-0">
                                            <div class="d-flex flex-no-wrap justify-space-between">
                                                <div>
                                                    <v-card-title class="text-h6" v-text="template.name"></v-card-title>
                                                    <v-card-subtitle class="mt-1">
                                                        <div>by <span class="black--text">{{template.user.name}}</span></div>
                                                        {{template.content.panes.length}} Stages,
                                                        {{template.content.cards.length}} Cards
                                                        <span v-if="countSubTasks(template.content) > 0">, {{countSubTasks(template.content)}} Sub-tasks</span>
                                                    </v-card-subtitle>

                                                    <v-card-actions>
                                                        <v-btn class="ml-2 mt-5"
                                                               @click="useTemplate(template)"
                                                               :loading="template.busy"
                                                               rounded small color="success" depressed>
                                                            Use Template
                                                        </v-btn>
                                                    </v-card-actions>
                                                </div>

                                                <v-avatar class="ma-3 mt-5" size="135" tile>
                                                    <v-row dense>
                                                        <v-col cols="12">
                                                            <v-icon size="90">{{template.field_type.icon}}</v-icon>
                                                        </v-col>
                                                        <v-col cols="12">
                                                            <v-chip color="transparent" label text-color="white" title="Views" outlined>
                                                                <v-icon left>mdi-eye-outline</v-icon>
                                                                {{template.views}}
                                                            </v-chip>
                                                            <v-chip color="transparent" label text-color="white" title="Views" outlined>
                                                                <v-icon left>mdi-content-copy</v-icon>
                                                                {{template.installs}}
                                                            </v-chip>
                                                        </v-col>
                                                    </v-row>
                                                </v-avatar>
                                            </div>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:no-data>
                                <v-row v-show="user.role == 'owner'">
                                    <v-col cols="12" class="d-flex justify-center">
                                        No templates created yet,
                                        <router-link class="px-1" :to="{name: 'ace_board', params: {id: 0, mode: 'load', field: field}}">start from scratch</router-link>
                                        instead ?
                                    </v-col>
                                </v-row>
                            </template>
                        </v-data-iterator>
                    </v-card>
                </v-tab-item>
            </v-tabs-items>
        </v-card>
    </v-container>
</template>
<script>
    import axios from 'axios'
    import {api} from '~/api'
    import Vue from 'vue'
    import Confirmable from "../shared/confirmable";

    export default {
        components: {Confirmable},
        data(){
            return {
                tab         : null,
                templates   : [],
                boards      : [],
                deletedBoardId : null,
                alert : false,
                loading: false,
            }
        },
        props: {
            'field' : String
        },
        methods:{
            getTemplates(){
                let self = this;
                axios
                    .get(api.path('templates.byField', {'field': self.field}))
                    .then(function(resp){
                        self.templates = resp.data;
                    });
            },
            getBoards(){
                let self = this;
                axios
                    .get(api.path('boards.byField', {'field': self.field}))
                    .then(function(resp){
                        self.boards = resp.data;
                    });
            },
            countSubTasks(board){
                let count = 0;
                if ('cards' in board) {
                    board.cards.forEach(function(card){
                        count += card.sub_tasks.length;
                    })
                }
                return count;
            },
            useTemplate(template) {
                let self = this;
                Vue.set(template, 'busy', true);
                axios.post(api.path('boards.fromTemplate'), {'templateId': template.id})
                .then(function(resp){
                    self.boards.push(resp.data);
                    template.busy = false;
                    self.showSnack("Board created successfully from template.");
                    self.$router.push({name: 'ace_board', params: {id: resp.data.id, mode: 'board', field: self.field}});
                });
            },
            deleteBoard(board) {
                let self = this;

                Vue.set(board, 'busy', true);
                axios.delete(api.path('boards.delete', {'id': board.id})).then(function(resp){
                    let index = self.boards.findIndex(function (item) {
                        return item.id == board.id;
                    });
                    if(index != -1 )  self.boards.splice(index, 1);
                    board.busy = false;

                    self.deletedBoardId = board.id;
                    self.alert = true;
                })
            },
            restore(){
                let self = this;
                if(self.deletedBoardId == null) return;

                self.loading = true;
                axios.post(api.path('boards.restore', {'id': self.deletedBoardId})).then(function(resp){
                    self.showSnack(resp.data);
                    if(resp.data.status == 'success') self.boards.push(resp.data.board);

                    self.deletedBoardId = null;
                    self.alert = false;
                    self.loading = false;
                }).catch(function (error) {
                    console.info('%c --------------','color: #bada55', {error});
                    self.deletedBoardId = null;
                    self.loading = false;
                })
            }
        },
        mounted() {
            this.getTemplates();
            this.getBoards();
        }
    }
</script>
