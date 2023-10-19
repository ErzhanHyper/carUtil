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
