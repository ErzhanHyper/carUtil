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

import SellPage from "@/Pages/sell/Sell.vue";
import SellDetail from "@/Pages/sell/SellDetail.vue";
import SellCreate from "@/Pages/sell/SellCreate.vue";

import ReportPage from "@/Pages/report/Report.vue";

import VehiclePage from "@/Pages/vehicle/Vehicle.vue";
import VehicleDetail from "@/Pages/vehicle/VehicleDetail.vue";
import VehicleCreate from "@/Pages/vehicle/VehicleCreate.vue";

import FactoryPage from "@/Pages/factory/Factory.vue";
import FactoryDetail from "@/Pages/factory/FactoryDetail.vue";
import FactoryCreate from "@/Pages/factory/FactoryCreate.vue";

import ManufacturePage from "@/Pages/manufacture/Manufacture.vue";
import ManufactureDetail from "@/Pages/manufacture/ManufactureDetail.vue";
import ManufactureCreate from "@/Pages/manufacture/ManufactureCreate.vue";

import UserPage from "@/Pages/user/User.vue";
import UserDetail from "@/Pages/user/UserDetail.vue";
import UserCreate from "@/Pages/user/UserCreate.vue";

import ProfilePage from "@/Pages/profile/ProfileMain.vue";

import ExchangePage from "@/Pages/exchange/Exchange.vue";
import ExchangeDetail from "@/Pages/exchange/ExchangeDetail.vue";

import LogPage from "@/Pages/log/LogPage.vue";

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
                path: '/exchange/:id',
                component: ExchangeDetail,
                name: 'exchange_detail',
                props: true
            },

            {
                path: '/sell',
                component: SellPage,
                name: 'sell',
            },
            {
                path: '/sell/create',
                component: SellCreate,
                name: 'sell_create',
            },
            {
                path: '/sell/:id',
                component: SellDetail,
                name: 'sell_detail',
                props: true
            },

            {
                path: '/report',
                component: ReportPage,
                name: 'report',
            },


            {
                path: '/vehicle',
                component: VehiclePage,
                name: 'vehicle',
            },
            {
                path: '/vehicle/:id',
                component: VehicleDetail,
                name: 'vehicle_detail',
                props: true
            },
            {
                path: '/vehicle/create',
                component: VehicleCreate,
                name: 'vehicle_create',
            },

            {
                path: '/manufacture',
                component: ManufacturePage,
                name: 'manufacture',
            },
            {
                path: '/manufacture/:id',
                component: ManufactureDetail,
                name: 'manufacture_detail',
                props: true
            },
            {
                path: '/manufacture/create',
                component: ManufactureCreate,
                name: 'manufacture_create',
            },

            {
                path: '/factory',
                component: FactoryPage,
                name: 'factory',
            },
            {
                path: '/factory/:id',
                component: FactoryDetail,
                name: 'factory_detail',
                props: true
            },

            {
                path: '/factory/create',
                component: FactoryCreate,
                name: 'factory_create',
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

            {
                path: '/user/create',
                component: UserCreate,
                name: 'user_create',
            },

            {
                path: '/user/:id',
                component: UserDetail,
                name: 'user_detail',
                props: true
            },

            {
                path: '/log',
                component: LogPage,
                name: 'log',
            },

        ]
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router
