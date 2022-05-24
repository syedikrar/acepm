<template>
    <v-card flat>
        <v-row flat>
            <v-col class="text-h4 pt-6 pl-10" cols="12">
                Requirements Questions
            </v-col>
        </v-row>

        <v-card-text class="mt-4">
            <v-row flat class="mx-0 mb-4">
                <v-col cols="12" v-if="gig && gig.questions.length > 0">
                    <v-textarea
                        v-for="(question,q) in gig.questions"
                        :key="q"
                        v-model="question.answer"
                        clearable
                        clear-icon="mdi-close-circle"
                        :label="question.question"
                    ></v-textarea>
                </v-col>
                <v-col v-else>
                    <p>There is no requirements questions for this gig!</p>
                </v-col>
                <v-btn class="mt-3"
                       @click="saveOrder"
                       depressed
                       color="success">Continue</v-btn>
            </v-row>
        </v-card-text>


    </v-card>
</template>

<script>
    export default {
        name: "order",
        data(){
            return {
                gig         : null,
                package: null,
            }
        },
        created() {
            this.getGig();
            this.package = this.$route.params.package
        },
        methods: {
            getGig: function() {
                let self = this;
                axios.get(api.path('gig.single', {id: self.$route.params.id}))
                    .then((response)=>{
                        self.gig = response.data;
                    });
            },
            saveOrder: function() {
                let self = this;

                axios.post(api.path('gig.save_order'),{package: self.package, gig: self.gig})
                    .then((response)=>{
                        console.log(response)
                    });
            }
        }
    }
</script>

<style scoped>

</style>
