<template>
    <v-container>
        <v-card flat class="px-3" id="templates">
            <v-data-iterator :items="templates" hide-default-footer>
                <template v-slot:default="props">
                    <v-row>
                        <v-col
                                v-for="template in props.items"
                                :key="template.id"
                                cols="12"
                                sm="6"
                                md="4">
                            <v-card color="blue lighten-2" dark class="mt-0">
                                <div class="d-flex flex-no-wrap justify-space-between">
                                    <div>
                                        <v-card-title class="text-h6" v-text="template.name"></v-card-title>
                                        <v-card-subtitle class="mt-1">
                                            <div>by <span class="black--text">{{template.user.name}}</span></div>
                                            {{template.content.panes.length}} Stages,
                                            {{template.content.cards.length}} Cards
                                            <span v-if="countSubTasks(template.content) > 0">, {{countSubTasks(template.content)}} Sub-tasks</span>
                                        </v-card-subtitle>

                                        <v-card-actions>
                                            <v-btn class="ml-2 mt-5"
                                                   @click="useTemplate(template)"
                                                   :loading="template.busy"
                                                   rounded small color="success" depressed>
                                                Use Template
                                            </v-btn>
                                        </v-card-actions>
                                    </div>

                                    <v-avatar class="ma-3 mt-5" size="135" tile>
                                        <v-row dense>
                                            <v-col cols="12">
                                                <v-icon size="90">{{template.field_type.icon}}</v-icon>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-chip color="transparent" label text-color="white" title="Views" outlined>
                                                    <v-icon left>mdi-eye-outline</v-icon>
                                                    {{template.views}}
                                                </v-chip>
                                                <v-chip color="transparent" label text-color="white" title="Views" outlined>
                                                    <v-icon left>mdi-content-copy</v-icon>
                                                    {{template.installs}}
                                                </v-chip>
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
                            No templates created yet,
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
        data() {
            return {
                templates: [],
                field: 'trello'
            }
        },
        methods: {
            getTemplates() {
                let self = this;
                axios
                    .get(api.path('templates.byField', {'field': self.field}))
                    .then(function (resp) {
                        self.templates = resp.data;
                    });
            },
            countSubTasks(board) {
                let count = 0;
                if ('cards' in board) {
                    board.cards.forEach(function (card) {
                        count += card.sub_tasks.length;
                    })
                }
                return count;
            },
            useTemplate(template) {
                Vue.set(template, 'busy', true);
                axios.post(api.path('boards.fromTemplate'), {'templateId': template.id})
                    .then(function (resp) {
                        template.busy = false;
                    });
            },

        },
        mounted() {
            this.getTemplates();
        }
    }
</script>