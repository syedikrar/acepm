<template>
    <v-container id="dashboard" fluid tag="section">
        <v-row>
            <v-col cols="12" sm="6" lg="4">
                <base-material-stats-card
                    color="info"
                    icon="mdi-dock-top"
                    title="Total Boards"
                    :value="boards.length">
                    <v-col cols="12" class="d-flex justify-end pa-0">
                        <v-btn x-small text class="align-self-end">Show All</v-btn>
                    </v-col>
                </base-material-stats-card>
            </v-col>
            <v-col cols="12" sm="6" lg="4">
                <base-material-stats-card
                    color="primary"
                    icon="mdi-clock"
                    title="Cards Assigned"
                    :value="totalCardsCount()">
                    <v-col cols="12" class="d-flex justify-end pa-0">
                        <v-btn x-small text class="align-self-end">Show All</v-btn>
                    </v-col>
                </base-material-stats-card>
            </v-col>
            <v-col cols="12" sm="6" lg="4">
                <base-material-stats-card
                    color="success"
                    icon="mdi-cart-plus"
                    title="Total Templates Created"
                    :value="templates.length">
                    <v-col cols="12" class="d-flex justify-end pa-0">
                        <v-btn x-small text class="align-self-end">Show All</v-btn>
                    </v-col>
                </base-material-stats-card>
            </v-col>
        </v-row>

        <v-row>
            <v-col offset="2" justify="center" cols="8">
                <campaign></campaign>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12" md="6">
                <base-material-card class="px-5 py-3" color="info">
                    <template v-slot:heading>
                        <div class="display-2 font-weight-light mt-n3">Your Boards</div>
                        <div class="subtitle-1 font-weight-light">
                            An overview of your boards
                        </div>
                    </template>
                    <v-card-text class="px-0">
                        <boards></boards>
                    </v-card-text>
                </base-material-card>
            </v-col>

            <v-col cols="12" md="6">
                <base-material-card class="px-5 py-3" color="primary">
                    <template v-slot:heading>
                        <div class="display-2 font-weight-light mt-n3">Your Templates</div>
                        <div class="subtitle-1 font-weight-light">
                            An overview of your templates
                        </div>
                    </template>
                    <v-card-text class="px-0">
<!--                        <templates></templates>-->
                    </v-card-text>
                </base-material-card>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12" md="6" v-show="user.role == 'owner'">
                <base-material-card class="px-5 py-3" color="pink lighten-1">
                    <template v-slot:heading>
                        <div class="display-2 font-weight-light mt-n3">Members and Team</div>
                    </template>
                    <v-card-text class="px-0">
                        <team></team>
                    </v-card-text>
                </base-material-card>
            </v-col>
            <v-col cols="12" md="6">
                <base-material-card class="px-5 py-3" color="warning">
                    <template v-slot:heading>
                        <div class="display-2 font-weight-light mt-n3">Announcements & Notifications</div>
                    </template>
                    <v-card-text class="px-0">
                        <v-list three-line>
                            <v-list-item-group active-class="pink--text">
                                <template v-for="(item, index) in notifications">
                                    <v-list-item :key="item.title" inactive>
                                        <template v-slot:default="{ active }">
                                            <v-list-item-icon>
                                                <v-icon color="pink">mdi-star</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="item.title"></v-list-item-title>

                                                <v-list-item-subtitle
                                                    class="text--primary"
                                                    v-text="item.headline"
                                                ></v-list-item-subtitle>

                                                <v-list-item-subtitle v-text="item.subtitle"></v-list-item-subtitle>
                                            </v-list-item-content>

                                            <v-list-item-action>
                                                <v-list-item-action-text>
                                                    <span :title="localTime(item.date).format('MMMM DD, YYYY hh:mm A')">
                                                            {{ localTime(item.date).fromNow() }}
                                                        </span>
                                                </v-list-item-action-text>
                                            </v-list-item-action>
                                        </template>
                                    </v-list-item>

                                    <v-divider
                                        v-if="index < notifications.length - 1"
                                        :key="index"
                                        class="keep-real dim"
                                    ></v-divider>
                                </template>
                            </v-list-item-group>
                        </v-list>
                    </v-card-text>
                </base-material-card>
            </v-col>
            <v-col cols="12" md="12">
                <base-material-card class="px-5 py-3" color="primary">
                    <template v-slot:heading>
                        <div class="display-2 font-weight-light mt-n3">Team cards</div>
                        <div class="subtitle-1 font-weight-light">
                            An overview of your Team Cards
                        </div>
                    </template>
                    <v-card-text class="px-0">
                       <TeamTasks></TeamTasks>
                    </v-card-text>
                </base-material-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    import axios from 'axios'
    import {api} from '~/api'
    import Campaign from "./widgets/campaign";
    import Team from "./team";
    import TeamTasks from "./teamtask";
    import Boards from "./widgets/boards";
    import Templates from "./widgets/templates";

    export default {
        name: 'Dashboard',

        data() {
            return {
                boards      : [],
                templates   : [],
                temp : [],
                notifications   : [],
                loading: false
            }
        },
        components: { Campaign, Team, Boards, Templates, TeamTasks},
        methods: {
            getNotifications(){
                let self = this;
                axios.get('/notifications.json')
                    .then(response => {
                        self.notifications = response.data;
                    });
            },
            getTemplates(){
                let self = this;
                axios
                    .get(api.path('templates.all'))
                    .then(function(resp){
                        self.templates = resp.data;
                    });
            },
            getBoards(){
                let self = this;
                axios
                    .get(api.path('boards.all'))
                    .then(function(resp){
                        self.boards = resp.data;
                    });
            },
            totalCardsCount(){
                let self = this;
                let count = 0;

                self.boards.forEach(function(board){
                    board.cards.forEach(function(card) {
                        if(card.assignee_id == self.user.id) count++;
                    });
                });
                return count;
            }
        },
        mounted() {
            let self = this;
            self.getNotifications();
            self.getTemplates();
            self.getBoards();
        }
    }
</script>
