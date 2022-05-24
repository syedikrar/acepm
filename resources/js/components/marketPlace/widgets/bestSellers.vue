<template>
    <v-card
            class="mx-auto pa-1"
            max-width="500"
    >
        <v-toolbar
                color="indigo"
                dark
        >
            <v-toolbar-title>Best Sellers of the Week</v-toolbar-title>
        </v-toolbar>
        <v-timeline>
            <v-timeline-item
                    v-for="(user,index) in users"
                    :key="index"
                    large
                    link
                    :to="{name: 'seller.dashboard'}"
            >
                <template v-slot:icon>
                    <v-avatar>
                        <img :src="user.profile_picture">
                    </v-avatar>
                </template>
                <template v-slot:opposite>
                    <span>{{user.education.title}}</span>
                    <v-rating
                            :value="3.5"
                            color="amber"
                            dense
                            half-increments
                            readonly
                            size="14"
                    ></v-rating>
                    <div class="grey--text ml-1">3.5</div>
                </template>
                <v-card class="elevation-2">
                    <v-card-title class="headline">
                        {{user.name}}
                    </v-card-title>
                    <v-card-text>
                        {{user.description}}
                    </v-card-text>
                </v-card>
            </v-timeline-item>
        </v-timeline>
    </v-card>
</template>

<script>
    export default {
        name: "bestSellers",
        data() {
            return {
                users: []
            }
        },
        methods: {
            get(){
                let self = this;
                axios.get(api.path('users.bestSellers'))
                    .then((response)=>{
                        let data = response.data.users;
                        // data.forEach( function (item, index){
                        //
                        // });
                        self.users = response.data.users;

                    });
            }
        },
        mounted() {
            let self = this;
            self.get();
        }
    }
</script>