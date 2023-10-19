import {createWebHistory, createRouter} from "vue-router";
import Login from '@/Pages/Login.vue'
import LoginManager from '@/Pages/LoginManager.vue'

import MainLayout from "@/Components/MainLayout.vue";
import OrderPage from '@/Pages/order/Order.vue'
import OrderDetail from "@/Pages/order/OrderDetail.vue";
import CertificatePage from "@/Pages/certificate/Certificate.vue";
import PreorderPage from "@/Pages/preorder/Preorder.vue";
import PreorderDetail from "@/Pages/preorder/PreorderDetail.vue";
import TransferOrderPage from "./Pages/transfer/TransferOrder.vue";
import TransferOrderDetail from "./Pages/transfer/TransferOrderDetail.vue";

const routes = [

    {
        path: '/login',
        component: Login,
        name: 'login',
    },

    {
        path: '/manager',
        component: LoginManager,
        name: 'manager',
    },

    {
        path: '/',
        component: MainLayout,
        name: 'main',
        redirect: 'order',
        meta: {
            requiresLogin: true,
        },
        children: [

            {
                path: '/preorder',
                component: PreorderPage,
                name: 'preorder',
            },

            {
                path: '/preorder/:id',
                component: PreorderDetail,
                name: 'preorder_detail',
                props: true
            },

            {
                path: '/order',
                component: OrderPage,
                name: 'order',
            },

            {
                path: '/order/:id',
                component: OrderDetail,
                name: 'order_detail',
                props: true
            },

            {
                path: '/certificate',
                component: CertificatePage,
                name: 'certificate',
            },

            {
                path: '/transfer/order',
                component: TransferOrderPage,
                name: 'transfer_order',
            },
            {
                path: '/transfer/order/:id',
                component: TransferOrderDetail,
                name: 'transfer_detail',
                props: true
            },
        ]
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router
