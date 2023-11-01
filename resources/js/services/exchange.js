import api from "../api";

export function getExchangeList(params) {
    return new Promise((resolve, reject) => {
        api.get('/exchange', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getExchangeById(id) {
    return new Promise((resolve, reject) => {
        api.get('/exchange/'+id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function storeExchange(params) {
    return new Promise((resolve, reject) => {
        api.post('/exchange', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally((response) => {
        })
    })
}

export function updateExchange(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/exchange/'+id, params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function approveExchange(id) {
    return new Promise((resolve, reject) => {
        api.put('/exchange/'+id+'/approve').then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function declineExchange(id) {
    return new Promise((resolve, reject) => {
        api.put('/exchange/'+id+'/decline').then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function storeExchangeFile(params) {
    return new Promise((resolve, reject) => {
        api.post('/exchange/storeFile', params, {headers: {'Content-Type': 'multipart/form-data'}}).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject(e.response.data)
        }).finally(() => {
        })
    })
}

export function getExchangeFile(params) {
    return new Promise((resolve, reject) => {
        api.post('/exchange/getFile', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function deleteExchangeFile(params) {
    return new Promise((resolve, reject) => {
        api.post('/exchange/deleteFile', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
