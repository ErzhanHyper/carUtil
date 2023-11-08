<template>

    <q-toolbar class="bg-primary text-white text-center">
        <q-toolbar-title>АО &laquo;Жасыл Даму&raquo;</q-toolbar-title>
    </q-toolbar>

    <q-separator color="grey-8" />

    <q-list bordered padding class="rounded-borders text-white" v-if="show">

        <template v-for="(item, i) in items" :key="i" v-if="items.length > 0">

            <q-expansion-item
                expand-separator
                icon="mail"
                :label="item.name"
                :caption="item.caption"
                v-if="item.expand"
            >
                <template v-for="(child, c) in item.children" :key="c">
                    <router-link :to="child.link">
                        <q-item
                            clickable
                            v-ripple
                            active-class="main-menu-link"
                            class="q-ml-lg"
                        >
                            <q-item-section avatar>
                                <q-icon :name="child.icon"/>
                            </q-item-section>

                            <q-item-section>{{ child.name }}</q-item-section>
                        </q-item>
                    </router-link>
                </template>
            </q-expansion-item>

            <router-link :to="item.link">
                <q-item
                    clickable
                    v-ripple
                    active-class="my-menu-link"
                    v-if="!item.expand"
                >
                    <q-item-section avatar>
                        <q-icon :name="item.icon"/>
                    </q-item-section>

                    <q-item-section>{{ item.name }}</q-item-section>
                </q-item>
            </router-link>

        </template>

    </q-list>

    <div class="text-center" v-else>
    <q-circular-progress
        indeterminate
        rounded
        size="50px"
        color="white"
        class="q-my-lg"
    />
    </div>

</template>

<script>
import menu from "../constants/menu";
import {getUser} from "../services/user";

export default {

    components: {},

    data() {
        return {
            liner_menu: menu.items,
            moderator_menu: menu.moderatorItems,
            operator_menu: menu.operatorItems,
            dealer_light_menu: menu.dealerLightItems,
            items: [],
            show: false
        }
    },

    created() {
        getUser({}).then(res => {
            if(res && res.role) {
                if (res.role === 'liner') {
                    this.items = this.liner_menu
                } else if (res.role === 'moderator') {
                    this.items = this.moderator_menu
                } else if (res.role === 'operator') {
                    this.items = this.operator_menu
                }
                else if (res.role === 'dealer-light') {
                    this.items = this.dealer_light_menu
                }
                this.show = true
            }
        })

    }
}
</script>
