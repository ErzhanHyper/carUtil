import api from "../api";

export function getManufactureList(params) {
    return new Promise((resolve, reject) => {
        api.get('/manufacture', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function storeManufacture(params) {
    return new Promise((resolve, reject) => {
        api.post('/manufacture', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getManufactureById(id) {
    return new Promise((resolve, reject) => {
        api.get('/manufacture/'+id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function updateManufacture(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/manufacture/'+id, params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function deleteManufacture(id) {
    return new Promise((resolve, reject) => {
        api.delete('/manufacture/'+id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
