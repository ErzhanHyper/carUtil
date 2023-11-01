import api from "../api";

export function getManufactoryList(params) {
    return new Promise((resolve, reject) => {
        api.get('/manufactory', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
