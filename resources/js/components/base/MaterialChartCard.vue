<template>
    <base-material-card
        class="v-card--material-chart"
        v-bind="$attrs"
        v-on="$listeners"
    >
        <template v-slot:heading>
            <chartist
                :data="data"
                :event-handlers="eventHandlers"
                :options="options"
                :ratio="ratio"
                :responsive-options="responsiveOptions"
                :type="type"
                style="max-height: 150px;"
            />
        </template>

        <slot
            slot="reveal-actions"
            name="reveal-actions"
        />

        <slot/>

        <slot
            slot="actions"
            name="actions"
        />
    </base-material-card>
</template>

<script>
    export default {
        name: 'MaterialChartCard',

        inheritAttrs: false,

        props: {
            data: {
                type: Object,
                default: () => ({}),
            },
            eventHandlers: {
                type: Array,
                default: () => ([]),
            },
            options: {
                type: Object,
                default: () => ({}),
            },
            ratio: {
                type: String,
                default: undefined,
            },
            responsiveOptions: {
                type: Array,
                default: () => ([]),
            },
            type: {
                type: String,
                required: true,
                validator: v => ['Bar', 'Line', 'Pie'].includes(v),
            },
        },
    }
</script>
