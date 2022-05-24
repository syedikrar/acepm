<template>
    <v-container
        id="contracts"
        fluid
        tag="section"
    >
        <base-v-component
            heading="My Contract"
            subheading="All contracts."
        />

        <base-material-card
            icon="group"
            title="Contracts"
            class="px-5 py-3"
        >
            <v-toolbar flat>
                <v-spacer></v-spacer>
                <v-btn text outlined>Invite a Member</v-btn>
            </v-toolbar>
            <v-layout>
                <v-row>
                    <v-col cols="12">
                        <v-data-table
                            :headers="headers"
                            :items="contracts"
                            :items-per-page="10"
                            class="br-4"
                        >
                            <template v-slot:item.gig_id="{ item }">
                                {{ item.gig.title }}
                            </template>
                            <template v-slot:item.package_id="{ item }">
                                {{ item.package.title }}
                            </template>
                            <template v-slot:item.seller="{ item }">
                                {{ item.gig.user.name }}
                            </template>
                            <template v-slot:item.action="{ item }">
                                <confirmable :target="item" :name="'Contract'" @delete="deleteContract">
                                    <v-btn icon text color="red">
                                        <v-icon>delete_outline</v-icon>
                                    </v-btn>
                                </confirmable>
                            </template>
                        </v-data-table>
                    </v-col>
                </v-row>
            </v-layout>
        </base-material-card>
    </v-container>
</template>

<script>
    import axios from "axios";
    import Confirmable from "../../shared/confirmable";

    export default {
        name: "contracts",
        data: () => ({
            busy        : false,
            headers     : [
                { text: 'Gig',     value: 'gig_id' },
                { text: 'Package',    value: 'package_id' },
                { text: 'Seller',    value: 'seller' },
                { text: 'Created', value: 'created_at' },
                {
                    text: 'Actions',
                    align: 'center',
                    value: 'action',
                    width: 200
                }
            ],
            contracts: []
        }),
        components: {
            Confirmable
        },
        created() {
            this.getContracts();
        },
        methods: {
            getContracts: function() {
                let self = this;
                axios.get(api.path('gig.contracts'))
                    .then((response)=>{
                        self.contracts = response.data;
                    });
            },
            deleteContract: function(contract){
                let self = this;
                axios
                    .delete(api.path('gig.contract_delete', {id: contract.id}), contract)
                    .then(function(resp){
                        self.showSnack('Contract removed.');
                        self.contracts.splice(self.contracts.indexOf(contract), 1);
                    });
            }
        }
    }
</script>

<style scoped>

</style>
