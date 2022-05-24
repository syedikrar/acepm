<template>
    <div id="gigs">
        <v-container class="grey lighten-5" fluid>
            <v-row>
                <v-col>
                    <v-btn
                        depressed
                        color="primary"
                        :to="{name: 'seller.gigs.new'}"
                    >
                        Create A New Gig
                    </v-btn>
                </v-col>
            </v-row>
            <Listing></Listing>
        </v-container>
    </div>
</template>

<script>
    import Listing from './listing';

    export default {
        name: "dashboard",
        data() {
            return {}
        },
        components: {
          Listing
        }
    }
</script>
