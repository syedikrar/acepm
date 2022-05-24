<template>
    <v-list subheader>
        <v-alert color="primary" outlined border="left" dense class="py-3"
                 v-if="items.length == 0">No Top members at the moment.</v-alert>
        <v-list-item
            v-for="item in items"
            :key="item.title"
            @click=""
        >
            <v-list-item-avatar>
                <v-img :src="item.avatar"></v-img>
            </v-list-item-avatar>

            <v-list-item-content>
                <v-list-item-title v-text="item.name"></v-list-item-title>
            </v-list-item-content>

            <v-list-item-icon>
                {{ item.experience }} Points
                <v-icon :color="true ? 'cyan' : 'grey'">assessment</v-icon>

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


                axios.get('/api/top/members')
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
            //console.log(this.$data)

        }
    }
</script>

<style>

</style>
