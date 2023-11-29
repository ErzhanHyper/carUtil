import api from "../api";

export function getBookingOrderList(params) {
    return new Promise((resolve, reject) => {
        api.post('/booking/order', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function deleteBookingOrder(params) {
    return new Promise((resolve, reject) => {
        api.post('/booking/order/delete', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}


export function bookingOrder(params) {
    return new Promise((resolve, reject) => {
        api.post('/booking/store', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject(e)
        }).finally(() => {
        })
    })
}
