import api from "../api";

export function getCertReport(params) {
    return new Promise((resolve, reject) => {
        api.get('/report/cert', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getSellReport(params) {
    return new Promise((resolve, reject) => {
        api.get('/report/sell', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getExchangeReport(params) {
    return new Promise((resolve, reject) => {
        api.get('/report/exchange', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getActionReport(params) {
    return new Promise((resolve, reject) => {
        api.get('/report/action', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
