import api from "../api";

export function getLogList(params) {
    return new Promise((resolve, reject) => {
        api.get('/log', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
