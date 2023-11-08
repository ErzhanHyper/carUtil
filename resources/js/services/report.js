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
