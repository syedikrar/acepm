<template>
    <v-card :elevation="0">
        <v-toolbar color="secondary" dark dense flat>
            <v-toolbar-title class="font-weight-light title">
                Upsell Campaigns
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-dialog v-model="newCampaign" max-width="900px" scrollable hide-overlay persistent>
                <template v-slot:activator="{ on }">
                    <v-btn color="primary" v-on="on">+ Campaign</v-btn>
                </template>
                <v-card>
                    <v-card-title>
                        <span class="headline">New Campaign</span>
                    </v-card-title>
                    <v-card-text style="height: 500px;" class="slimScroll">
                        <v-container class="px-0 pb-0">
                            <v-row dense>
                                <v-col cols="6">
                                    <v-select
                                            label="Users"
                                            :items="plans"
                                            item-text="name"
                                            item-value="val"
                                            :clearable="true"
                                            v-model="campaign.plan"
                                            outlined
                                            multiple
                                            @input="defaultPlan"
                                    ></v-select>
                                </v-col>
                                <v-col cols="6">
                                    <v-select
                                            label="Select campaign type"
                                            :items="types"
                                            item-text="name"
                                            item-value="val"
                                            :clearable="true"
                                            v-model="campaign.type"
                                            outlined
                                    ></v-select>
                                </v-col>

                                <v-col cols="6">
                                    <v-text-field
                                            v-model="campaign.title"
                                            label="Campaign Title"
                                            outlined
                                            counter
                                            maxlength="30"
                                            :rules="required"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="6">
                                    <v-textarea
                                            v-model="campaign.message"
                                            label="Campaign Message"
                                            outlined
                                            counter
                                            maxlength="500"
                                            height="140px"
                                            no-resize
                                            @input="setPreview"
                                            :rules="required"
                                    ></v-textarea>
                                </v-col>
                                <!--      Not part of campaign object      -->
                                <v-col cols="9">
                                    <v-textarea
                                            :value="preview"
                                            label="Preview Campaign Message"
                                            outlined
                                            counter
                                            maxlength="500"
                                            height="140px"
                                            no-resize
                                            readonly
                                    ></v-textarea>
                                </v-col>
                                <v-col cols="3" style="font-size: small; color: red;">
                                    <span style="color: green;">Available WildCards</span><br>
                                    {days_installed}<br>
                                    {current_plan}<br>{higher_plan}
                                </v-col>
                                <!--           end            -->
                                <v-col cols="6">
                                    <v-text-field
                                            v-model="campaign.repeat_after"
                                            label="Days after to re-display campaign"
                                            outlined
                                            counter
                                            type="number"
                                            min="1"
                                            :rules="numberRequired"
                                    ></v-text-field>
                                </v-col>

                                <v-col cols="6">
                                    <v-text-field
                                            v-model="campaign.max_tries"
                                            label="Max times to show campaign"
                                            outlined
                                            counter
                                            type="number"
                                            min="1"
                                            :rules="numberRequired"
                                    ></v-text-field>
                                </v-col>

                                <v-col cols="6">
                                    <v-text-field
                                            v-model="formattedCampaignRange"
                                            label="Campaign start - expiry date"
                                            clearable
                                            readonly
                                            hide-details
                                            dense
                                            outlined
                                            :rules="required"
                                    ></v-text-field>
                                    <VueCtkDateTimePicker
                                            format="MM-DD-YY"
                                            v-model="campaign.campaign_range"
                                            :range="true"
                                            :only-date="true"
                                            :dark="rangeDark"
                                            position="bottom"
                                            :min-date="today"
                                            :no-value-to-custom-elem="true"
                                    >
                                        <v-btn text fab color="accent" class="mt-1">
                                            <v-icon>mdi-calendar-month-outline</v-icon>
                                        </v-btn>
                                    </VueCtkDateTimePicker>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn outlined color="accent" @click="closePop" :width="70">Cancel</v-btn>
                        <v-btn depressed color="success" :disabled="cantSave" @click="save" :width="70" :loading="busy">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-toolbar>
        <v-card-text class="pa-0">
            <v-data-table
                    :headers="headers"
                    :items="list"
                    class="elevation-1"
                    item-key="id"
                    :loading="busy"
                    :sort-by="['created_at']"
                    :sort-desc="[true]"
                    show-expand
                    no-data-text="No Campaigns found."
            >
                <template v-slot:item.delete="{ item }">
                    <confirmable :target="item" :name="'Upsell Campaign'" @delete="deleteCampaign(item.id)">
                        <v-btn text icon color="red lighten-1">
                            <v-icon size="20px">mdi-delete-outline</v-icon>
                        </v-btn>
                    </confirmable>
                </template>
                <template v-slot:item.plan="{ item }">
                    <v-chip v-show="item.plan != null" v-for="name in JSON.parse(item.plan)" :key="name"
                            class="ma-2"
                            color="secondary"
                    >
                        {{name}}
                    </v-chip>
                    <v-chip v-show="item.plan == null"
                            class="ma-2"
                            color="secondary"
                    >
                        all
                    </v-chip>

                </template>
                <template v-slot:item.campaign_starts="{ item }">
                    <span :title="localTime(item.campaign_starts).format('DD/MM/YYYY hh:mm A')">{{ localTime(item.campaign_starts).fromNow() }}</span>
                </template>
                <template v-slot:item.campaign_expires="{ item }">
                    <span :title="localTime(item.campaign_expires).format('DD/MM/YYYY hh:mm A')">{{ localTime(item.campaign_expires).fromNow() }}</span>
                </template>
                <template v-slot:item.status="{ item }">
                    <v-switch
                            v-model="item.status"
                            color="success"
                            class="mt-n1"
                            hide-details
                            @change="toggleStatus(item)"
                    ></v-switch>
                </template>
                <template v-slot:expanded-item="{ headers, item }">
                    <td :colspan="headers.length" class="statDetails">
                        <div>{{item.message}}</div>
                    </td>
                </template>
            </v-data-table>
        </v-card-text>
    </v-card>
</template>

<script>
    import Confirmable from "../../shared/confirmable";
    import Helper from "../../../../helpers/Helper";
    import 'vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css';

    export default {
        name: 'upsellCampaign',
        data() {
            return {
                busy        : false,
                list        : [],
                preview     : '',
                cantSave    : true,
                required    : [
                    value => !!value.trim() || 'This field is required',
                ],
                numberRequired: [value => (!isNaN(parseFloat(value)) && value > 0 && !!value) ||
                    'Only positive numbers are accepted'],
                headers     : [
                    { text: 'Title', value: 'title'},
                    { text: 'Plan', value: 'plan'},
                    { text: 'Type', value: 'type'},
                    { text: 'Starts On', value: 'campaign_starts', width: 150},
                    { text: 'Expires On', value: 'campaign_expires', width: 150},
                    { text: 'Status', value: 'status'},
                    { text: 'Impressions', value: 'impressions'},
                    { text: 'Conversions', value: 'conversions'},
                    { text: 'Delete', value: 'delete', sortable: false, width: 40 },
                ],
                plans   : [
                    {'name': 'All', 'val': 'all'},
                    {'name': 'Freemium', 'val': 'basic'},
                    {'name': 'Enterprise', 'val': 'enterprise'}
                ],
                types: [
                    {name: 'Review', val: 'review'},
                    {name: 'Upsell', val: 'upsell'}
                ],
                newCampaign  : false,
                defaultCampaign: {
                    'plan'           : ['all'],
                    'type'           : 'upsell',
                    'title'          : '',
                    'message'        : '',
                    'repeat_after'   : 1,
                    'max_tries'      : 3,
                    'campaign_range' : {start: null, end: null},
                },
                campaign     : {
                    'plan'           : ['all'],
                    'type'           : 'upsell',
                    'title'          : '',
                    'message'        : '',
                    'repeat_after'   : 1,
                    'max_tries'      : 3,
                    'campaign_range' : {start: null, end: null},
                },
                today       : moment().format('YYYY-MM-DD'),
                rangeDark   : true,
            }
        },
        watch:{
            'globals.dark': function(){
                let self = this;
                self.rangeDark = self.globals.dark;
            },
            campaign :{
                handler(val){
                    let self = this;
                    if(self.campaign.title.trim() != '' && self.campaign.message.trim() != '' &&
                        self.campaign.campaign_range.start != null && self.campaign.campaign_range.end != null &&
                        self.campaign.max_tries > 0 && self.campaign.repeat_after > 0
                    ) self.cantSave = false;
                    else self.cantSave = true;
                },
                deep: true
            }
        },
        mounted(){
            this.get();
        },

        components: {
            Confirmable,
        },
        methods: {
            get(){
                let self = this;
                axios.get(api.path('upsellCampaign.get')).then(function(response){
                    self.list = response.data.campaigns;
                });
            },
            setPreview(){
                let self = this;
                if(self.campaign.message.trim() == '' || self.campaign.message == null) return;

                let plan = self.billing.plan.toUpperCase();
                let obj = {
                    '{current_plan}'   : (plan == 'BASIC') ? 'FREEMIUM' : plan,
                    '{higher_plan}'    : Helper.getHigherPlan(plan),
                    '{days_installed}' : Helper.daysInstalled(self.user)
                };

                self.preview = Helper.replaceAll(self.campaign.message, obj);
            },
            deleteCampaign(id){
                let self = this;
                self.busy = true;
                axios
                    .delete(api.path('upsellCampaign.delete', {id: id}))
                    .then(function(resp){
                        self.busy = false;
                        self.showSnack(resp.data);
                        self.get();
                    });
            },
            closePop: function(){
                this.newCampaign = false;
                this.campaign = _.cloneDeep(this.defaultCampaign);
                this.preview = '';
            },
            save: function(){
                let self = this;
                self.busy = true;
                axios.patch(api.path('upsellCampaign.save'), { 'data': self.campaign}).then(function(response){
                    self.showSnack(response.data);
                    self.busy = false;
                    self.closePop();
                    self.get();
                });
            },
            defaultPlan: function(){
                let self = this;
                if (self.campaign.plan.length == 0) self.campaign.plan.push('all');
            },
            toggleStatus: function (item) {
                let self = this;
                self.busy = true;
                axios.patch(api.path('upsellCampaign.toggle'), item).then(function(response){
                    self.busy = false;
                });
            }
        },
        created() {
            let self = this;
            self.rangeDark = self.globals.dark;
        },
        computed: {
            formattedCampaignRange: {
                get: function(){
                    return this.campaign.campaign_range.start != null || this.campaign.campaign_range.end != null ?
                        (this.campaign.campaign_range.start + ' -- ' + this.campaign.campaign_range.end) : '';
                },
                set: function(){
                    return '';
                }
            },

        }
    }
</script>
