import api from "../api";

export function getCertificateList(params) {
    return new Promise((resolve, reject) => {
        api.get('/certificate', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function generateCertificate(id, params) {
    return new Promise((resolve, reject) => {
        api.get('/certificate/'+id+'/get', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
