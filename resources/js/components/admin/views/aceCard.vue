<template>
    <v-card v-if="card != null">
            <v-toolbar
                dark
                flat
                color="info"
            >
                <v-btn
                    icon
                    dark
                    @click="done"
                    v-if="$vuetify.breakpoint.smAndDown"
                    small
                >
                    <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-toolbar-title style="width: 80%">
                    <v-form>
                        <v-text-field
                            hide-details
                            prepend-icon="mdi-card-bulleted-outline"
                            v-model="card.title" class="text-h6"></v-text-field>
                    </v-form>
                </v-toolbar-title>

                <v-spacer></v-spacer>
                <v-toolbar-items>
                    <v-btn dark text color="blue darken-1" @click="createJob">
                        Create Job
                    </v-btn>
                    <v-btn
                        dark
                        text
                        v-if="$vuetify.breakpoint.mdAndDown"
                        @click="done"
                    >
                        Save
                    </v-btn>
                </v-toolbar-items>
            </v-toolbar>
        <v-progress-linear
                color="blue"
                rounded
                :indeterminate="loading"
                :active="loading"
                height="3"
        ></v-progress-linear>
        <v-card-text style="max-height: 68vh; overflow-y: scroll" class="slimScroll">
            <v-row dense>
                <v-col cols="8" class="pr-4">
                    <v-row>
                        <v-col cols="12">
                            <v-textarea v-model="card.description"
                                        label="Description"
                                        filled
                                        auto-grow
                                        hide-details
                                        placeholder="Add detailed description of the task ..."></v-textarea>
                        </v-col>
                        <v-col cols="12">
                            <dropbox
                                    :showDropboxFiles = "showDropboxFiles"
                                    :dropboxSelectedFiles = "dropboxSelectedFiles"
                                    v-on:updated-files="updatedFiles"
                                    :card="card"
                            ></dropbox>
                        </v-col>
                        <v-col cols="12">
                            <google-drive
                                    :showGoogleDriveFiles = "showGoogleDriveFiles"
                                    :selectedFiles = "selectedFiles"
                                    v-on:updated-files="updatedFiles"
                                    :card="card"
                            ></google-drive>
                        </v-col>
                        <v-col cols="12">
                            <shopify-integration
                                    :showAttachedData = "showAttachedData"
                                    :selectedProducts = "selectedProducts"
                                    :selectedOrders = "selectedOrders"
                                    :card="card"
                            ></shopify-integration>
                        </v-col>

                        <v-col cols="12">
                            <h4 class="no-select">
                               Attached Files
                            </h4>
                            <v-chip
                                    v-for="(attachment, index) in card.attachments"
                                    :key="index"
                                    class="ma-2"
                                    close
                                    color="cyan"
                                    label
                                    text-color="white"
                                    :disabled="loading"
                                    @click:close="deleteFile(attachment)"
                                    @click="downloadFile(attachment)"
                            >
                                <v-icon left>
                                    mdi-download
                                </v-icon>
                                <span class="text-truncate"> {{attachment.name}} </span>
                            </v-chip>
                        </v-col>

                        <v-col cols="12">
                            <v-file-input
                                label="Attach Files"
                                v-model="attachments"
                                :accept="fileTypes"
                                dense
                                small-chips
                                multiple
                                show-size
                                clearable
                                :loading="fileLoading"
                                :disabled="fileLoading"
                                @change="uploadFiles"
                            >
                            </v-file-input>
<!--                            <v-progress-linear-->
<!--                                    v-show="uploadPercentage > 0"-->
<!--                                    rounded-->
<!--                                    v-model="uploadPercentage"-->
<!--                                    height="15"-->
<!--                            ></v-progress-linear>-->
                        </v-col>
                        <v-col cols="12" class="d-flex justify-space-between pb-0">
                            <h4 class="no-select">
                                Sub Tasks
                                <span v-if="card.sub_tasks.length > 0">({{subTasksDone}} / {{card.sub_tasks.length}} Done)</span>
                            </h4>
                            <v-btn text small @click="addSubTask" color="secondary">Add Sub Task</v-btn>
                        </v-col>
                        <v-col cols="12" class="py-1">
                            <v-progress-linear
                                color="teal"
                                rounded
                                buffer-value="0"
                                :value="subTasksPercentage"
                                stream
                                height="3"
                            ></v-progress-linear>
                        </v-col>
                        <v-col cols="12" class="pt-0">
                            <v-list>
                                <template v-for="(task, index) in card.sub_tasks">
                                    <v-form @submit="saveSubTask(task)" onSubmit="return false" :key="index">
                                        <v-list-item
                                        class="blue lighten-5 my-1 border-ra rounded"
                                        :key="index">
                                        <v-list-item-icon class="mr-2 my-n2 pt-2">
                                            <v-checkbox color="info"
                                                        @change="saveSubTask(task)"
                                                        v-model="task.done"></v-checkbox>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <v-row dense>
                                                    <v-col cols="6">
                                                        <v-text-field
                                                            dense
                                                            style="font-size: 14px"
                                                            hide-details
                                                            append-outer-icon="mdi-content-save"
                                                            color="green"
                                                            @click:append-outer="saveSubTask(task)"
                                                            v-model="task.title">
                                                        </v-text-field>
                                                        <v-progress-linear v-if="task.busy" absolute indeterminate
                                                                           class="mt-2"
                                                                           height="3" :active="task.busy"></v-progress-linear>
                                                    </v-col>
                                                    <v-col cols="2" class="d-flex justify-end pt-2">
                                                        <v-dialog
                                                                v-model="task.dialog"
                                                                persistent
                                                                max-width="340"
                                                        >
                                                            <template v-slot:activator="{ on, attrs }">
                                                                <v-chip
                                                                        v-show="(task.assignee != null)"
                                                                        v-bind="attrs"
                                                                        v-on="on"
                                                                        :title="(task.assignee != null)? task.assignee.name: ''"
                                                                >
                                                                    <v-avatar left>
                                                                        <v-img v-show="(task.assignee != null && task.assignee.profile_picture != null)" :src="(task.assignee != null && task.assignee.profile_picture != null) ? task.assignee.profile_picture: ''"></v-img>
                                                                        <v-icon v-show="(task.assignee == null || task.assignee.profile_picture == null)">mdi-account-circle</v-icon>
                                                                    </v-avatar>
                                                                </v-chip>
                                                                <v-icon title="Assign sub task" v-show="task.assignee == null" v-bind="attrs"
                                                                    v-on="on">mdi-account-plus-outline</v-icon>
                                                            </template>
                                                            <v-form ref="form" @submit.prevent="saveSubTask(task, index)" lazy-validation>
                                                                <v-card>
                                                                    <v-card-title>
                                                                        <p class="text-h5"> Assign sub task </p>
                                                                    </v-card-title>
                                                                    <v-card-text>
                                                                        <v-autocomplete
                                                                                v-model="task.assignee_id"
                                                                                :disabled="task.busy || user.id != board.creator[0]['id']"
                                                                                :items="board.members"
                                                                                chips
                                                                                dense
                                                                                color="blue-grey lighten-2"
                                                                                label="Assign to"
                                                                                item-text="name"
                                                                                item-value="id"
                                                                                clearable
                                                                        >
                                                                            <template v-slot:selection="data">
                                                                                <v-chip
                                                                                        v-bind="data.attrs"
                                                                                        :input-value="data.selected"
                                                                                >
                                                                                    <v-avatar left>
                                                                                        <v-img v-if="data.item.profile_picture != null" :src="data.item.profile_picture"></v-img>
                                                                                        <v-icon v-else>mdi-account-circle</v-icon>
                                                                                    </v-avatar>
                                                                                    <span class="text-truncate">{{ data.item.name }}</span>
                                                                                </v-chip>
                                                                            </template>
                                                                            <template v-slot:item="data">
                                                                                <template v-if="typeof data.item !== 'object'">
                                                                                    <v-list-item-content v-text="data.item"></v-list-item-content>
                                                                                </template>
                                                                                <template v-else>
                                                                                    <v-list-item-avatar>
                                                                                        <v-img v-if="data.item.profile_picture != null" :src="data.item.profile_picture"></v-img>
                                                                                        <v-icon v-else>mdi-account-circle</v-icon>
                                                                                    </v-list-item-avatar>
                                                                                    <v-list-item-content>
                                                                                        <v-list-item-title v-html="data.item.name"></v-list-item-title>
                                                                                    </v-list-item-content>
                                                                                </template>
                                                                            </template>
                                                                        </v-autocomplete>
                                                                    </v-card-text>
                                                                    <v-card-actions>
                                                                        <v-spacer></v-spacer>
                                                                        <v-btn
                                                                                small
                                                                                type="submit"
                                                                                :loading="task.busy"
                                                                                :disabled="task.busy"
                                                                                color="primary"
                                                                                class="ml-4"
                                                                                depressed
                                                                        >
                                                                            Save
                                                                        </v-btn>
                                                                        <v-btn
                                                                                small
                                                                                text
                                                                                :disabled="task.busy"
                                                                                color="grey darken-2"
                                                                                @click="task.dialog = false; task.assignee_id = (task.assignee != null) ? task.assignee.id : null;"
                                                                        >
                                                                            Cancel
                                                                        </v-btn>
                                                                    </v-card-actions>
                                                                </v-card>
                                                            </v-form>
                                                        </v-dialog>
                                                    </v-col>
                                                    <v-col cols="4" class="d-flex justify-end pt-2">
                                                        <span
                                                            v-if="task.due_date != null"
                                                            :title="task.due_date" class="pl-1 mt-1 text-body-2">{{ localTime(task.due_date).fromNow() }}</span>
                                                        <v-menu offset-y left>
                                                            <template v-slot:activator="{ on, attrs }">
                                                                <v-btn icon text
                                                                       v-bind="attrs"
                                                                       style="margin-top: -5px"
                                                                       title="Add due date"
                                                                       color="info" v-on="on"><v-icon>mdi-calendar</v-icon></v-btn>
                                                            </template>
                                                            <v-date-picker
                                                                :min="new Date().toISOString()"
                                                                @change="saveSubTask(task)"
                                                                v-model="task.due_date" class="my-0"></v-date-picker>
                                                        </v-menu>
                                                    </v-col>
                                                </v-row>
                                            </v-list-item-title>
                                        </v-list-item-content>
                                        <v-list-item-action>
                                            <v-btn text fab x-small>
                                                <confirmable :target="task" :name="'Sub Task'" @delete="deleteSubTask">
                                                    <v-btn fab text small color="red lighten-2"><v-icon>mdi-delete-outline</v-icon></v-btn>
                                                </confirmable>
                                            </v-btn>
                                        </v-list-item-action>
                                    </v-list-item>
                                    </v-form>
                                </template>
                            </v-list>
                        </v-col>
                        <v-col cols="12" class="d-flex justify-end pt-1">

                        </v-col>
                        <v-col cols="12" class="py-1">
                            <v-textarea v-model="newComment"
                                        label="Add a comment ..."
                                        outlined
                                        auto-grow
                                        hide-details
                                        rows="1"
                                        :loading="commentBusy"
                                        placeholder="Add a comment ..."></v-textarea>
                        </v-col>
                        <v-col cols="12" class="d-flex justify-end pt-1">
                            <v-btn depressed
                                   small
                                   @click="addComment"
                                   :disabled="newComment == ''"
                                   :loading="commentBusy"
                                   color="secondary">Add Comment</v-btn>
                        </v-col>
                        <v-col cols="12" class="pt-1 comments-section">
                            <v-list three-line>
                                <template v-for="(comment, index) in card.comments">
                                    <v-list-item :key="index" class="px-0">
                                        <v-list-item-avatar>
                                            <v-icon large>mdi-account-circle-outline</v-icon>
                                        </v-list-item-avatar>

                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <b>{{comment.author.name}}</b>
                                                <span :title="comment.created_at" class="pl-1">{{ localTime(comment.created_at).fromNow() }}</span>
                                            </v-list-item-title>
                                            <v-list-item-subtitle
                                                style="overflow: visible"
                                                v-html="comment.body.replace(/\r\n|\r|\n/g,'<br />')">
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </template>
                            </v-list>
                        </v-col>
                    </v-row>
                </v-col>
                <v-col cols="4">
                    <v-row>
                        <v-col cols="12" class="d-flex justify-end pb-2">
                            <v-select
                                v-model="card.pre_requisite_id"
                                :items="preReqs"
                                item-text="title"
                                item-value="id"
                                label="Pre Requisite if any ..."
                                :menu-props="{ bottom: true, offsetY: true }"
                                clearable
                                dense
                                ></v-select>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="5">Assigned To:</v-col>
                        <v-col cols="7" class="pt-0 d-flex justify-end">
                            <v-autocomplete
                                    v-model="card.assignee_id"
                                    :disabled="loading || user.id != board.creator[0]['id']"
                                    :items="board.members"
                                    chips
                                    color="blue-grey lighten-2"
                                    label="Select a member"
                                    item-text="name"
                                    item-value="id"
                                    clearable
                            >
                                <template v-slot:selection="data">
                                    <v-chip
                                            v-bind="data.attrs"
                                            :input-value="data.selected"
                                    >
                                        <v-avatar left :size="32">
                                            <v-img :size="32" v-if="data.item.profile_picture != null" :src="data.item.profile_picture"></v-img>
                                            <v-icon :size="32" v-else>mdi-account-circle</v-icon>
                                        </v-avatar>
                                        <span class="text-truncate">{{ data.item.name }}</span>
                                    </v-chip>
                                </template>
                                <template v-slot:item="data">
                                    <template v-if="typeof data.item !== 'object'">
                                        <v-list-item-content v-text="data.item"></v-list-item-content>
                                    </template>
                                    <template v-else>
                                        <v-list-item-avatar>
                                            <v-img v-if="data.item.profile_picture != null" :src="data.item.profile_picture"></v-img>
                                            <v-icon v-else>mdi-account-circle</v-icon>
                                        </v-list-item-avatar>
                                        <v-list-item-content>
                                            <v-list-item-title v-html="data.item.name"></v-list-item-title>
                                        </v-list-item-content>
                                    </template>
                                </template>
                            </v-autocomplete>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="5">Priority:</v-col>
                        <v-col cols="7" class="d-flex justify-end">
                            <v-select
                                v-model="card.priority"
                                :items="priorities"
                                :menu-props="{ bottom: true, offsetY: true }"
                                hide-details
                                height="25px"
                                dense>
                                <template v-slot:selection="{item, index}">
                                    <v-list-item-avatar size="20">
                                        <v-icon small>{{item.icon}}</v-icon>
                                    </v-list-item-avatar>
                                    <v-list-item-content class="text-caption">
                                        {{item.text}}
                                    </v-list-item-content>
                                </template>
                                <template v-slot:item="{item, index}">
                                    <v-list-item-avatar size="20">
                                        <v-icon small>{{item.icon}}</v-icon>
                                    </v-list-item-avatar>
                                    <v-list-item-content class="text-caption">
                                        {{item.text}}
                                    </v-list-item-content>
                                </template>
                            </v-select>
                        </v-col>
                    </v-row>
                    <v-row class="pt-3">
                        <v-col cols="5">Due Date:</v-col>
                        <v-col cols="7" class="d-flex justify-end">
                            <span
                                v-if="card.due_date != null"
                                :title="card.due_date" class="pl-1">{{ localTime(card.due_date).fromNow() }}</span>
                            <v-menu offset-y left>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn icon text
                                           v-bind="attrs"
                                           style="margin-top: -5px"
                                           color="info" v-on="on"><v-icon>mdi-calendar</v-icon></v-btn>
                                </template>
                                <v-date-picker
                                    :min="new Date().toISOString()"
                                    v-model="card.due_date" class="my-0"></v-date-picker>
                            </v-menu>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="5">Time Tracking:</v-col>
                        <v-col cols="7" class="d-flex justify-end">
                            <v-dialog
                                    v-model="trackerDialog"
                                    persistent
                                    max-width="340"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-progress-linear :value="timeTrackerProgress" rounded height="5"
                                       class="mt-2" v-bind="attrs"
                                       v-on="on"></v-progress-linear>
                                </template>
                                <v-form ref="form" @submit.prevent="persistTracking" lazy-validation v-model="valid">
                                    <v-card>
                                        <v-card-title>
                                            <p class="text-h5"> Time Tracking </p>
                                        </v-card-title>
                                        <v-card-text>
                                            <v-progress-linear :value="timeTrackerProgress" height="8" rounded></v-progress-linear>
                                            <div style="display: flex;">
                                                <span v-show="tracker.total_time_spent_words != ''" >{{tracker.total_time_spent_words}} logged</span> <v-spacer></v-spacer> <span v-show="tracker.total_time_remaining_words != ''" >{{tracker.total_time_remaining_words}} remaining</span>
                                            </div>

                                            <v-row class="mt-2">
                                                <v-col cols="6">
                                                    <v-text-field
                                                            outlined
                                                            dense
                                                            label="Time Spent"
                                                            placeholder="2w 3d 5h 1m"
                                                            @change="calculateTime(tracker.time_spent, 'total_time_spent', true, false)"
                                                            v-model="tracker.time_spent"
                                                            :error-messages="errors.timeSpent"
                                                            :rules="[rules.required('time Spent')]">
                                                        <template v-slot:append>
                                                            <v-tooltip
                                                                    bottom
                                                            >
                                                                <template v-slot:activator="{ on }">
                                                                    <v-icon color="info" v-on="on">
                                                                        mdi-help-circle-outline
                                                                    </v-icon>
                                                                </template>
                                                                Use this format, 2w 3d 5h 1m<br>
                                                                2w = 2 weeks<br>
                                                                3d = 3 days<br>
                                                                5h = 5 hours<br>
                                                                1m = 1 minute
                                                            </v-tooltip>
                                                        </template>
                                                    </v-text-field>
                                                </v-col>
                                                <v-col cols="6">
                                                    <v-text-field
                                                            outlined
                                                            dense
                                                            label="Time Remaining"
                                                            placeholder="2w 3d 5h 1m"
                                                            @change="calculateTime(tracker.time_remaining, 'total_time_remaining', true, false)"
                                                            v-model="tracker.time_remaining">
                                                        <template v-slot:append>
                                                            <v-tooltip
                                                                    bottom
                                                            >
                                                                <template v-slot:activator="{ on }">
                                                                    <v-icon color="info" v-on="on">
                                                                        mdi-help-circle-outline
                                                                    </v-icon>
                                                                </template>
                                                                Use this format, 2w 3d 5h 1m<br>
                                                                2w = 2 weeks<br>
                                                                3d = 3 days<br>
                                                                5h = 5 hours<br>
                                                                1m = 1 minute
                                                            </v-tooltip>
                                                        </template>
                                                    </v-text-field>
                                                </v-col>
                                            </v-row>

                                            <v-row class="mt-2">
                                                <v-col cols="6">
                                                    <v-menu
                                                            v-model="calendar"
                                                            :close-on-content-click="false"
                                                            :nudge-right="40"
                                                            transition="scale-transition"
                                                            offset-y
                                                            min-width="auto"
                                                    >
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-text-field
                                                                    v-model="tracker.start_date"
                                                                    label="Start Date"
                                                                    append-icon="mdi-calendar"
                                                                    readonly
                                                                    outlined
                                                                    dense
                                                                    v-bind="attrs"
                                                                    v-on="on"
                                                                    :error-messages="errors.startDate"
                                                                    :rules="[rules.required('start date')]"
                                                            ></v-text-field>
                                                        </template>
                                                        <v-date-picker
                                                                v-model="tracker.start_date"
                                                                @input="calendar = false"
                                                        ></v-date-picker>
                                                    </v-menu>
                                                </v-col>

                                                <v-col cols="6">
                                                    <v-menu
                                                            v-model="clock"
                                                            :close-on-content-click="false"
                                                            :nudge-right="40"
                                                            transition="scale-transition"
                                                            offset-y
                                                            min-width="auto"
                                                    >
                                                        >
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-text-field
                                                                    v-model="tracker.start_time"
                                                                    label="Start Time"
                                                                    append-icon="mdi-clock"
                                                                    readonly
                                                                    outlined
                                                                    dense
                                                                    v-bind="attrs"
                                                                    v-on="on"
                                                                    :error-messages="errors.startTime"
                                                                    :rules="[rules.required('start time')]"
                                                            ></v-text-field>
                                                        </template>
                                                        <v-time-picker
                                                                v-model="tracker.start_time"
                                                                @input="clock = false"
                                                        ></v-time-picker>
                                                    </v-menu>
                                                </v-col>
                                            </v-row>
                                            <v-row class="mt-2">
                                                <v-card
                                                        class="mx-auto"
                                                        elevation="0"
                                                >
                                                    <p class="mb-0 text-h6">Work Description</p>
                                                    <vue-editor v-model="tracker.description" :editorToolbar="customToolbar"></vue-editor>
                                                </v-card>
                                            </v-row>
                                        </v-card-text>
                                        <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn
                                                    small
                                                    type="submit"
                                                    :loading="loading"
                                                    :disabled="loading || !valid"
                                                    color="primary"
                                                    class="ml-4"
                                                    depressed
                                            >
                                                Save
                                            </v-btn>
                                            <v-btn
                                                    small
                                                    text
                                                    :disabled="loading"
                                                    color="grey darken-2"
                                                    @click="trackerDialog = false"
                                            >
                                                Cancel
                                            </v-btn>
                                        </v-card-actions>
                                    </v-card>

                                </v-form>
                            </v-dialog>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="5" class="pt-5">Label:</v-col>
                        <v-col cols="7" class="d-flex justify-end">
                            <v-select
                                v-model="card.label"
                                :items="labels"
                                :menu-props="{ bottom: true, offsetY: true }"
                                dense
                                hide-details
                                height="25px">
                                <template v-slot:selection="{item, index}">
                                    <v-list-item-avatar size="20">
                                        <v-btn :color="item"></v-btn>
                                    </v-list-item-avatar>
                                    <v-list-item-content>
                                        <v-list-item-title>{{item}}</v-list-item-title>
                                    </v-list-item-content>
                                </template>
                                <template v-slot:item="{item, index}">
                                    <v-list-item-avatar size="20">
                                        <v-btn :color="item"></v-btn>
                                    </v-list-item-avatar>
                                    <v-list-item-content>
                                        <v-list-item-title>{{item}}</v-list-item-title>
                                    </v-list-item-content>
                                </template>
                            </v-select>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="5" class="pt-5">Integrations:</v-col>
                        <v-col cols="7" class="d-flex justify-end">
                            <v-select
                                    :items="integrations"
                                    :menu-props="{ bottom: true, offsetY: true }"
                                    dense
                                    outlined
                                    hide-details
                                    height="30px"
                                    label="Integrations"
                                    v-model="crntIntegration"
                                    @change="currentIntegration($event)"
                            >
                            </v-select>
                        </v-col>
                    </v-row>
                    <v-row v-if="crntIntegration == 'shopify'">
                        <v-tabs
                                fixed-tabs
                                background-color="info"
                        >
                            <v-tab>
                                Products
                            </v-tab>
                            <v-tab>
                                Orders
                            </v-tab>
                            <v-tab-item>
                                <v-col cols="12">
                                    <v-autocomplete
                                            :menu-props="{ bottom: true, offsetY: true }"
                                            dense
                                            outlined
                                            hide-details
                                            height="30px"
                                            label="Products"
                                            :loading="productsLoader"
                                            :items="products"
                                            item-text="title"
                                            placeholder="Start typing to Search"
                                            v-model="product"
                                            return-object
                                    ></v-autocomplete>
                                </v-col>
                            </v-tab-item>
                            <v-tab-item>
                                <v-col cols="12">
                                    <v-autocomplete
                                            :menu-props="{ bottom: true, offsetY: true }"
                                            dense
                                            outlined
                                            hide-details
                                            height="30px"
                                            label="orders"
                                            :loading="ordersLoader"
                                            :items="orders"
                                            item-text="customer.first_name"
                                            placeholder="Start typing to Search"
                                            v-model="order"
                                            return-object
                                    >
                                        <template v-slot:item="data">
                                            <template>
                                                <v-list-item-content>
                                                    <v-list-item-title v-html="orderDropdownInfoTitle(data)"></v-list-item-title>
                                                    <v-list-item-subtitle v-html="orderDropdownInfoSubTitle(data)"></v-list-item-subtitle>
                                                </v-list-item-content>
                                            </template>
                                        </template>
                                    </v-autocomplete>
                                </v-col>
                            </v-tab-item>
                        </v-tabs>
                        <v-col cols="12">
                            <v-btn depressed color="info" @click="attachData" style="margin:0 auto; left:30%">attach</v-btn>
                        </v-col>
                    </v-row>
                    <v-row v-if="crntIntegration == 'google'">

                        <google-drive  :attachGoogleDriveFiles = "attachGoogleDriveFiles"
                                       :selectedFiles = "selectedFiles"
                                       v-on:updated-files="updatedFiles"
                                       :card="card"
                        ></google-drive>
                    </v-row>
                    <v-row v-if="crntIntegration == 'dropbox'" class="justify-center">
                        <div id="dropbox-container"></div>
                        <dropbox  :attachDropboxFiles = "attachDropboxFiles"
                                       :dropboxSelectedFiles = "dropboxSelectedFiles"
                                       v-on:updated-files="updatedFiles"
                                       :card="card"
                        ></dropbox>
                    </v-row>
                    <v-row>
                        <v-col cols="12">
                            <tag-control :card="card"></tag-control>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions class="d-flex justify-end px-2">
            <v-btn text color="info" @click="close">Close</v-btn>
            <v-btn depressed color="success" @click="done">Done</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
    import {VueEditor} from "vue2-editor";
    import {mapGetters} from "vuex";
    import confirmable from "../shared/confirmable";
    import TagControl from "../shared/tagControl";
    import Form from '~/mixins/form'
    import Vue from "vue";
    import shopifyIntegration from "./integrations/shopify";
    import googleDrive from "./integrations/google-drive";
    import dropbox from "./integrations/dropbox";

    export default {
        mixins: [Form],

        name: 'aceCard',
        data() {
            return {
                selectedFiles : [],
                showGoogleDriveFiles : true,
                showDropboxFiles : true,
                dropboxSelectedFiles : [],
                attachGoogleDriveFiles : true,
                attachDropboxFiles : true,
                customToolbar: [["bold", "italic", "underline"], [{ list: "ordered" }, { list: "bullet" }], ["code-block"]],
                newComment  : '',
                commentBusy : false,
                switches    : {
                    files       : false,
                    subTasks    : false,
                    datePicker  : false
                },
                labels: [
                    'blue',
                    'green',
                    'pink',
                    'red',
                    'purple',
                    'indigo',
                    'light-blue',
                    'cyan',
                    'teal',
                    'lime'
                ],
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
                trackerDialog: false,
                defaultTracker:{
                    'total_time_spent' : 0,
                    'total_time_remaining' : 0,
                    'total_time_spent_words' : '0m',
                    'total_time_remaining_words' : '0m',
                    'current' : {
                        'total_time_spent' : 0,
                        'total_time_remaining' : 0,
                        'total_time_spent_words' : '0m',
                        'total_time_remaining_words' : '0m',
                    }
                },
                tracker: {},
                calendar: false,
                clock: false,
                valid: true,
                loading: false,
                integrations: [
                    'google',
                    'dropbox',
                    'shopify',
                    'facebook',
                ],
                crntIntegration : '',
                products : [],
                productsLoader  : false,
                ordersLoader    : false,
                product : '',
                selectedProducts : [],
                selectedOrders : [],
                showAttachedData : false,
                orders : [],
                order : '',
                fileLoading: false,
                attachments: [],
                // uploadPercentage: 0,
                fileTypes : "audio/*, image/*, video/*, .doc, .docx, .pdf, .msword, .zip, .rar",
            }
        },
        props: {
            card    : Object,
            board   : Object
        },
        computed: {
            ...mapGetters({
                user    : 'auth/user',
            }),
            preReqs: function(){
                let self = this;
                return self.board.cards.filter(function(card){ return card.id != self.card.id })
            },
            subTasksDone(){
                let self = this;
                let tasksDone = 0;
                self.card.sub_tasks.forEach(function(card){
                    if (card.done == 1) tasksDone++;
                });
                // update board current card data.
                let indexId = _.findIndex(self.board.cards, function (o) {
                    return o.id == self.card.id;
                });
                if (indexId > -1) {
                    self.board.cards[indexId]['tasksDone'] = tasksDone;
                }
                self.card.tasksDone = tasksDone;
                return tasksDone;
            },
            subTasksPercentage(){
                let self = this;
                return ((self.subTasksDone/self.card.sub_tasks.length) * 100).toFixed(0)
            },
            timeTrackerProgress(){
                let self = this;

                let total = self.tracker.total_time_spent + self.tracker.total_time_remaining;
                let percentage = ((self.tracker.total_time_spent /total) * 100).toFixed(0);
                return (!isNaN(percentage)) ? percentage : 0;
            }
        },
        components: {
            TagControl,
            VueEditor,
            'confirmable'           : confirmable,
            'shopify-integration'   : shopifyIntegration,
            'google-drive'          : googleDrive,
            'dropbox'               : dropbox
        },
        methods: {
            updatedFiles(files, type = null){
                if (type == 'dropbox') {
                    this.dropboxSelectedFiles = [];
                    this.dropboxSelectedFiles = files;
                } else {
                    this.selectedFiles = [];
                    this.selectedFiles = files;
                }
            },
            // shopify integration code start here,
            currentIntegration(event){
                let self = this;
                if (event == 'shopify') {
                    self.getProducts();
                    self.getOrders();
                }
            },
            // fetch products and order list.
            getProducts() {
                var self = this;
                self.productsLoader = true;
                axios.get(api.path('resource.getProducts')).then(function(resp){
                    if (resp.data.status) {
                        self.products = resp.data.data;
                    }
                    self.productsLoader = false;
                });
            },
            getOrders() {
                var self = this;
                self.ordersLoader = true;
                axios.get(api.path('resource.getOrders')).then(function(resp){
                    if (resp.data.status) {
                        self.orders = resp.data.data;
                    }
                    self.ordersLoader = false;
                });
            },
            orderDropdownInfoTitle(data){
                let info = '';
                if (data !== null && data !== undefined) {
                    info = data.item.customer.first_name + ' '  + moment(data.item.created_at).format('MMM D,YYYY');
                }
                return info;
            },
            orderDropdownInfoSubTitle(data){
                let info = '';
                if (data !== null && data !== undefined) {
                    let itemCount = data.item.line_items.length;
                    info = itemCount + ' items';
                }
                return info;
            },
            // fetch already save orders and products data.
            getResourceByIds(){
                let self = this;
                if (this.card !=null && this.card.shopify_integrations !== undefined && this.card.shopify_integrations.length > 0){
                    let productIds = '';
                    let products = '';
                    let orderIds = '';
                    let orders = '';
                    if (Array.isArray(this.card.shopify_integrations)) {
                        $.each(this.card.shopify_integrations, function (index, currentElement) {
                            if (currentElement.resource_type == 'product') {
                                productIds += currentElement.resource_id + ',';
                            }
                            if (currentElement.resource_type == 'order') {
                                orderIds += currentElement.resource_id + ',';
                            }
                        });
                        products = 'ids_' + productIds;
                        orders = 'ids_' + orderIds;
                    }
                    if (productIds !== '') {
                        axios.get(api.path('resource.getProducts')+'?search='+products).then(function(resp){
                            if (resp.data.status) {
                                self.selectedProducts = resp.data.data;
                                $.each(self.card.shopify_integrations, function (index, currentElement) {
                                    let crntProduct = _.find(self.selectedProducts, ['id', currentElement.resource_id]);
                                    if (crntProduct) {
                                        crntProduct.db_id = currentElement.id;
                                        self.card.shopify_integrations[index]['db_id'] = currentElement.id;
                                    }
                                });
                                self.showAttachedData = true;
                            }
                        });
                    }
                    if (orderIds !=='') {
                        axios.get(api.path('resource.getOrders')+'?search='+orders).then(function(resp){
                            if (resp.data.status) {
                                self.selectedOrders = resp.data.data;
                                $.each(self.card.shopify_integrations, function (index, currentElement) {
                                    let crntOrder = _.find(self.selectedOrders, ['id', currentElement.resource_id]);
                                    if (crntOrder) {
                                        crntOrder.db_id = currentElement.id;
                                        self.card.shopify_integrations[index]['db_id'] = currentElement.id;
                                    }
                                });
                                self.showAttachedData = true;
                            }
                        });
                    }

                }
            },
            // save shopify attached resource
            attachData(){
                let self = this;
                self.showAttachedData = true;
                if (self.product != '') {
                    let newResourceData = {
                        id              : null,
                        card_id         : self.card.id,
                        resource_id     : self.product.id,
                        resource_type   : 'product',
                    };
                    axios.post(api.path('shopifyIntegration.save'), newResourceData).then(function(resp){
                        if (resp.data.shopifyIntegration) {
                            if (resp.data.newly_created) {
                                self.product.db_id = resp.data.shopifyIntegration.id;
                                resp.data.shopifyIntegration.db_id = resp.data.shopifyIntegration.id;
                                if (!self.card.hasOwnProperty('shopify_integrations')) {
                                    Vue.set(self.card, 'shopify_integrations', []);
                                }
                                self.card.shopify_integrations.push(resp.data.shopifyIntegration);
                                self.selectedProducts.push(self.product);
                            }
                            self.product = '';
                        }
                    });
                }
                if (self.order != '') {
                    let newResourceData = {
                        id              : null,
                        card_id         : self.card.id,
                        resource_id     : self.order.id,
                        resource_type   : 'order',
                    };
                    axios.post(api.path('shopifyIntegration.save'), newResourceData).then(function(resp){
                        if (resp.data.shopifyIntegration) {
                            if (resp.data.newly_created) {
                                self.order.db_id = resp.data.shopifyIntegration.id;
                                resp.data.shopifyIntegration.db_id = resp.data.shopifyIntegration.id;
                                if (!self.card.hasOwnProperty('shopify_integrations')) {
                                    Vue.set(self.card, 'shopify_integrations', []);
                                }
                                self.card.shopify_integrations.push(resp.data.shopifyIntegration);
                                self.selectedOrders.push(self.order);
                            }
                            self.order = '';
                        }
                    });
                }
            },
            done(){
                if(this.consent()) this.$emit('done', this.card);
            },
            close(){
                if(this.consent()) this.$emit('close');
            },
            consent(){
                let response = true; let self = this;
                if(self.fileLoading) response = confirm('Attachments upload is in progress, Are you sure to leave?');
                return response;
            },
            addSubTask(){
                this.card.sub_tasks.push({
                    'id'        : null,
                    'assignee_id' : null,
                    'card_id'   : this.card.id,
                    'title'     : 'New Sub Task',
                    'due_date'  : null,
                    'dialog'    : false,
                    'done'      : false
                });
            },
            saveSubTask(task, index){
                Vue.set(task, 'busy', true);
                axios.post(api.path('subTasks.save'), task)
                .then(function(resp){
                    Vue.set(task, 'id', resp.data.entity.id);
                    Vue.set(task, 'assignee_id', resp.data.entity.assignee_id);
                    Vue.set(task, 'assignee', resp.data.entity.assignee);
                    Vue.set(task, 'busy', false);
                    Vue.set(task, 'dialog', false);
                })
            },
            addComment(){
                let self = this;
                self.commentBusy = true;

                let comment = {
                    id      : null,
                    card_id : self.card.id,
                    user_id : self.user.id,
                    body    : this.newComment
                };

                axios.post(api.path('comments.save'), comment)
                    .then(function(resp){
                        comment.id = resp.data.entity.id;
                        self.card.comments.push(resp.data.entity);
                        self.commentBusy = false;
                        self.newComment = ''
                    })
            },
            deleteSubTask(task){
                let self = this;
                let indx = this.card.sub_tasks.indexOf(task);
                if (task.id != null) {
                    axios.delete(api.path('subTasks.delete', {id: task.id})).then(function(){
                        self.card.sub_tasks.splice(indx, 1);
                    })
                } else self.card.sub_tasks.splice(indx, 1);
            },
            persistTracking() {
                let self = this;
                if (this.$refs.form.validate()) {
                    this.loading = true;
                    self.tracker.date_started = moment(self.tracker.start_date + ' ' + self.tracker.start_time, 'YYYY/MM/DD HH:mm');
                    self.tracker.card_id = this.card.id;

                    axios.post(api.path('timeTracker.save'), self.tracker)
                        .catch((error) => {
                            this.loading = false;
                        })
                        .then((response) => {
                            this.loading = false;
                            self.trackerDialog = false;
                            self.card.time_tracker.push(response.data.data);
                            self.initializeTracker();
                            self.showSnack(response.data.message);
                        });
                }
            },
            calculateTime(value, type, convert, init) {
                let self = this;
                if(value == null || value == '' || value == 0) {
                    if(type.includes('spent')){
                        self.tracker['total_time_remaining'] = self.tracker.current['total_time_remaining'];
                        self.tracker['total_time_remaining_words'] = self.tracker['time_remaining'] =self.tracker.current['total_time_remaining_words'];
                        self.tracker['total_time_spent'] = self.tracker.current['total_time_spent'];
                        self.tracker['total_time_spent_words'] = self.tracker.current['total_time_spent_words'];
                    }else{
                        self.tracker['total_time_remaining'] = 0;
                        self.tracker['total_time_remaining_words'] = self.tracker['time_remaining'] = "";
                    }
                    return;
                }

                value = value.trim();
                let timeStr = value.replace('w', '*3360');
                timeStr = timeStr.replace('d', '*480');
                timeStr = timeStr.replace('h', '*60');
                timeStr = timeStr.replace('m', '*1');
                let res = eval(timeStr.split(' ').join('+'));

                if(init == true) {
                    self.tracker[type] = self.tracker[type]  + res;
                    self.tracker.current[type] = _.clone(self.tracker[type]);
                }else {
                    self.tracker[type] = self.tracker.current[type]  + res;
                    if(type.includes('spent') && self.tracker.time_remaining != 0) {
                        if(res > self.tracker.total_time_remaining) {
                            self.tracker.total_time_remaining = 0; self.tracker.total_time_remaining_words = '';
                            self.tracker.time_remaining = null;
                        }else{
                            self.tracker.total_time_remaining = self.tracker.total_time_remaining - res;
                            self.timeInWords(self.tracker.total_time_remaining, 'total_time_remaining', false);
                            self.tracker.time_remaining = self.tracker.total_time_remaining_words;
                        }
                    }
                }

                if (convert) self.timeInWords(self.tracker[type], type, init);
                else {self.tracker[type+'_words'] = value; if(init == true) self.tracker.current[type+'_words'] = value;}
            },
            timeInWords(value, type, init){
                let self = this;

                let weeks = Math.floor( value / 3360);
                weeks = (weeks != 0) ? weeks+'w ' : '';
                let remainder = value % 3360;

                let days = Math.floor( remainder / 480);
                days = days != '0' ? days+'d ' : '';
                remainder = remainder % 480;

                let hours = Math.floor( remainder / 60);
                hours = (hours != '0') ? hours+'h ': '';
                remainder = remainder % 60;

                let mins = Math.floor( remainder);
                mins = (mins != '0') ? mins+'m': '';

                self.tracker[type+'_words'] = (weeks + days + hours + mins).replaceAll(' 0', '').trim();
                if(init == true) self.tracker.current[type+'_words'] = self.tracker[type+'_words'];
            },
            initializeTracker() {
                let self = this;
                self.tracker = _.cloneDeep(self.defaultTracker);
                let total = this.card.time_tracker.length;
                if(total == 0) return;

                this.card.time_tracker.forEach(function(tracker, index){
                    let convert = (index+1 == total) ? true : false;
                    self.calculateTime(tracker.time_spent, 'total_time_spent', convert, true);
                });

                self.tracker.time_remaining = this.card.time_tracker[(total- 1)].time_remaining;
                self.calculateTime(self.tracker.time_remaining, 'total_time_remaining', false, true);
            },
            createJob() {
                this.$emit('close');
                this.$emit('createJob');
            },

            uploadFiles() {
                let self = this;
                if(self.attachments.length == 0) return;

                let invalid = false;
                self.fileLoading = true;

                let formData = new FormData();
                formData.append("card_id", self.card.id);
                self.attachments.map(function(file, key) {
                    let documentType = file.type.split("/");
                    documentType = documentType[0];

                    let nameType = file.name.split(".");
                    nameType = nameType[nameType.length - 1];

                    if(documentType == "" || nameType == ""){invalid = true; return false;}
                    if(!self.fileTypes.includes(documentType) && !self.fileTypes.includes(nameType)) {invalid = true; return false;}
                    formData.append('attachments[]',file)
                });

                if(invalid) {
                    self.showSnack("ERROR. Allowed file formats are audio, image, video, doc, docx, pdf, msword");
                    self.fileLoading=false;
                    return;
                }

                axios.post(api.path('attachments.save'),
                    formData,
                    {
                    // onUploadProgress: function( progressEvent ) {
                    //     this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded / progressEvent.total ) * 100 ));
                    // }.bind(this)

                    }).then((res) => {
                        self.fileLoading = false;
                        self.showSnack(res.data.message);
                        self.card.attachments = _.clone(self.card.attachments.concat(res.data.data));
                    })
                    .catch((error) => {
                        console.log({error});
                        self.fileLoading = false;
                    });

            },
            downloadFile(file){
                window.open("/attachments/download/"+file.name, '_parent', 'location=yes,height=570,width=520,scrollbars=yes,status=yes');
            },
            deleteFile(file){
                let self = this;
                if(!confirm('Are sure to delete this file?') || self.loading) return;

                self.loading = true;
                axios.delete(api.path('attachments.delete', {'id': file.id}))
                    .then((res) => {
                        self.showSnack(res.data.message);
                        let index = self.card.attachments.indexOf(file);
                        self.card.attachments.splice(index, 1);
                        self.loading = false;
                    })
                    .catch((error) => {
                        console.log({error});
                        self.loading = false;
                    });
            },
            initialize(){
                let self = this;
                // this code is for shopify product and order integration start here.
                self.crntIntegration = [];
                self.selectedProducts = [];
                self.selectedOrders = [];
                self.selectedFiles = [];
                self.dropboxSelectedFiles = [];
                self.product = '';
                self.order = '';
                self.getResourceByIds();
                // this code is for shopify product and order integration end here.
                // this.card = _.cloneDeep(this.card);
                if(self.card != null) self.initializeTracker();
                self.attachments = [];
            },
            checkAssignee(task){
                return task.assignee != null;
            }
        },
        watch: {
            card: {
                immediate: true,
                handler (val, oldVal) {
                    this.initialize();
                }
            },
        }
    }
</script>
