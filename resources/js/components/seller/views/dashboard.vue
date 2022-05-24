<template>
    <v-container id="dashboard" fluid tag="section">
        <v-row>
            <v-col cols="12" sm="6" lg="4">
                <base-material-stats-card
                    color="info"
                    icon="mdi-dock-top"
                    title="Stat One"
                    :value="stat_one.length">
                    <v-col cols="12" class="d-flex justify-end pa-0">
                        <v-btn x-small text class="align-self-end">Show All</v-btn>
                    </v-col>
                </base-material-stats-card>
            </v-col>
            <v-col cols="12" sm="6" lg="4">
                <base-material-stats-card
                    color="primary"
                    icon="mdi-clock"
                    title="Stat Two"
                    :value="stat_two.length">
                    <v-col cols="12" class="d-flex justify-end pa-0">
                        <v-btn x-small text class="align-self-end">Show All</v-btn>
                    </v-col>
                </base-material-stats-card>
            </v-col>
            <v-col cols="12" sm="6" lg="4">
                <base-material-stats-card
                    color="success"
                    icon="mdi-cart-plus"
                    title="Stat Three"
                    :value="stat_three.length">
                    <v-col cols="12" class="d-flex justify-end pa-0">
                        <v-btn x-small text class="align-self-end">Show All</v-btn>
                    </v-col>
                </base-material-stats-card>
            </v-col>
        </v-row>

        <v-row>
            <v-col offset="2" justify="center" cols="8">
<!--                <campaign></campaign>-->
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12" md="6">
                <base-material-card class="px-5 py-3" color="info">
                    <template v-slot:heading>
                        <div class="display-2 font-weight-light mt-n3">Stat One</div>
                        <div class="subtitle-1 font-weight-light">
                            An overview of the stats
                        </div>
                    </template>
                    <v-card-text class="px-0">
                        <v-container fluid class="px-0">
                            <v-data-iterator
                                :items="stat_one"
                                item-key="id"
                                :items-per-page="9"
                                :no-data-text="stat_one_busy ? 'Please Wait ...' : 'No Stats created.'"
                                hide-default-footer>
                                <template v-slot:default="{ items }">
                                    <v-row dense>
                                        <v-col cols="4" v-for="item in items" :key="item.id">
                                            <v-card flat outlined class="my-0" height="200px" style="overflow: hidden">
                                                <v-img
                                                    height="90"
                                                    :src="(item.details.rightBgIcon != '' ? item.details.rightBgIcon : item.details.leftBgIcon)"
                                                ></v-img>
                                                <v-card-title>
                                                    <h5 class="font-weight-regular trimmed-text" :title="item.bar_name">{{ item.bar_name }}</h5>
                                                </v-card-title>
                                                <v-card-text class="pb-0">
                                                    <div class="text-right text-caption font-weight-light">Created
                                                        <span :title="localTime(item.created_at).format('MMMM DD, YYYY hh:mm A')">
                                                            {{ localTime(item.created_at).fromNow() }}
                                                        </span>
                                                    </div>
                                                    <hr class="dim"/>
                                                </v-card-text>
                                                <v-card-actions class="pt-2">
                                                    <span class="text-overline justify-start">{{item.status}}</span>
                                                    <v-spacer></v-spacer>
                                                    <v-btn x-small depressed color="info lighten-1" :to="{name: 'mb_edit', params: {id: item.id}}">Details</v-btn>
                                                </v-card-actions>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </template>
                            </v-data-iterator>
                        </v-container>
                    </v-card-text>
                </base-material-card>
            </v-col>

            <v-col cols="12" md="6">
                <base-material-card class="px-5 py-3" color="primary">
                    <template v-slot:heading>
                        <div class="display-2 font-weight-light mt-n3">Stat Two</div>
                        <div class="subtitle-1 font-weight-light">
                            An overview of the stats
                        </div>
                    </template>
                    <v-card-text class="px-0">
                        <v-container fluid class="px-0">
                            <v-data-iterator
                                :items="stat_two"
                                item-key="id"
                                :items-per-page="9"
                                :no-data-text="stat_two_busy ? 'Please Wait ...' : 'No stats created.'"
                                hide-default-footer>
                                <template v-slot:default="{ items }">
                                    <v-row dense>
                                        <v-col cols="4" v-for="item in items" :key="item.id">
                                            <v-card flat outlined class="my-0" height="200px" style="overflow: hidden">
                                                <div class="d-flex justify-center info" style="height: 90px">
                                                    <v-icon size="50">mdi-timer-sand</v-icon>
                                                    <v-icon size="50">mdi-timer-outline</v-icon>
                                                    <v-icon size="50">mdi-camera-timer</v-icon>
                                                </div>
                                                <v-card-title>
                                                    <h5 class="font-weight-regular trimmed-text" :title="item.title">{{ item.title }}</h5>
                                                </v-card-title>
                                                <v-card-text class="pb-0">
                                                    <div class="text-right text-caption font-weight-light">Created
                                                        <span :title="localTime(item.created_at).format('MMMM DD, YYYY hh:mm A')">
                                                            {{ localTime(item.created_at).fromNow() }}
                                                        </span>
                                                    </div>
                                                    <hr class="dim"/>
                                                </v-card-text>
                                                <v-card-actions class="pt-2">
                                                    <span class="text-overline justify-start">{{item.is_active ? 'Active' : 'Paused'}}</span>
                                                    <v-spacer></v-spacer>
                                                    <v-btn x-small depressed color="info lighten-1" :to="{name: 'ct_edit', params: {id: item.id}}">Details</v-btn>
                                                </v-card-actions>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </template>
                            </v-data-iterator>
                        </v-container>
                    </v-card-text>
                </base-material-card>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12" md="6">
                <base-material-card class="px-5 py-3">
                    <template v-slot:heading>
                        <div class="display-2 font-weight-light mt-n3">Stat Three</div>
                        <div class="subtitle-1 font-weight-light">
                            An overview of the stats
                        </div>
                    </template>
                    <v-card-text class="px-0">
                        <v-container fluid class="px-0">
                            <v-data-iterator
                                :items="stat_three"
                                item-key="id"
                                :items-per-page="9"
                                :no-data-text="stat_three_busy ? 'Please Wait ...' : 'No stats created.'"
                                hide-default-footer>
                                <template v-slot:default="{ items }">
                                    <v-row dense>
                                        <v-col cols="4" v-for="item in items" :key="item.id">
                                            <v-card flat outlined class="my-0" height="200px" style="overflow: hidden">
                                                <div class="d-flex justify-center"
                                                     v-if="item.details.defaultStickyBarColor == 'solid'"
                                                     v-bind:style="{height: '90px', background: 'none '+item.details.barBackgroundColorValue.hex }"
                                                >
                                                    <v-icon size="50" color="white">mdi-widgets</v-icon>
                                                    <v-icon size="50" color="white">mdi-cash-usd</v-icon>
                                                    <v-icon size="50" color="white">mdi-cart-plus</v-icon>
                                                </div>
                                                <v-img
                                                    v-else
                                                    height="90"
                                                    :src="item.details.backgroundImage"
                                                ></v-img>
                                                <v-card-title>
                                                    <h5 class="font-weight-regular trimmed-text" :title="item.bar_name">{{ item.bar_name }}</h5>
                                                </v-card-title>
                                                <v-card-text class="pb-0">
                                                    <div class="text-right text-caption font-weight-light">Created
                                                        <span :title="localTime(item.created_at).format('MMMM DD, YYYY hh:mm A')">
                                                            {{ localTime(item.created_at).fromNow() }}
                                                        </span>
                                                    </div>
                                                    <hr class="dim"/>
                                                </v-card-text>
                                                <v-card-actions class="pt-2">
                                                    <span class="text-overline justify-start">{{item.status ? 'Active' : 'Paused'}}</span>
                                                    <v-spacer></v-spacer>
                                                    <v-btn x-small depressed color="info lighten-1" :to="{name: 'st_edit', params: {id: item.id}}">Details</v-btn>
                                                </v-card-actions>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </template>
                            </v-data-iterator>
                        </v-container>
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
        </v-row>
    </v-container>
</template>

<script>
    import axios from 'axios'
    import Campaign from "./widgets/campaign";

    export default {
        name: 'Dashboard',

        data() {
            return {
                stat_one_busy : false,
                stat_two_busy   : false,
                stat_three_busy  : false,

                stat_one    : [],
                stat_two    : [],
                stat_three  : [],
                notifications   : []
            }
        },
        components: { Campaign },
        methods: {
            getNotifications(){
                let self = this;
                axios.get('/notifications.json')
                    .then(response => {
                        self.notifications = response.data;
                    });
            }
        },
        mounted() {
            let self = this;
            self.getNotifications()
        }
    }
</script>
