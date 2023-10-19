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
