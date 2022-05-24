<template>
    <v-card flat>
        <v-row flat>
            <v-col class="text-h4 pt-6 pl-10" cols="12">
                Single Gig Title here
            </v-col>
        </v-row>
        <v-card-text>
            <v-row align="center"
                   class="mx-0">
                sfs
            </v-row>
        </v-card-text>
    </v-card>
</template>

<script>
    export default {
        name: "single",
        data(){
            return {
                gig         : null,
            }
        },
        created() {
            this.getGig();
        },
        methods: {
            getGig: function() {
                let self = this;
                axios.get(api.path('gig.single', {id: self.$route.params.id}))
                    .then((response)=>{

                    });
            }
        }

    }
</script>