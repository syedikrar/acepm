<template>
    <div>
        <v-col col="12" v-if="showDropboxFiles && dropboxSelectedFiles.length > 0">
            <v-list>
                <h1>Dropbox Files</h1>
                <template v-for="(crntFile, index) in dropboxSelectedFiles">
                    <v-list-item
                            class="blue lighten-5 my-1 border-ra rounded"
                            :key="index">
                        <v-list-item-content>

                            <v-list-item-title>
                                <a target="_blank" :href="crntFile.url">
                                    <v-row dense>
                                        <v-col cols="2">
                                            <v-img :src="crntFile.iconUrl" height="40" contain></v-img>
                                        </v-col>
                                        <v-col cols="7 pt-4" style="white-space: normal;text-align: center;">
                                            <h4>{{crntFile.name}}</h4>
                                        </v-col>
                                        <v-col cols="3 pt-4" style="white-space: normal;text-align: center;">
                                            <h4>{{crntFile.type}}</h4>
                                        </v-col>
                                    </v-row>
                                </a>
                            </v-list-item-title>
                            <!--<v-list-item-subtitle style="text-align: center;">-->
                                <!--{{extraInfo(crntFile)}}-->
                            <!--</v-list-item-subtitle>-->
                        </v-list-item-content>

                        <v-list-item-action>
                            <v-btn text fab x-small>
                                <v-btn icon small  @click="deleteOrderResource(crntFile)">
                                    <v-icon>delete</v-icon>
                                </v-btn>
                            </v-btn>
                        </v-list-item-action>
                    </v-list-item>
                </template>
            </v-list>

        </v-col>
        <v-btn text color="info" class="justify-center" @click="dropBoxChooser()" v-if="attachDropboxFiles">Pick From dropbox</v-btn>
    </div>
</template>

<script>
    import helper from '../../../../helpers/Helper';
    export default {
        name: 'google-drive',
        props: {
            attachDropboxFiles : Boolean,
            showDropboxFiles    : Boolean,
            dropboxSelectedFiles : Array,
            card    : Object,
        },
        data() {

            return {
                developerKey : process.env.MIX_GOOGLE_DEVELOPER_KEY,
                appId : process.env.MIX_GOOGLE_APP_ID,
                scope : ['https://www.googleapis.com/auth/drive.file'],
                pickerApiLoaded : false,
                oauthToken : '',
                picker : '',
                currentlySelectedFiles : [],
                showGoogleDrive : false,
                showGoogleLogin : true,
            }
        },

        components: {
        },
        methods: {
            dropBoxChooser(){
                let self = this;
                Dropbox.choose({
                    success: function(files) {
                        //files is list of selected files (array)

                        if(files.length < 0){
                            return;
                        }
                        self.pickerCallback(files);
                    },
                    cancel: function() {
                        //here is code when user close chooser
                    },
                    linkType: "preview", //Direct = Download link
                    multiselect: true, //Allow to select one file
                    extensions: [], //Allow to choose PDF file only
                    folderselect: false, //Not allow to select folder
                    //sizeLimit: 1024 * 1024 * 2 //File size limit (2MB)
                });

            },
            // A simple callback implementation.
            pickerCallback(data) {
                let self = this;
                self.currentlySelectedFiles = [];
                    for(var i = 0; i < data.length; i++){
                        let doc = data[i];
                        //change the file permissions to share with anyone with the link
                        let  fileID = doc.id;
                        //change the file permissions to share with anyone with the link
                        let indexId = _.findIndex(self.dropboxSelectedFiles, function (o) {
                            return o.id == fileID;
                        });
                        if (indexId == undefined || indexId == -1) {
                            self.currentlySelectedFiles.push(doc);
                        }
                    }
                    this.attachData();
            },
            // save google drive files data
            attachData(){
                let self = this;
                self.showAttachedData = true;
                if (self.currentlySelectedFiles.length > 0) {
                    let newResourceData = {
                        id              : null,
                        card_id         : self.card.id,
                        filesData       : JSON.stringify(self.currentlySelectedFiles)
                    };
                    axios.post(api.path('dropBoxIntegration.save'), newResourceData).then(function(resp){
                        if (resp.data.dropboxIntegration) {
                            $.each(resp.data.dropboxIntegration, function (index, currentElement) {
                                self.dropboxSelectedFiles.push(currentElement);
                            });
                            self.callBack();
                        }
                    });

                }
            },
            // delete google drive data.
            deleteOrderResource(resource){
                let self = this;
                axios.delete(api.path('dropBoxIntegration.delete', {id: resource.db_id})).then(function (res) {
                    if (res.data.status == 'success') {
                        let indexId = _.findIndex(self.dropboxSelectedFiles, function (o) {
                            return o.db_id == resource.db_id;
                        });
                        self.dropboxSelectedFiles.splice(indexId, 1);
                        self.showSnack('Attached file deleted');
                    }
                });
            },
            callBack(){
                let self = this;
                if ( self.dropboxSelectedFiles.length > 0) {
                    self.$emit('updated-files', self.dropboxSelectedFiles, 'dropbox');
                }
            },
            listing(){
                let self = this;
                if (this.card !=null){
                    axios.get(api.path('dropBoxIntegration.getSavedDataByCardId', {id: this.card.id})).then(function(resp){
                        if (resp.data.status) {
                            self.$emit('updated-files', resp.data.data,'dropbox');
                        }
                    });
                }
            },
        },
        mounted() {

        },
        watch: {
            card: {
                immediate: true,
                handler (val, oldVal) {
                    this.listing();
                }
            },
        }
    }
</script>