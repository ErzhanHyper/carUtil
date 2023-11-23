import api from "../api";

export function getCategoryList(params) {
    return new Promise((resolve, reject) => {
        api.get('/category', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getCategoryComplectList(params) {
    return new Promise((resolve, reject) => {
        api.get('/category/complect', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
