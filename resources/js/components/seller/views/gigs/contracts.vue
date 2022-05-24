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
                            <template v-slot:item.buyer="{ item }">
                                {{ item.user.name }}
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

    export default {
        name: "contracts",
        data: () => ({
            busy        : false,
            headers     : [
                { text: 'Gig',     value: 'gig_id' },
                { text: 'Package',    value: 'package_id' },
                { text: 'Buyer',    value: 'buyer' },
                { text: 'Created', value: 'created_at' },
            ],
            contracts: []
        }),
        created() {
            this.getContracts();
        },
        methods: {
            getContracts: function() {
                let self = this;
                axios.get(api.path('gig.seller_contracts'))
                    .then((response)=>{
                        self.contracts = response.data;
                    });
            },
        }
    }
</script>

<style scoped>

</style>
