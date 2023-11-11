import './bootstrap'

import {createApp, h} from 'vue'
import {Quasar, Notify} from 'quasar'
import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/src/css/index.sass'
import '../sass/app.scss'
import App from "./Components/App.vue";
import middleware from '@/modules/middleware.js'
import './services/interceptors'

import moment from 'moment';
import 'moment/dist/locale/ru'
moment.locale('ru')
import emitter from '@/mitt';
import '@/store/subscriber.js';
import store from '@/store/index.js';
store.dispatch('auth/attempt', localStorage.getItem('token__auto.recycle.kz'))

import router from './router.js';
middleware(router)

const myApp = createApp(App);
myApp.config.globalProperties.$moment = moment
myApp.config.globalProperties.$emitter = emitter;

myApp.use(store);

myApp.use(Quasar, {
    plugins: {
        Notify
    },
    config: {
        notify: { /* look at QuasarConfOptions from the API card */ }
    }
});
myApp.use(router);
myApp.mount('#app')
