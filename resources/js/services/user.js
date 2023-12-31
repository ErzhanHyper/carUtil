import api from "../api";

export function logout(params) {
    return new Promise((resolve, reject) => {
        api.post('/logout', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}


export function getUser(params) {
    return new Promise((resolve, reject) => {
        api.post('/user', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getUserCollection(params) {
    return new Promise((resolve, reject) => {
        api.get('/user', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getUserById(id) {
    return new Promise((resolve, reject) => {
        api.get('/user/'+id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject(e)
        }).finally(() => {
        })
    })
}

export function updateProfile(params) {
    return new Promise((resolve, reject) => {
        api.post('/profile/update', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject(e)
        }).finally(() => {
        })
    })
}

export function storeUser(params) {
    return new Promise((resolve, reject) => {
        api.post('/user/store', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject(e)
        }).finally(() => {
        })
    })
}

export function updateUser(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/user/'+id+'/update', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject(e)
        }).finally(() => {
        })
    })
}

export function getRoleList(params) {
    return new Promise((resolve, reject) => {
        api.get('/role', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
