import api from "../api";

export function getFactoryList(params) {
    return new Promise((resolve, reject) => {
        api.get('/factory', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
