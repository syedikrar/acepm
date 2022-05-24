<template>
    <div class="pb-3">
        <v-btn
            v-if="showText == false"
            block color="secondary" text @click="showText = true">Add a Card</v-btn>
        <v-row v-if="showText">
            <v-col cols="12">
                <v-text-field
                    placeholder="Card Title"
                    v-model="cardTitle"
                    filled
                    autofocus
                    @keyup.enter="save"
                    dense hide-details full-width></v-text-field>
            </v-col>
            <v-col cols="12" class="pt-0 d-flex justify-end">
                <v-btn small @click="showText = false" text icon tile class="mr-1">
                    <v-icon>mdi-close-thick</v-icon>
                </v-btn>
                <v-btn small depressed color="success" @click="save" :disabled="cardTitle == ''">Create</v-btn>
            </v-col>
        </v-row>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                showText : false,
                cardTitle : ''
            }
        },
        props: {
            pane : Number
        },
        methods: {
            save: function(){
                this.showText = false;
                this.$emit('save', this.pane, this.cardTitle);
                this.cardTitle = '';
            }
        }
    }
</script>
