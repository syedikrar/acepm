<template>
        <div style="display: contents">
            <main @mousemove="onDrag($event)" @mouseup="onDragStop()">
                <div
                    v-for="(pane, paneIdx) in filledPanes"
                    :key="paneIdx"
                    :ref="`pane-${paneIdx}`"
                    class="pane"
                    v-bind:style="{ backgroundColor : ('color' in pane && pane.color != undefined ? pane.color : '') }"
                    >
                    <div class="pane-header">
                        <v-text-field hide-details label="Stage name" v-model="board.panes[paneIdx].name" class="mb-2"></v-text-field>
                        <v-menu offset-y>
                            <template v-slot:activator="{ on }">
                                <v-btn small color="info" v-on="on" tile min-width="30" class="mr-2 br-4" depressed
                                       title="Stage Color">
                                    <v-icon :color="pane.color" class="mt-1">mdi-format-color-fill</v-icon>
                                </v-btn>
                            </template>
                            <v-color-picker
                                    v-model="pane.color"
                                    hide-canvas
                                    hide-inputs
                                    hide-sliders
                                    show-swatches
                                    class="mx-auto"
                                    swatches-max-height="200"
                                    @update:color="colorSelected"
                            ></v-color-picker>
                        </v-menu>
                    </div>
                    <v-card
                        v-for="(card, cardIdx) in pane.cards"
                        :key="cardIdx"
                        :class="{ 'pane-card': true, 'dragging': draggedCardIdx === card.index }"
                        :ref="`card-${card.index}`"
                    >
                        <v-card-text class="pa-0" @mousedown="onDragStart($event, card.index)" @mouseup="upListener($event, card)">
                            <v-row dense>
                                <v-col cols="8"><v-icon
                                    :color="'label' in card ? card.label : 'info'"
                                    class="mr-2">mdi-card-bulleted-outline</v-icon>{{ card.title }}
                                </v-col>
                                <v-col cols="2">
                                    <v-list-item-avatar size="20" v-for="(priority, indexPriority) in priorities"
                                        :key="indexPriority" v-if="priority.text == card.priority"
                                        style="margin-top: unset"
                                    >
                                        <v-icon small>{{priority.icon}}</v-icon>
                                    </v-list-item-avatar>
                                </v-col>
                                <v-col cols="2" v-for="(member, index) in board.members" :key="index" v-if="member.id == card.assignee_id">
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-avatar left v-bind="attrs"
                                                      v-on="on" style="height: unset; float: right" :size="32">
                                                <v-img :size="32" v-if="member.profile_picture != null" :src="member.profile_picture"></v-img>
                                                <v-icon :size="32" v-else>mdi-account-circle</v-icon>
                                            </v-avatar>
                                        </template>
                                        <span>{{member.name}}</span>
                                    </v-tooltip>
                                </v-col>
                            </v-row>
                            <v-row dense>
                                <v-col cols="8" v-if="card.due_date != null" >
                                    <span :title="card.due_date" class="pl-1">{{ localTime(card.due_date).fromNow() }}</span>
                                </v-col>

                                <v-col cols="4" v-if="card.sub_tasks.length > 0" :class="(card.tasksDone === card.sub_tasks.length ? 'success' : '')">
                                    <span ><v-icon>mdi-check-box-multiple-outline</v-icon> {{card.tasksDone}} / {{card.sub_tasks.length}}</span>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                    <add-card @save="addCard" :pane="paneIdx"></add-card>
                </div>
                <add-pane v-show="user.id == board.creator[0]['id']" @save="addPane"></add-pane>
            </main>
            <div
                id="ghost-card"
                ref="ghostCard"
                :style="`
           width: ${ghostCardStyle.width}px;
           left: ${ghostCardStyle.pos.x - ($store.state.drawer ? 70 : 0)}px; top: ${ghostCardStyle.pos.y - 85}px;
           transform: ${ghostCardStyle.transform};
           transform-origin: ${ghostCardStyle.transformOrigin};
        `"
                :class="{ 'pane-card ghost-card': true, 'active': draggedCardIdx !== -1, leaving: ghostCardStyle.leaving, animate: settings.animate }"
            >
                {{ draggedCard.title }}
            </div>
            <v-dialog
                v-model="cardPop"
                width="900"
                persistent
                :fullscreen="$vuetify.breakpoint.smAndDown">
                <ace-card :card="activeCard" :board="board" @done="popDone" @close="cardPop = false" @createJob="createJob"></ace-card>
            </v-dialog>
            <v-dialog
                v-model="job_card"
                width="900"
                persistent
                :fullscreen="$vuetify.breakpoint.smAndDown">
                <createJob :card="activeCard" :board="board" @done="jobDone" @close="job_card = false"></createJob>
            </v-dialog>
        </div>
</template>
<script>
    import axios from 'axios'
    import {mapGetters} from 'vuex'
    import {api} from '~/api'
    import Form from '~/mixins/form'
    import Confirmable from "../shared/confirmable";
    import aceCard from "./aceCard";
    import addCard from "../shared/addCard";
    import addPane from "../shared/addPane";
    import createJob from "./gigs/createJob";
    import Vue from "vue";

    const SETTINGS = {
        trelloStyle: false,
        animateEnd: true,
        transformOriginMode: 'mouse', // or 'center'
        scale: 1.1,
        rotationOffset: {
            min: 1.2,
            max: 2
        },
        rotationMitigation: 0.2
    }

    export default {
        name: 'Board',
        mixins: [Form],
        components: {
            Confirmable,
            aceCard,
            addCard,
            addPane,
            createJob
        },
        data() {
            return {
                subTaskStatus : 0,
                priorities: [
                    {
                        'icon': 'mdi-arrow-up-thick',
                        'text' : 'highest'
                    },
                    {
                        'icon': 'mdi-arrow-up',
                        'text' : 'high'
                    },
                    {
                        'icon': 'mdi-code-tags',
                        'text' : 'average'
                    },
                    {
                        'icon': 'mdi-arrow-down',
                        'text' : 'low'
                    }
                ],
                busy : false,
                color: 'rgba(211,211,211,0.3)',
                mousePos: {
                    x: -1000,
                    y: -1000
                },
                lastMousePos: { x: 0, y: 0 },
                draggedCardIdx: -1,
                paneOverlappedIdx: -1,
                ghostCardStyle: {
                    leaving: false,
                    pos: {
                        x: 0,
                        y: 0
                    },
                    width: 0,
                    cursorDistance: {
                        x: 0,
                        y: 0
                    },
                    percentDistanceMiddle: 0,
                    transform: '',
                    transformOrigin: '',
                    velocity: 0,
                    rotation: 0
                },
                settings: SETTINGS,
                delta       : 6,
                startX      : 0,
                startY      : 0,
                activeCard  : null,
                cardPop     : false,
                job_card: false,
            };
        },
        props : {
            board : Object
        },
        computed: {
            ...mapGetters({
                auth: 'auth/user',
                shop: 'auth/shop'
            }),
            filledPanes() {
                let filledPanes = this.board.panes.map(item => ({ name: item.name, color : item.color }));

                for (let i = 0; i < this.board.cards.length; i++) {
                    let pane = filledPanes[this.board.cards[i].pane_index];

                    if (!pane.cards) pane.cards = [];
                    pane.cards.push({ ...this.board.cards[i], index: i });
                }
                return filledPanes;
            },
            draggedCard() {
                return this.board.cards[this.draggedCardIdx] || { name: '' };
            }
        },
        methods : {
            colorSelected: function(color){
                let self = this;
                self.board.panes = _.cloneDeep(self.filledPanes.map(item => ({ name: item.name, color : item.color })));
                this.$emit('syncBoard');
            },
            upListener(event, card){
                let self = this;
                const diffX = Math.abs(event.pageX - this.startX);
                const diffY = Math.abs(event.pageY - this.startY);

                if (diffX < this.delta && diffY < this.delta) {
                    self.activeCard = card;
                    self.cardPop = true;
                }
            },
            onDragStart(e, index) {
                this.startX = e.pageX;
                this.startY = e.pageY;

                let cardEl = this.$refs[`card-${index}`][0].$el;
                let cardRect = cardEl.getBoundingClientRect();

                document.documentElement.style.cursor = 'grabbing';

                let paddingLeft = parseFloat(getComputedStyle(cardEl).paddingLeft);
                let paddingRight = parseFloat(getComputedStyle(cardEl).paddingRight);

                this.mousePos.x = e.pageX;
                this.mousePos.y = e.pageY;

                this.draggedCardIdx = index;

                this.ghostCardStyle.width =
                    cardEl.clientWidth - paddingLeft - paddingRight;
                this.ghostCardStyle.cursorDistance.x = e.pageX - cardRect.x;
                this.ghostCardStyle.cursorDistance.y = e.pageY - cardRect.y;

                this.setGhostCardStyle(e);
                this.updateUI();

                if (this.settings.transformOriginMode === 'center')
                    this.ghostCardStyle.transformOrigin = 'center';
                else
                    this.ghostCardStyle.transformOrigin = `${this.ghostCardStyle.cursorDistance.x}px ${this.ghostCardStyle.cursorDistance.y}px`;
                this.ghostCardStyle.percentDistanceMiddle =
                    this.ghostCardStyle.cursorDistance.x - cardEl.clientWidth / 2;
                this.ghostCardStyle.percentDistanceMiddle = Math.abs(
                    this.ghostCardStyle.percentDistanceMiddle
                );
                this.ghostCardStyle.percentDistanceMiddle /= cardEl.clientWidth / 2;
                this.ghostCardStyle.percentDistanceMiddle =
                    Math.round(this.ghostCardStyle.percentDistanceMiddle * 100) / 100;
            },
            onDrag(e) {
                e = e || window.event;
                if (this.draggedCardIdx === -1)
                    return;
                this.mousePos.x = e.pageX;
                this.mousePos.y = e.pageY;
            },
            updateUI() {
                let dragX = this.mousePos.x,
                    dragY = this.mousePos.y;

                if (this.draggedCardIdx === -1 || this.ghostCardStyle.leaving) return;

                if (!dragX && !dragY) {
                    this.lastMousePos.x = 0;
                    this.lastMousePos.y = 0;
                    return requestAnimationFrame(this.updateUI);
                }
                this.findTransformValues();
                this.setGhostCardStyle(true);

                let isOverlapping;

                for (let i = 0, paneEl = null; (paneEl = this.$refs[`pane-${i}`]); i++) {
                    paneEl = paneEl[0] ? paneEl[0] : paneEl;

                    isOverlapping = this.checkOverlap(
                        { x: dragX, y: dragY },
                        paneEl.getBoundingClientRect()
                    );

                    if (isOverlapping && this.paneOverlappedIdx === i)
                        return requestAnimationFrame(this.updateUI);
                    else if (isOverlapping) {
                        this.paneOverlappedIdx = i;
                        break;
                    }
                }

                if (!isOverlapping) {
                    return requestAnimationFrame(this.updateUI);
                }
                this.putCardInPane();
                return requestAnimationFrame(this.updateUI);
            },
            onDragStop() {
                if (this.draggedCardIdx === -1) return;
                document.documentElement.style.cursor = 'default';
                let cardEl = this.$refs[`card-${this.draggedCardIdx}`] && this.$refs[`card-${this.draggedCardIdx}`][0].$el
                let cardRect = cardEl.getBoundingClientRect();

                if (!this.settings.animateEnd) {
                    return this.resetValues()
                }
                setTimeout(() => {
                    this.resetValues();
                }, 100);
                this.ghostCardStyle.leaving = true;
                let xOffset = cardRect.x - this.ghostCardStyle.pos.x
                let yOffset = cardRect.y - this.ghostCardStyle.pos.y
                this.ghostCardStyle.transform = `scale(1) translate(${xOffset}px, ${yOffset}px)`
            },
            resetValues() {
                this.draggedCardIdx = -1;
                this.paneOverlappedIdx = -1;
                this.lastMousePos.x = 0;
                this.lastMousePos.y = 0;
                this.ghostCardStyle.x = -1000;
                this.ghostCardStyle.y = -1000;
                this.ghostCardStyle.width = 0;
                this.ghostCardStyle.cursorDistance.x = 0;
                this.ghostCardStyle.cursorDistance.y = 0;
                this.ghostCardStyle.transform = '';
                this.ghostCardStyle.leaving = false;
                this.ghostCardStyle.percentDistanceMiddle = 0;
            },
            checkOverlap(drag, rect) {
                if (drag.x < rect.x || drag.x > rect.x + rect.width) return false;
                if (drag.y < rect.y || drag.y > rect.y + rect.height) return false;
                return true;
            },
            putCardInPane() {
                this.board.cards[this.draggedCardIdx].pane_index = this.paneOverlappedIdx;
            },
            setGhostCardStyle(isDragstart) {
                let dragX = this.mousePos.x,
                    dragY = this.mousePos.y;
                let transform = [];

                if (isDragstart)
                    transform.push(`scale(${this.settings.scale})`);

                transform.push(`rotate(${this.ghostCardStyle.rotation}deg)`);
                this.ghostCardStyle.transform = transform.join(' ');
                this.ghostCardStyle.pos.x = dragX - this.ghostCardStyle.cursorDistance.x;
                this.ghostCardStyle.pos.y = dragY - this.ghostCardStyle.cursorDistance.y;
            },
            findTransformValues() {
                if (this.settings.trelloStyle) {
                    this.ghostCardStyle.rotation = '4';
                    this.lastMousePos.x = this.mousePos.x;
                    this.lastMousePos.y = this.mousePos.y;
                    return;
                }


                let velocity = this.mousePos.x - this.lastMousePos.x;
                let rotation = this.ghostCardStyle.rotation || 0;

                let rotationMin = this.settings.rotationOffset.min;
                let rotationMax = this.settings.rotationOffset.max;
                let rotationOffset =
                    (rotationMax - rotationMin) *
                    (1 - this.ghostCardStyle.percentDistanceMiddle);
                let rotationMitigation = this.settings.rotationMitigation

                rotation =
                    rotation * (1 - rotationMitigation) +
                    this.sigmoid(velocity) * (rotationMin + rotationOffset);
                if (Math.abs(rotation) < 0.01) rotation = 0;

                this.ghostCardStyle.velocity = velocity;
                this.ghostCardStyle.rotation = rotation
                this.lastMousePos.x = this.mousePos.x;
                this.lastMousePos.y = this.mousePos.y;
            },
            sigmoid(x) {
                return x / (1 + Math.abs(x));
            },

            popDone(card) {
                let self = this;
                self.cardPop = false;
                self.activeCard = null;
                //self.$set(self.board.cards, card.index, card);
                axios.post(api.path('cards.save'), card);
            },
            addCard(pane, title) {
                let self = this;
                let newCard = {
                    id          : null,
                    board_id    : self.board.id,
                    title       : title,
                    description : null,
                    pane_index  : pane,
                    moved       : false,
                    sub_tasks   : [],
                    comments    : [],
                    time_tracker: []
                };
                axios.post(api.path('cards.save'), newCard).then(function(resp){
                    newCard.id = resp.data.card.id;
                });
                self.board.cards.push(newCard);

            },
            addPane(pane) {
                let self = this;
                self.board.panes.push(pane);
            },
            createJob() {
              this.job_card = true
            },
            jobDone() {
                this.job_card = false
            },
        },
        watch: {
            board: {
                immediate: true,
                handler (val, oldVal) {
                    let self = this;
                    if (self.board != null) {
                        self.board.cards.forEach(function(card){
                            card.moved = false;
                            let tasksDone = 0;
                            if (!card.hasOwnProperty('tasksDone')) {
                                Vue.set(card, 'tasksDone', tasksDone);
                            }
                            if (card.sub_tasks.length > 0) {
                                card.sub_tasks.forEach(function(subTask){
                                    if (subTask.done == 1) tasksDone++;
                                });
                                card.tasksDone = tasksDone;
                            }
                            if ('sub_tasks' in card == false) card.sub_tasks = [];
                            if ('comments' in card == false) card.comments = [];
                        });
                    }
                }
            }
        }
    }

</script>

