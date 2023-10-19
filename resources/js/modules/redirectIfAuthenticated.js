import store from '@/store'

export default function RedirectIfAuthenticated(router) {
    /**
     * If the user is already authenticated he shouldn't be able to visit
     * pages like login, register, etc...
     */
    router.beforeEach((to, from, next) => {
        /**
         * Checks if there's a token and the next page name is none of the following
         */
        let authenticated = store.getters['auth/authenticated']

        if (to.matched.some(record => record.meta.requiresLogin) && authenticated) {
            return next({name: 'home'})
        }

    })
}
