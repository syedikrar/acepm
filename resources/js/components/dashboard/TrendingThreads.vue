<template>
    <v-list subheader>
        <v-alert color="primary" outlined border="left" dense class="py-3"
                 v-if="items.length == 0">No trending threads at the moment.</v-alert>
        <v-list-item
            v-for="item in items"
            :key="item.title"
            @click=""
        >

            <v-list-item-content>
                <v-list-item-title v-text="item.title"></v-list-item-title>
            </v-list-item-content>

            <v-list-item-icon>
                {{ item.visits }}
                <v-icon :color="true ? 'teal' : 'grey'">visibility</v-icon>
            </v-list-item-icon>

            <v-list-item-icon>
                {{ item.totalReplies }}
                <v-icon :color="true ? 'teal' : 'grey'">chat</v-icon>
            </v-list-item-icon>

        </v-list-item>
    </v-list>
</template>
<script>

    export default {


        data() {
            return {

                items: [],

            }
        },

        methods: {
            initialize () {

                var self= this

                axios.get('/api/trending/threads')
                    .then(function (response) {

                        self.items = response.data

                    })
                    .catch(function (error) {

                    })
                    .finally(function () {
                        // always executed
                    });

            },

        },
        created() {

            this.initialize()

        }
    }
</script>

<style>

</style>
