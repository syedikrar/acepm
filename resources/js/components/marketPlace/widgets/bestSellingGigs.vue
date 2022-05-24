<template>
    <v-card
            class="mx-auto pa-1"
            max-width="500"
    >
        <v-toolbar
                color="indigo"
                dark
        >
            <v-toolbar-title>Gigs of the Week</v-toolbar-title>
        </v-toolbar>

        <v-row dense>
            <v-col
                    v-for="(card,index) in cards"
                    :key="card.title"
                    :cols="card.flex"
            >
                <v-card>
                    <v-img
                            :src="card.src"
                            class="white--text align-end"
                            gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                            height="200px"
                    >
<!--                        <p style="right: 4px; position: absolute;" >{{index+1}}</p>-->

                        <v-card-title>{{card.title}} </v-card-title>
                    </v-img>

                    <v-card-actions>
                        <v-row
                                align="center"
                                class="mx-0"
                        >
                            <v-rating
                                    :value="card.rating"
                                    color="amber"
                                    dense
                                    half-increments
                                    readonly
                                    size="14"
                            ></v-rating>

                            <div class="grey--text ml-4">
                                {{card.rating}} ({{card.total}})
                            </div>
                        </v-row>
                        <v-spacer></v-spacer>

                        <v-tooltip bottom>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn :to="{name: 'marketPlace.gigs.single',  params: {id: card.id}}" small>
                                    <v-icon
                                            color="primary"
                                            dark
                                            v-bind="attrs"
                                            v-on="on"
                                    >
                                        mdi-eye
                                    </v-icon>
                                </v-btn>

                            </template>
                            <span>View Gig</span>
                        </v-tooltip>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
    export default {
        data: () => ({
            cards: [
                { id: 4, title: 'Voice Over Pro', src: 'https://cdn.vuetifyjs.com/images/cards/house.jpg', flex: 12 , rating: 5, total: 15},
                { id: 3, title: 'Facebook Marketing Expert', src: 'https://cdn.vuetifyjs.com/images/cards/road.jpg', flex: 6 , rating: 4, total: 10},
                { id: 2, title: 'Advertisements Creator', src: 'https://cdn.vuetifyjs.com/images/cards/plane.jpg', flex: 6 , rating: 3, total: 13},
            ],
        }),
        methods: {

        },
        mounted() {
            let self = this;
        }
    }
</script>