<template>
    <div>
        <v-col col="12" v-if="selectedProducts.length > 0  && showAttachedData">
            <v-list>
                <h1>Products</h1>
                <template v-for="(crntProduct, index) in selectedProducts">
                    <v-list-item
                            class="blue lighten-5 my-1 border-ra rounded"
                            :key="index">
                        <v-list-item-content>

                            <v-list-item-title>
                                <a target="_blank" :href="productUrl(crntProduct)">
                                <v-row dense>
                                    <v-col cols="2">
                                        <v-img :src="(crntProduct.image !=null ? crntProduct.image.src : '/images/no-image.png')" height="50" contain></v-img>
                                    </v-col>
                                    <v-col cols="8 pt-4" style="white-space: normal;text-align: center;">
                                        <h4>{{crntProduct.title}}</h4>
                                    </v-col>
                                    <v-col cols="2 d-flex justify-end pt-4">
                                        {{activeVariantPrice(crntProduct)}}
                                    </v-col>
                                </v-row>
                                </a>
                            </v-list-item-title>
                            <v-list-item-subtitle style="text-align: center;">
                                {{extraInfo(crntProduct)}}
                            </v-list-item-subtitle>
                        </v-list-item-content>

                        <v-list-item-action>
                            <v-btn text fab x-small>
                                <v-btn icon small  @click="deleteProductResource(crntProduct)">
                                    <v-icon>delete</v-icon>
                                </v-btn>
                            </v-btn>
                        </v-list-item-action>
                    </v-list-item>
                </template>
            </v-list>

        </v-col>
        <v-col col="12" v-if="selectedOrders.length > 0  && showAttachedData">
            <v-list>
                <h1>Orders</h1>
                <v-data-table
                        :headers="headers"
                        :items="selectedOrders"
                        item-key="name"
                        :single-expand="singleExpand"
                        class="elevation-1 blue lighten-5 my-1 border-ra rounded"
                        :expanded.sync="expanded"
                        show-expand
                >
                    <template v-slot:item.created_at="{ item }">
                        <td class="text-xs-center">{{ dateFormat(item)}}</td>
                    </template>
                    <template v-slot:item.action="{ item }">
                        <v-btn text fab x-small>
                            <v-btn icon small  @click="deleteOrderResource(item)">
                                <v-icon>delete</v-icon>
                            </v-btn>
                        </v-btn>
                    </template>
                    <template v-slot:expanded-item="{ headers, item }">
                        <td :colspan="headers.length" v-html="extraOrderInfo(item)"  class="extraInfo">

                        </td>
                    </template>
                </v-data-table>
            </v-list>
        </v-col>
    </div>
</template>

<script>
    import {mapState, mapMutations, mapGetters} from 'vuex'
    import moment from "moment";
    export default {
        name: 'shopify-product-order-preview',
        data() {
            return {
                expanded: [],
                singleExpand: false,
                headers: [
                    {
                        text: 'Order',
                        align: 'center',
                        sortable: false,
                        value: 'name',
                    },
                    { text: 'Date',align: 'center',value: 'created_at', },
                    { text: 'Email', value: 'email', align: 'center' },
                    { text: 'Action', value: 'action'},
                    { text: '', value: 'data-table-expand' },
                ],
            }
        },
        props: {
            showAttachedData: Boolean,
            selectedOrders : Array,
            selectedProducts : Array,
            card: Object,
        },
        computed: {
            ...mapGetters({
                shop: 'auth/shop'
            }),
        },
        components: {
        },
        methods: {
            dateFormat(item){
                let info = '';
                if (item !== null && item !== undefined) {
                    info = moment(item.created_at).format('MMM D,YYYY');
                }
                return info;
            },
            productUrl(product) {
                let productUrl = '';
                productUrl = 'https://' + this.shop + '/products/'+ product.handle;
                return productUrl;
            },
            // get active variant price. active variant position 1.
            activeVariantPrice(crntProduct){
                let price = 0;
                let activeVariant = _.find(crntProduct.variants, {'position': 1});
                if (activeVariant !== undefined && activeVariant !== null) {
                    price = activeVariant.price;
                }
                return price;
            },
            extraOrderInfo(crntOrderItem) {
                let info = '';
                if (crntOrderItem != null) {
                    info = '<b>Customer Name : </b>' + crntOrderItem.customer.first_name + ' ' + crntOrderItem.customer.last_name +
                    '<b> Total Price : </b>' + crntOrderItem.total_price +
                    '<b> Total Items : </b>' + crntOrderItem.line_items.length + '<b> item</b>';
                }
                return info;
            },
            extraInfo(crntProduct){
                let self = this;
                let info = '';
                let totalVariant = crntProduct.variants.length;
                let totalInventory = 0;
                $.each(crntProduct.variants, function (index, currentElement) {
                    totalInventory += self.checkVariantStock(currentElement);
                });

                info = totalInventory + " in stock for " + totalVariant + " variants";
                return info;
            },
            checkVariantStock (variant) {
                let quantity = 0;
                if ((variant['inventory_management'] == null) || (variant['inventory_management'] !== 'shopify'))  quantity += variant['inventory_quantity'];
                if (variant["inventory_management"] == 'shopify') {
                    if (variant["inventory_policy"] == 'continue') {
                        quantity += variant['inventory_quantity']
                    }
                    if (variant["inventory_policy"] == 'deny') {
                        if (variant['inventory_quantity'] != null && variant["inventory_quantity"] > 0) {
                            quantity += variant['inventory_quantity']
                        }
                    }
                }
                return quantity;
            },
            // delete product resource.
            deleteProductResource(resource){
                let self = this;
                axios.delete(api.path('shopifyIntegration.delete', {id: resource.db_id})).then(function (res) {
                    if (res.data.status == 'success') {
                        let indexId = _.findIndex(self.selectedProducts, function (o) {
                            return o.db_id == resource.db_id;
                        });
                        self.selectedProducts.splice(indexId, 1);
                        _.remove(self.card.shopify_integrations, {
                            'db_id': resource.db_id
                        });
                        self.showSnack('Attached resource deleted');
                    }
                });
            },
            // delete order resource.
            deleteOrderResource(resource){
                let self = this;
                axios.delete(api.path('shopifyIntegration.delete', {id: resource.db_id})).then(function (res) {
                    if (res.data.status == 'success') {
                        let indexId = _.findIndex(self.selectedOrders, function (o) {
                            return o.db_id == resource.db_id;
                        });
                        self.selectedOrders.splice(indexId, 1);
                        _.remove(self.card.shopify_integrations, {
                            'db_id': resource.db_id
                        });
                        self.showSnack('Attached resource deleted');
                    }
                });
            },
        },
    }
</script>
