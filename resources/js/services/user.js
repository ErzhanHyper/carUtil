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

export function validUser(params) {
    return new Promise((resolve, reject) => {
        api.post('/validUser', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function updateUser(params) {
    return new Promise((resolve, reject) => {
        api.post('/user/update', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
