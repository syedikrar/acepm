<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <v-container>
        <v-card flat class="px-3">
            <v-data-iterator :items="boards" hide-default-footer>
                <template v-slot:default="props">
                    <v-row>
                        <v-col
                                v-for="board in props.items"
                                :key="board.id"
                                cols="12"
                                sm="6"
                                md="4">
                            <v-card color="blue lighten-2" dark class="mt-0">
                                <div class="d-flex flex-no-wrap justify-space-between">
                                    <div>
                                        <v-card-title class="text-h6" v-text="board.name"></v-card-title>
                                        <v-card-subtitle class="mt-1">
                                            {{board.panes.length}} Stages,
                                            {{board.cards.length}} Cards
                                            <!--<span v-if="countSubTasks(board) > 0">, {{countSubTasks(board)}} Sub-tasks</span>-->
                                        </v-card-subtitle>

                                        <v-card-actions class="mt-5">
                                            <v-btn class="ml-2"
                                                   :to="{name: 'ace_board', params: {id: board.id, mode: 'board', field: field}}"
                                                   rounded small color="success" depressed>
                                                Open Board
                                            </v-btn>
                                            <confirmable :target="board" name="Board" @delete="deleteBoard">
                                                <v-btn v-show="user.id == board.creator[0]['id']" fab x-small depressed dark color="pink lighten-1" class="ml-2">
                                                    <v-icon>mdi-delete-empty-outline</v-icon>
                                                </v-btn>
                                            </confirmable>
                                        </v-card-actions>
                                    </div>

                                    <v-avatar class="ma-3" size="135" tile>
                                        <v-row dense>
                                            <v-col cols="12">
                                                <v-icon size="90">{{board.field_type.icon}}</v-icon>
                                            </v-col>
                                        </v-row>
                                    </v-avatar>
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>
                </template>
                <template v-slot:no-data>
                    <v-row v-show="user.role == 'owner'">
                        <v-col cols="12" class="d-flex justify-center">
                            No boards created yet,
                            <router-link class="px-1" :to="{name: 'ace_board', params: {id: 0, mode: 'load', field: field}}">start from scratch</router-link>
                            instead ?
                        </v-col>
                    </v-row>
                </template>
            </v-data-iterator>
        </v-card>
    </v-container>
</template>

<script>
    import Confirmable from "../../shared/confirmable";
    export default {

        components: {Confirmable},
        data(){
            return {
                boards      : [],
                field : 'trello'
            }
        },
        methods:{
            getBoards(){
                let self = this;
                axios
                    .get(api.path('boards.byField', {'field': self.field}))
                    .then(function(resp){
                        self.boards = resp.data;
                    });
            },
            countSubTasks(board){
                let count = 0;
                if ('cards' in board) {
                    board.cards.forEach(function(card){
                        count += card.sub_tasks.length;
                    })
                }
                return count;
            },

            deleteBoard(board) {
                let self = this;
                Vue.set(board, 'busy', true);
                axios.delete(api.path('boards.delete', {'id': board.id})).then(function(resp){
                    self.showSnack(resp.data);

                    board.busy = false;
                })
            }
        },
        mounted() {
            this.getBoards();
        }
    }
</script>