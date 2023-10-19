import api from "../api";

export function getRegionList(params) {
    return new Promise((resolve, reject) => {
        api.get('/region', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
