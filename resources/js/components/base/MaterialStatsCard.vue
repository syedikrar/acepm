<template>
    <base-material-card
        :icon="icon"
        class="v-card--material-stats"
        v-bind="$attrs"
        v-on="$listeners"
    >
        <template v-slot:after-heading>
            <div class="ml-auto text-right">
                <div
                    class="body-3 grey--text font-weight-light"
                    v-text="title"
                />

                <h3 class="display-2 font-weight-light text--primary">
                    {{ value }} <small>{{ smallValue }}</small>
                </h3>
            </div>
        </template>

        <v-col
            cols="12"
            class="px-0"
        >
            <v-divider/>
        </v-col>
        <!--
        the subicon and subtext
        <v-icon
            :color="subIconColor"
            size="16"
            class="ml-2 mr-1"
        >
            {{ subIcon }}
        </v-icon>

        <span
            :class="subTextColor"
            class="caption grey&#45;&#45;text font-weight-light"
            v-text="subText"
        />-->
        <slot></slot>
    </base-material-card>
</template>

<script>
    import Card from './Card'

    export default {
        name: 'MaterialStatsCard',

        inheritAttrs: false,

        props: {
            ...Card.props,
            icon: {
                type: String,
                required: true,
            },
            subIcon: {
                type: String,
                default: undefined,
            },
            subIconColor: {
                type: String,
                default: undefined,
            },
            subTextColor: {
                type: String,
                default: undefined,
            },
            subText: {
                type: String,
                default: undefined,
            },
            title: {
                type: String,
                default: undefined,
            },
            value: {
                type: String | Number,
                default: undefined,
            },
            smallValue: {
                type: String,
                default: undefined,
            },
        },
    }
</script>

