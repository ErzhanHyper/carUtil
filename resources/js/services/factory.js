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

export function getFactoryById(id) {
    return new Promise((resolve, reject) => {
        api.get('/factory/'+id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function updateFactory(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/factory/'+id, params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function deleteFactory(id) {
    return new Promise((resolve, reject) => {
        api.delete('/factory/'+id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function storeFactory(params) {
    return new Promise((resolve, reject) => {
        api.post('/factory', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
