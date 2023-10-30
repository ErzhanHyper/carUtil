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
import ServicePage from "@/Pages/service/Service.vue";
import ExchangePage from "@/Pages/exchange/Exchange.vue";
import SellPage from "@/Pages/sell/Sell.vue";
import ReportPage from "@/Pages/report/Report.vue";
import UserPage from "@/Pages/user/User.vue";

import ProfilePage from "@/Pages/profile/ProfileMain.vue";

const routes = [

    {
        path: '/login',
        component: Login,
        name: 'login',
    },

    {
        path: '/line',
        redirect: '/login',
    },

    {
        path: '/manager',
        component: LoginManager,
        name: 'manager',
    },

    {
        path: '/auth-rop',
        redirect: '/manager',
    },

    {
        path: '/auth-operator',
        redirect: '/manager',
    },

    {
        path: '/auth-dealer',
        redirect: '/manager',
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

            {
                path: '/service',
                component: ServicePage,
                name: 'service',
            },

            {
                path: '/exchange',
                component: ExchangePage,
                name: 'exchange',
            },

            {
                path: '/sell',
                component: SellPage,
                name: 'sell',
            },

            {
                path: '/report',
                component: ReportPage,
                name: 'report',
            },

            {
                path: '/profile',
                component: ProfilePage,
                name: 'profile',
            },

            {
                path: '/user',
                component: UserPage,
                name: 'user',
            },
        ]
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router
