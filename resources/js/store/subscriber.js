import store from '@/store'
import api from "../api";

store.subscribe((mutation) => {

    switch (mutation.type) {
        case 'auth/SET_TOKEN' :

            if (mutation.payload) {
                api.defaults.headers.common['Authorization'] = `Bearer ${mutation.payload}`
                window.localStorage.setItem('token__auto.recycle.kz', mutation.payload)
            }else{
                api.defaults.headers.common['Authorization'] = null
                window.localStorage.removeItem('token__auto.recycle.kz')
            }

            break;

        case 'auth/SET_USER' :

            if (mutation.payload) {
                window.localStorage.setItem('user', JSON.stringify(mutation.payload))

            }else{
                window.localStorage.removeItem('user')
                window.localStorage.removeItem('transfer_terms')
            }

            break;
    }
})
