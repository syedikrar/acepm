<template>
    <v-row justify="space-around" style="flex: initial;">
            <v-menu
                    v-for="(menuItem, pIndex) in categories"
                    :key="pIndex"
                    open-on-hover
                    transition="scale-transition"
                    offset-x
            >
                <template v-slot:activator="{ attrs, on }">
                    <p
                            class="ma-5"
                            v-bind="attrs"
                            v-on="on"
                    >
                        {{menuItem.title}}
                    </p>
                </template>

                <v-list>
                    <v-list-item
                            v-for="(subItem, cIndex) in menuItem.sub_categories"
                            :key="cIndex"
                            link
                    >
                        <v-list-item-title v-text="subItem.title"></v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
    </v-row>
</template>

<script>
    export default {
        name: 'AppBar',

        data: () => ({
            categories: [],
        }),
        methods: {
            getCategories: function() {
                let self = this;
                axios.get(api.path('gig.categories'))
                    .then((response)=>{
                        self.categories = response.data;
                    });
            },
        },
        mounted (){
            let self = this;
            self.getCategories();
        }
    }
</script>
