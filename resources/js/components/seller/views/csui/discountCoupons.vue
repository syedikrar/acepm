<template>
    <v-card :elevation="0">
        <v-toolbar color="indigo" dark dense flat>
            <v-toolbar-title class="font-weight-light title">
                Discount Coupons
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-dialog v-model="popup" max-width="400px" persistent>
                <template v-slot:activator="{ on }">
                    <v-btn class="mr-2" color="primary" v-on="on">+ Coupon Code</v-btn>
                </template>
                <v-card>
                    <v-card-title>
                        <span class="headline">Create Discount Coupon</span>
                    </v-card-title>
                    <v-card-text class="slimScroll">
                        <v-container class="px-0 pb-0">
                            <v-row dense>
                                <v-col cols="12">
                                    <v-select
                                            v-model="selectedItem.percentage"
                                            label="Discount percentage"
                                            :items="percentages"
                                            item-text="name"
                                            item-value="val"
                                            outlined
                                    ></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                            v-model="selectedItem.shop"
                                            label="Shop Name"
                                            outlined
                                            :rules="required"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn outlined color="accent" @click="popup = false;" :width="70">Cancel</v-btn>
                        <v-btn depressed color="success" :disabled="selectedItem.shop == null || selectedItem.percentage == null" @click="persist('granted')" :width="70">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <span style="width: 300px;">
                <v-text-field
                        v-model="search"
                        append-icon="mdi-account-search"
                        label="Search"
                        hide-details
                        outlined
                        dense>
            </v-text-field>
            </span>
        </v-toolbar>
        <v-card-text class="pa-0">
            <v-data-table
                    :headers="headers"
                    :items="list"
                    class="elevation-1"
                    item-key="id"
                    :loading="busy"
                    show-expand
                    :search="search"
            >
                <template v-slot:item.status="{ item }">
                    <span class="text-capitalize">{{ item.status }}</span>
                </template>
                <template v-slot:item.requested_on="{ item }">
                    <span v-if="item.requested_on != null" :title="localTime(item.requested_on).format('DD/MM/YYYY hh:mm A')">{{ localTime(item.requested_on).fromNow() }}</span>
                </template>
                <template v-slot:item.availed_on="{ item }">
                    <span v-if="item.availed_on != null" :title="localTime(item.availed_on).format('DD/MM/YYYY hh:mm A')">{{ localTime(item.availed_on).fromNow() }}</span>
                </template>
                <template v-slot:item.grant="{ item }">
                    <v-btn v-show="item.status != 'availed'" text icon small color="success" @click="grant(item, 'granted')">
                        <v-icon>mdi-check-decagram</v-icon>
                    </v-btn>
                </template>
                <template v-slot:item.deny="{ item }">
                    <v-btn v-show="item.status != 'availed'" icon small text color="error" @click="grant(item, 'declined')">
                        <v-icon>mdi-close-outline</v-icon>
                    </v-btn>
                </template>
                <template v-slot:item.delete="{ item }">
                    <confirmable :target="item" :name="'Discount Coupon'" @delete="deleteCoupon(item.id)">
                        <v-btn v-show="item.status != 'availed'" text icon color="red lighten-1">
                            <v-icon size="20px">mdi-delete-outline</v-icon>
                        </v-btn>
                    </confirmable>
                </template>
            </v-data-table>
        </v-card-text>
    </v-card>

</template>

<script>
    import confirmable from "../../shared/confirmable";

    export default {
        name: 'discountCoupons',
        data() {
            return {
                busy        : false,
                list        : [],
                headers     : [
                    { text: 'Shop Name', value: 'shop_name' },
                    { text: 'Discount Coupon', value: 'coupon' },
                    { text: 'Percentage', value: 'percentage' },
                    { text: 'Requested On', value: 'requested_on', width: 180 },
                    { text: 'Availed On', value: 'availed_on', width: 180 },
                    { text: 'Status', value: 'status', width: 100 },
                    { text: 'Grant', value: 'grant', sortable: false, width: 100 },
                    { text: 'Deny', value: 'deny', sortable: false, width: 100 },
                    { text: 'Delete', value: 'delete', sortable: false, width: 100 }
                ],
                percentages: [
                    {name: '10 percent', val: 10},
                    {name: '20 percent', val: 20},
                    {name: '30 percent', val: 30},
                    {name: '40 percent', val: 40},
                    {name: '50 percent', val: 50}
                ],
                selectedItem: {shop: null, percentage: null},
                required: [
                    value => !!value || 'This field is required',
                ],
                // detailFields: ['details', 'fb_email', 'gmail', 'user_name'],
                popup: false,
                search: null,
            }
        },
        mounted(){
            this.get();
        },
        components: {
            'confirmable' : confirmable
        },
        methods: {
            start(){
                let self = this;
                self.busy = true;
            },
            get(){
                let self = this;
                self.start();
                axios.get(api.path('discountCoupon.get')).then(function(response){
                    self.list = response.data['discount-coupons'];
                    self.done();
                });
            },
            done(){
                let self = this;
                self.busy = false;
                self.popup = false;
            },
            grant(record, decision) {
                let self = this;
                self.selectedItem['shop'] = record.shop_name;
                self.selectedItem['percentage'] = record.percentage;

                if(decision == 'granted') self.popup = true;
                else self.persist(decision);
            },
            deleteCoupon(couponId) {
                let self = this;
                self.start();
                axios.delete(api.path('discountCoupon.delete', {id: couponId})).then(function (res) {
                    self.done();
                    self.showSnack('Discount coupon request deleted successfully.');
                    self.get();
                });
            },
            persist(decision) {
                let self = this;
                self.start();
                self.busy = true;
                axios
                    .patch(api.path('discountCoupon.save')+'?csui-shop='+self.selectedItem.shop, {
                        'shop_name'  : self.selectedItem.shop,
                        'decision'   : decision,
                        'percentage' : self.selectedItem.percentage
                    })
                    .then(function(resp){
                        self.done();
                        self.showSnack('Discount coupon updated');
                        self.get();
                    });
            }
        }
    }
</script>