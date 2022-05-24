<template>
    <v-list subheader>
        <v-alert color="primary" outlined border="left" dense class="py-3"
        v-if="items.length == 0">No inactive threads at the moment.</v-alert>
        <v-subheader v-if="items.length != 0">Today's Total: {{items.length}}</v-subheader>
        <v-list-item
            v-if="items.length > 0"
            v-for="item in items"
            :key="item.title"
            @click=""
        >
            <v-list-item-content>
                <v-list-item-title v-text="item.title"></v-list-item-title>
            </v-list-item-content>

            <!--                        <v-list-item-icon>-->
            <!--                            {{ item.visits }}-->
            <!--                            <v-icon :color="true ? 'teal' : 'grey'">visibility</v-icon>-->
            <!--                        </v-list-item-icon>-->

            <v-list-item-icon>
                0 {{ item.totalReplies }}
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

                var self = this;

                this.$root.$emit('loading', true);

                axios.get('/api/threads/with/no/replies')
                    .then(function (response) {

                        self.items = response.data

                        self.$root.$emit('loading', false);

                    })
                    .catch(function (error) {

                    })
                    .finally(function () {
                        self.$root.$emit('loading', false);
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
