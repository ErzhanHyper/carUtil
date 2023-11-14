import api from "../api";

export function storeTransfer(params) {
    return new Promise((resolve, reject) => {
        api.post('/transfer/order', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getTransferList(params) {
    return new Promise((resolve, reject) => {
        api.get('/transfer/order', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getTransferCurrentList(params) {
    return new Promise((resolve, reject) => {
        api.get('/transfer/orderCurrent', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getTransferById(id) {
    return new Promise((resolve, reject) => {
        api.get('/transfer/order/' + id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function closeTransfer(id) {
    return new Promise((resolve, reject) => {
        api.delete('/transfer/order/'+id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getTransferDealList(params) {
    return new Promise((resolve, reject) => {
        api.get('/transfer/deal', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function storeTransferDeal(params) {
    return new Promise((resolve, reject) => {
        api.post('/transfer/deal', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject(e.response.data)
        }).finally(() => {
        })
    })
}

export function acceptTransferDeal(id) {
    return new Promise((resolve, reject) => {
        api.put('/transfer/deal/'+id+'/accept').then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function closeTransferDeal(id) {
    return new Promise((resolve, reject) => {
        api.put('/transfer/deal/'+id + '/close').then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function signTransferOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/transfer/order/'+id+'/sign', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
