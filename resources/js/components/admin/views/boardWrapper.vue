<template>
    <div id="ace_board" v-bind:style="{ backgroundImage : 'url(/' + board.background_image + ')' }">
        <div style="width: 99%" class="pl-5 mt-2">
            <v-toolbar :height="id == 0 ? 120 : 65" rounded flat :color="$vuetify.theme.dark ? 'black' : 'grey lighten-3'">
                <v-toolbar-title style="width: 60%">
                    <v-form @submit.prevent="syncBoard">
                        <v-text-field
                            :class="{'mt-1': true, 'text-h5': (id != 0), 'text-h3 kong-text' : (id == 0) }"
                            v-model="board.name"
                            hide-details
                            placeholder="Add Board Title ..."
                            label="Board Title"
                            autofocus
                            :disabled="boardBusy"
                            :height="id == 0 ? 80 : 45">
                            <template v-slot:append v-if="id == 0">
                                <v-icon size="50" color="info" class="mt-2" @click="syncBoard">
                                    mdi-content-save
                                </v-icon>
                            </template>
                        </v-text-field>
                    </v-form>
                </v-toolbar-title>
                <v-spacer></v-spacer>

                <v-toolbar-items>
                    <v-autocomplete
                            v-show="board.id != null && user.role=='owner'"
                            v-model="board.members"
                            :items="teamMembers"
                            chips
                            dense
                            class="pt-4"
                            color="blue-grey lighten-2"
                            label="Add Members"
                            item-text="name"
                            item-value="id"
                            return-object
                            :item-disabled="disabledItems"
                            multiple
                            @change="syncMembers"
                            :disabled="boardBusy"
                            clearable
                    >
                        <template v-slot:selection="data">
                            <v-chip
                                    v-bind="data.attrs"
                                    :input-value="data.selected"
                                    :title="data.item.name"
                            >
                                <v-avatar left>
                                    <v-img v-if="data.item.profile_picture != null" :src="data.item.profile_picture"></v-img>
                                    <v-icon v-else>mdi-account-circle</v-icon>
                                </v-avatar>
                            </v-chip>
                        </template>
                        <template v-slot:item="data">

                            <template v-if="typeof data.item !== 'object'">
                                <v-list-item-content v-text="data.item"></v-list-item-content>
                            </template>
                            <template v-else>
                                <v-list-item-avatar>
                                    <v-img v-if="data.item.profile_picture != null" :src="data.item.profile_picture"></v-img>
                                    <v-icon v-else>mdi-account-circle</v-icon>
                                </v-list-item-avatar>
                                <v-list-item-content>
                                    <v-list-item-title class="text-truncate" v-html="data.item.name"></v-list-item-title>
                                </v-list-item-content>
                            </template>
                        </template>
                    </v-autocomplete>
                    <div class="pt-4" v-show="user.role != 'owner'">
                        <v-chip
                                :title="member.name"
                                v-for="(member,i) in board.members" :key="i"
                        >
                            <v-avatar left>
                                <v-img v-if="member.profile_picture != null" :src="member.profile_picture"></v-img>
                                <v-icon v-else>mdi-account-circle</v-icon>
                            </v-avatar>
                        </v-chip>
                    </div>

                    <v-file-input
                            label="Background"
                            placeholder="Background"
                            v-model="attachment"
                            accept="image/*"
                            clearable
                            :loading="fileLoading"
                            :disabled="fileLoading"
                            @change="uploadFile"
                            class="pt-6 pr-4"
                    >
                    </v-file-input>

                    <v-dialog v-model="templatePop" width="800" persistent>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn v-show="user.role=='owner'" text color="success" v-bind="attrs" v-on="on">Save as Template</v-btn>
                        </template>
                        <v-card>
                            <v-card-title class="headline grey lighten-2">
                                Save as Template
                            </v-card-title>
                            <v-card-text>
                                <v-form>
                                    <v-row>
                                        <v-col cols="6">
                                            <v-text-field
                                                hide-details
                                                filled label="Template name" v-model="newTemplate.name"></v-text-field>
                                        </v-col>
                                        <v-col cols="6">
                                            <v-select
                                                v-model="newTemplate.field_type_id"
                                                :items="fieldTypes"
                                                item-text="name"
                                                item-value="id"
                                                label="Field"
                                                filled
                                                :menu-props="{ bottom: true, offsetY: true }"
                                            ></v-select>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-textarea
                                                hide-details
                                                filled label="Template Description" v-model="newTemplate.description"></v-textarea>
                                        </v-col>
                                    </v-row>
                                </v-form>
                            </v-card-text>

                            <v-divider></v-divider>

                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="secondary" text @click="templatePop = false">Cancel</v-btn>
                                <v-btn color="primary" text @click="saveTemplate"
                                       :disabled="!canSaveTemplate" :loading="templateBusy">Save Template</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-btn v-show="user.role=='owner'" text color="secondary">Hire Resources</v-btn>
                </v-toolbar-items>
                <v-progress-linear indeterminate color="info"
                   class="mt-15"
                   absolute height="3" :active="boardBusy"></v-progress-linear>
            </v-toolbar>
        </div>
        <board :board="board" @syncBoard="syncBoard" ref="boardComponent" v-if="board.id != null"></board>
    </div>
</template>
<script>
    import axios from 'axios'
    import {api} from '~/api'
    import Board from "./board"
    import {mapGetters} from "vuex";
    import Helper from "../../../helpers/Helper";

    export default {
        components: {Board},
        data (){
            return {
                name        : '',
                templatePop : false,
                newTemplate : {
                    id              : null,
                    user_id         : null,
                    name            : '',
                    background_image : '',
                    description     : '',
                    field_type_id   : null,
                    content         : ''
                },
                templateBusy    : false,
                boardBusy       : false,
                initWatchers    : true,
                fieldTypes      : [],
                board           : {
                    id              : null,
                    user_id         : null,
                    shop_id         : null,
                    field_type_id   : null,
                    background_image : '',
                    name            : '',
                    panes           : [],
                    cards           : [],
                    members         : []
                },
                teamMembers : [],
                attachment: null,
                fileLoading: false
            }
        },
        props: {
            id      : Number,
            mode    : String,
            field   : String
        },
        computed: {
            ...mapGetters({ user: 'auth/user' }),
            canSaveTemplate(){
                return this.newTemplate.name != '' &&
                    this.newTemplate.description != '' &&
                    this.newTemplate.field_type_id != null;
            },
            paneNames(){
                let names = '';
                this.board.panes.forEach(function(pane){ return names += pane.name; });
                return names;
            }
        },
        methods:{
            saveTemplate(){
                let self = this;
                self.templateBusy = true;
                let templateContent = {
                    cards : [ ...self.board.cards ],
                    panes : [ ...self.board.panes ]
                };
                self.newTemplate.content = templateContent;
                self.newTemplate.background_image = self.board.background_image,
                self.newTemplate.user_id = self.user.id;

                axios.post(api.path('templates.save'), this.newTemplate).then(function(response){
                    if (response.data.status == true) {
                        self.templateBusy = false;
                        self.templatePop = false;
                        self.showAlert('Template saved successfully, ' +
                            'will be available for usage once it gets approved by admins.')
                    }
                });

            },
            getFieldTypes(){
                let self = this;
                axios.get(api.path('integrity.fieldTypes')).then(function(resp){
                    self.fieldTypes = resp.data;
                    let targetType = _.find(self.fieldTypes, {slug: self.field});
                    self.newTemplate.field_type_id = targetType.id;
                    if (self.id == 0) {
                        self.board.field_type_id = targetType.id;
                        self.board.user_id = self.user.id;
                        self.board.shop_id = self.getShopId();
                    }
                });
            },
            getTemplate(id){
                let self = this;
                axios.get(api.path('templates.get', {id: id}))
                    .then(resp => {
                        self.name = 'Untitled Board';
                        self.board = resp.data.content;
                    });
            },
            getBoard(){
                let self = this;
                self.boardBusy = true;
                self.initWatchers = false;
                axios.get(api.path('boards.get', {id: self.id})).then(function(resp){
                    self.board = resp.data;
                    self.boardBusy = false;
                    self.selectedMembers = _.cloneDeep(self.board.members);
                    setTimeout(function(){ self.initWatchers = true; }, 2000);
                });
            },
            syncBoard(){
                let self = this;

                if (self.boardBusy) return true;
                self.boardBusy = true;

                axios.post(api.path('boards.save'), this.board).then(function(resp){
                    if (self.board.id == null) {
                        self.$router.push({
                            name: 'ace_board', params: {id: resp.data.board.id, mode: 'board', field: self.field}
                        })
                    }
                    self.boardBusy = false;
                });
            },
            getUsers(){
                let self = this;
                if(self.user.role != 'owner') return;

                axios.get(api.path("users")).then((res)=> {
                    self.teamMembers = res.data;
                }).catch((err) => {
                    console.info('%c --------------','color: #bada55', {err});
                });
            },
            disabledItems(item){
                let self = this;
                return item.id == self.board.creator[0]['id'];
            },
            syncMembers(){
                let self = this;

                const index = self.board.members.findIndex(member => member.id == self.board.creator[0]['id']);
                if(index == -1) self.board.members.push(self.board.creator[0]);

                self.board.selectedMembers = [];
                self.board.members.forEach(function (member, index) {
                    self.board.selectedMembers.push(member.id);
                });
                self.syncBoard();
            },
            uploadFile() {
                let self = this;
                if(self.attachment == null) {self.deleteFile(); return;}

                if (!self.attachment.type.includes('image')) {self.showSnack("Error, only image(png, jpg, jpeg) files are allowed"); return;}
                self.fileLoading = true;

                let formData = new FormData();
                formData.append("board_id", self.board.id);
                formData.append('background_image',self.attachment);

                axios.post(api.path('boards.backgroundImage'),
                    formData,
                    {
                        // onUploadProgress: function( progressEvent ) {
                        //     this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded / progressEvent.total ) * 100 ));
                        // }.bind(this)

                    }).then((res) => {
                    self.fileLoading = false;
                    self.board.background_image = res.data.background_image;

                    self.showSnack(res.data.message);
                })
                    .catch((error) => {
                        console.log({error});
                        self.fileLoading = false;
                    });

            },

            deleteFile() {
                let self = this;
                if(!confirm('Are sure to remove current background image?') || self.fileLoading) return;

                self.fileLoading = true;
                axios.delete(api.path('boards.deleteBackgroundImage', {'id': self.board.id}))
                    .then((res) => {
                        self.showSnack(res.data.message);
                        self.fileLoading = false;
                        self.board.background_image = "";
                    })
                    .catch((error) => {
                        console.log({error});
                        self.fileLoading = false;
                    });
            }
        },
        watch: {
            'board.name' : Helper.debounce(function(){
                let self = this;
                if (this.id != 0 && self.initWatchers) this.syncBoard();
            }, 1500),
            paneNames : Helper.debounce(function(){
                let self = this;
                if (this.id != 0 && self.initWatchers) this.syncBoard();
            }, 1500)
        },
        mounted() {
            let self = this;
            self.getFieldTypes();
            if (self.mode == 'template') self.getTemplate(self.id);
            else if (self.id != 0) {self.getBoard(); self.getUsers();}
        }
    }
</script>
