import api from "../api";

export function getOrderList(params) {
    return new Promise((resolve, reject) => {
        api.get('/order', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getOrderItem(id, params) {
    return new Promise((resolve, reject) => {
        api.get('/order/' + id + '/get', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function signOrder(params) {
    return new Promise((resolve, reject) => {
        api.post('/order/sign', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function approveOrder(params) {
    return new Promise((resolve, reject) => {
        api.post('/order/approve', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function storeCertOrder(params) {
    return new Promise((resolve, reject) => {
        api.post('/order/cert', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

