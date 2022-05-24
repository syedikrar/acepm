<template>
    <v-container
        id="users"
        fluid
        tag="section"
    >
        <base-v-component
            heading="My Team"
            subheading="And Their Current Tasks."
        />

        <base-material-card
            icon="group"
            title="Team With Cards"
            class="px-5 py-3"
        >
            
            <v-layout>
                <v-row>
                    <v-col cols="12">
                        
                        <v-simple-table fixed-header min-height="375px">
                                <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Boards</th>
                                    <th>Cards</th>

                                    
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(user, index) in teamtasks.users" :key="index">
                                    
                                    <td class="text-left">{{ user.name }}</td>
                                    <td class="text-left">{{ user.role }}</td>
                                    <td class="text-left" >
                                       <tr v-for="(board, b_index) in user.boards" :key="b_index">
                                           <table>
                                                <tr>{{ board.name }}</tr>
                                            </table>
                                        </tr>
                                    </td>
                                    <td>
                                        <tr v-for="(userboard, c_index) in user.boards" :key="c_index">
                                            <table >
                                                <tr> 
                                                    <th>Title</th>
                                                    <th>Description</th>
                                                    <th>Priority</th>
                                                    <th>Due Date</th>
                                                    <th>Sub Tasks</th>
                                                </tr>
                                                <tr v-for="(card, o_index) in userboard.cards" :key="o_index">
                                                    <td>{{ card.title }}</td>
                                                    <td>{{ card.description }}</td>
                                                    <td>{{ card.priority }}</td>
                                                    <td>{{ card.due_date }}</td>
                                                    <td>{{ card.sub_tasks.length}}</td>
                                                </tr>

                                            </table>
                                        </tr>
                                    </td>
                                    
                                    
                                </tr>
                                </tbody>
                            </v-simple-table>
                    </v-col>
                </v-row>
            </v-layout>
        </base-material-card>
    </v-container>
</template>

<script>

    import axios from 'axios'
    import {mapGetters} from 'vuex'
    import {api} from '~/api'
    
    export default {
        data: () => ({
            loading     : false,
            teamtasks: [],
        }),
        methods: {
            teamTasks: function(){
                let self = this;
                axios
                    .get(api.path('users.teamTasks'))
                    .then(function(resp){
                        self.teamtasks = resp.data;
                    });
            }
        },

        mounted() {
            this.teamTasks();
        },
        computed: mapGetters({
            auth: 'auth/user'
        })
    }
</script>
