/**
 * This is where all the authorization login is stored
 */
import store from '@/store/index.js'
export default function UserHasPermissions(router) {
    /**
     * Before each route we will see if the current user is authorized
     * to access the given route
     */
    router.beforeEach((to, from, next) => {
        let authenticated = store.getters['auth/authenticated']
        if(to.meta.requiresLogin && !authenticated){
            return next({
                name: 'login'
            })
        }
        return next()
    })

    router.afterEach(() => {
    });
}
