import api from "../api";

export function findCertificateById(id, params) {
    return new Promise((resolve, reject) => {
        api.get('/checkup/'+id+'/getCert', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function findCertificateByOrderId(id, params) {
    return new Promise((resolve, reject) => {
        api.get('/checkup/'+id+'/downloadCert', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
