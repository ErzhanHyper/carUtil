import RedirectIfAuthenticated from './redirectIfAuthenticated.js'
import UserHasPermissions from './userHasPermissions'

export default function middleware(router) {
    // RedirectIfAuthenticated(router)
    UserHasPermissions(router)
}
