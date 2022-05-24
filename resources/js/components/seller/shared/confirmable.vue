<template>
    <v-dialog v-model="dialog" width="500" :disabled="disabled">
        <template v-slot:activator="{ on }">
            <div v-on="on" style="display: inline">
                <slot></slot>
            </div>
        </template>
        <v-card>
            <v-card-title class="headline py-3 quicksand">
                <v-icon large left class="error--text">mdi-delete-alert-outline</v-icon>
                <span>Confirm Delete ?</span>
            </v-card-title>
            <v-card-text class="mt-2" v-html="ask"></v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn text color="accent" @click="closeDialog" :width="70">No</v-btn>
                <v-btn depressed color="success" @click="deleteTarget" :width="70" class="mr-0">Yes</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        name: 'confirmable',
        data() {
            return {
                dialog      : false,
                targetObj   : this.target,
                entityName  : this.name
            }
        },
        props: {
            target  : Object,
            name    : String,
            trigger : Boolean,
            disabled: Boolean
        },
        computed: {
            ask : function(){
                return 'Are you sure you want to delete '+this.entityName+' ?'
            }
        },
        methods: {
            deleteTarget: function(){
                this.dialog = false;
                this.$emit('delete', this.targetObj);
            },
            closeDialog: function(){
                this.dialog = false;
                this.$emit('cancel', '');
            }
        },
        watch: {
            'trigger': function(val){
                this.dialog = val;
            },
            'target': function (val) {
                this.targetObj = this.target;
            },
            'dialog': function(){
                this.targetObj = this.target;
            }
        }
    }
</script>
