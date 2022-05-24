<template>
    <v-card width="280" color="transparent" height="100" flat class="my-0">
        <v-btn
            v-if="showText == false"
            @click="showText = true"
            fab small depressed class="br-4" color="blue" title="Add a Stage"><v-icon>mdi-card-plus-outline</v-icon></v-btn>
        <v-row v-if="showText">
            <v-col cols="12">
                <v-text-field
                    placeholder="Stage name ..."
                    v-model="paneTitle"
                    filled
                    autocom
                    @keyup.enter="save"
                    autofocus
                    dense hide-details full-width></v-text-field>
            </v-col>
            <v-col cols="12" class="pt-0 d-flex justify-end">
                <v-btn small @click="showText = false" text icon tile class="mr-1">
                    <v-icon>mdi-close-thick</v-icon>
                </v-btn>
                <v-menu offset-y>
                    <template v-slot:activator="{ on }">
                        <v-btn small color="info" v-on="on" tile min-width="30" class="mr-2 br-4" depressed
                               title="Stage Color">
                            <v-icon :color="paneColor" class="mt-1">mdi-format-color-fill</v-icon>
                        </v-btn>
                    </template>
                    <v-color-picker
                        hide-canvas
                        hide-inputs
                        hide-sliders
                        show-swatches
                        class="mx-auto"
                        swatches-max-height="200"
                        @update:color="colorSelected"
                    ></v-color-picker>
                </v-menu>
                <v-btn small depressed color="success" @click="save" :disabled="paneTitle == ''">Add Stage</v-btn>
            </v-col>
        </v-row>
    </v-card>
</template>
<script>
    export default {
        data() {
            return {
                showText    : false,
                paneTitle   : '',
                paneColor   : 'rgba(211,211,211,0.3)'
            }
        },
        methods: {
            save: function(){
                this.showText = false;
                this.$emit('save', {name : this.paneTitle, color : this.paneColor});
                this.paneTitle = '';
                this.paneColor = 'rgba(211,211,211,0.3)';
            },
            colorSelected: function(color){
                let self = this;
                self.paneColor = 'rgba('+color.rgba.r+','+color.rgba.g+','+color.rgba.b+',0.3)';
            }
        }
    }
</script>
