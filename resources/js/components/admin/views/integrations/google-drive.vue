<template>
    <div>
        <v-col col="12" v-if="showGoogleDriveFiles && selectedFiles.length > 0">
            <v-list>
                <h1>Google Drive Files</h1>
                <template v-for="(crntFile, index) in selectedFiles">
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
        <v-btn text color="info" class="justify-center" @click="goToSocialLogin()" id="google_login" @dblclick="initGoogleDrive" v-if="attachGoogleDriveFiles && showGoogleLogin">login google</v-btn>
        <v-btn text color="info" class="justify-center" @click="onApiLoad()" v-if="attachGoogleDriveFiles && showGoogleDrive">Pick From Google Drive</v-btn>
    </div>
</template>

<script>
    import helper from '../../../../helpers/Helper';
    export default {
        name: 'google-drive',
        props: {
            attachGoogleDriveFiles : Boolean,
            showGoogleDriveFiles    : Boolean,
            selectedFiles : Array,
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
            // Use the API Loader script to load google.picker and gapi.auth.
            onApiLoad() {
                let self = this;
                //gapi.load('auth', {'callback': self.onAuthApiLoad});
                gapi.load('picker:client', {'callback': self.onPickerApiLoad});
            },
            onPickerApiLoad() {
                this.pickerApiLoaded = true;
                this.createPicker();
            },
            createPicker() {
                if (this.pickerApiLoaded && this.oauthToken) {
                    var view = new google.picker.View(google.picker.ViewId.DOCS);
                    this.picker = new google.picker.PickerBuilder()
                        .enableFeature(google.picker.Feature.NAV_HIDDEN)
                        .enableFeature(google.picker.Feature.MULTISELECT_ENABLED)
                        .setAppId(this.appId)
                        .setOAuthToken(this.oauthToken)
                        .addView(view)
                        .addView(new google.picker.DocsUploadView())
                        .setDeveloperKey(this.developerKey)
                        .setCallback(this.pickerCallback)
                        .build();
                    this.picker.setVisible(true);
                }
            },

            // A simple callback implementation.
            pickerCallback(data) {
                let self = this;
                var doc = "";
                var fileID = "";
                self.currentlySelectedFiles = [];
                if (data[google.picker.Response.ACTION] == google.picker.Action.PICKED) {
                    var type = "anyone";
                    var role = "reader";
                    for(var i = 0; i < data[google.picker.Response.DOCUMENTS].length; i++){
                        doc = data[google.picker.Response.DOCUMENTS][i];
                        //change the file permissions to share with anyone with the link
                        fileID = doc.id;

                        let indexId = _.findIndex(self.selectedFiles, function (o) {
                            return o.id == fileID;
                        });
                        if (indexId == undefined || indexId == -1) {
                            self.currentlySelectedFiles.push(doc);
                        }
                        var request1 = window.gapi.client.request({
                            'path': '/drive/v3/files/' + fileID + '/permissions',
                            'method': 'POST',
                            'headers': {
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + self.oauthToken
                            },
                            'body':{
                                'role': role,
                                'type': type
                            }
                        });
                        request1.execute(function(resp) {
                        });
                    }
                    self.picker.dispose();
                    this.attachData();
                }
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
                    axios.post(api.path('googleDriveIntegration.save'), newResourceData).then(function(resp){
                        if (resp.data.googleDriveIntegration) {
                            $.each(resp.data.googleDriveIntegration, function (index, currentElement) {
                                self.selectedFiles.push(currentElement);
                            });
                            self.callBack();
                        }
                    });

                }
            },
            // delete google drive data.
            deleteOrderResource(resource){
                let self = this;
                axios.delete(api.path('googleDriveIntegration.delete', {id: resource.db_id})).then(function (res) {
                    if (res.data.status == 'success') {
                        let indexId = _.findIndex(self.selectedFiles, function (o) {
                            return o.db_id == resource.db_id;
                        });
                        self.selectedFiles.splice(indexId, 1);
                        self.showSnack('Attached file deleted');
                    }
                });
            },
            callBack(){
                let self = this;
                if ( self.selectedFiles.length > 0) {
                    self.$emit('updated-files', self.selectedFiles);
                }
            },
            listing(){
                let self = this;
                if (this.card !=null){
                    axios.get(api.path('googleDriveIntegration.getSavedDataByCardId', {id: this.card.id})).then(function(resp){
                        if (resp.data.status) {
                            self.$emit('updated-files', resp.data.data);
                            if (resp.data.token_data != null) {
                                let tokenData = JSON.parse(resp.data.token_data);
                                if (tokenData.access_token != null) {
                                    self.showGoogleDrive = true;
                                    self.showGoogleLogin = false;
                                    self.oauthToken =  tokenData.access_token;
                                }
                            }

                        }
                    });
                }
            },
            goToSocialLogin(provider) {
                let self = this;
                axios.post(api.path('socialLogin.socialLogin'), {"provider": 'google'})
                    .then(res => {
                        document.domain= helper.getDomain();
                        window.open(res.data.url, "_blank", "location=0,menubar=0,toolbar=no,scrollbars=yes,resizable=yes,width=600,height=450");

                    })
                    .catch(err => {
                        //NProgress.done();
                       // this.handleErrors(err.response.data.errors)
                    })
                    .then(() => {
                        // NProgress.done();
                    })
            },
            initGoogleDrive(e){
                let self = this;
                if (e.token != null) {
                    self.showGoogleDrive = true;
                    self.showGoogleLogin = false;
                    self.oauthToken =  e.token.access_token;
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