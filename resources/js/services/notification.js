import api from "../api";

export function getNotificationList(params) {
    return new Promise((resolve, reject) => {
        api.get('/notification', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
