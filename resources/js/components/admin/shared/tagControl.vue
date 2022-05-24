<template>
    <v-combobox
        v-model="card.tags"
        :filter="filter"
        :hide-no-data="!search"
        :items="items"
        :search-input.sync="search"
        hide-selected
        label="Card Tags"
        multiple
        small-chips
    >
        <template v-slot:no-data>
            <v-list-item>
                <span class="subheading">Create</span>
                <v-chip
                    :color="`${colors[nonce - 1]} lighten-3`"
                    label
                    small
                >
                    {{ search }}
                </v-chip>
            </v-list-item>
        </template>
        <template v-slot:selection="{ attrs, item, parent, selected }">
            <v-chip
                v-if="item === Object(item)"
                v-bind="attrs"
                :color="`${item.color} lighten-3`"
                :input-value="selected"
                label
                outlined>
                <v-icon left color="blue">
                    mdi-label
                </v-icon>
          <span class="pr-2">
            {{ item.text }}
          </span>
                <v-icon
                    small
                    @click="parent.selectItem(item)"
                >
                    mdi-close-thick
                </v-icon>
            </v-chip>
        </template>
        <template v-slot:item="{ index, item }">
            <v-text-field
                v-if="editing === item"
                v-model="editing.text"
                autofocus
                flat
                background-color="transparent"
                hide-details
                solo
                dense
                @keyup.enter="edit(index, item)"
            ></v-text-field>
            <v-chip
                v-else
                :color="`${item.color} lighten-3`"
                dark
                label
                small
            >
                {{ item.text }}
            </v-chip>
            <v-spacer></v-spacer>
            <v-list-item-action @click.stop>
                <v-btn
                    icon
                    @click.stop.prevent="edit(index, item)"
                    small
                >
                    <v-icon>{{ editing !== item ? 'mdi-pencil' : 'mdi-check' }}</v-icon>
                </v-btn>
            </v-list-item-action>
        </template>
    </v-combobox>
</template>
<script>
    export default {
        name : 'tagControl',
        data: () => ({
            activator: null,
            attach: null,
            colors: ['green', 'purple', 'indigo', 'cyan', 'teal', 'orange'],
            editing: null,
            editingIndex: -1,
            items: [
                { header: 'Select an option or create one' },
                {
                    text: 'Shopify',
                    color: 'blue',
                },
                {
                    text: 'Marketing',
                    color: 'red',
                },
            ],
            nonce: 1,
            menu: false,
            model: [
                {
                    text: 'Shopify',
                    color: 'blue',
                },
            ],
            x: 0,
            search: null,
            y: 0,
        }),
        watch: {
            model (val, prev) {
                if (val.length === prev.length) return

                this.model = val.map(v => {
                    if (typeof v === 'string') {
                        v = {
                            text: v,
                            color: this.colors[this.nonce - 1],
                        }

                        this.items.push(v)

                        this.nonce++
                    }

                    return v
                })
            },
        },
        methods: {
            edit (index, item) {
                if (!this.editing) {
                    this.editing = item
                    this.editingIndex = index
                } else {
                    this.editing = null
                    this.editingIndex = -1
                }
            },
            filter (item, queryText, itemText) {
                if (item.header) return false

                const hasValue = val => val != null ? val : ''

                const text = hasValue(itemText)
                const query = hasValue(queryText)

                return text.toString()
                    .toLowerCase()
                    .indexOf(query.toString().toLowerCase()) > -1
            },
        },
        props: {
            card    : Object
        }
    }
</script>
