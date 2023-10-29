import api from "../api";

export function storeTransfer(params) {
    return new Promise((resolve, reject) => {
        api.post('/transfer/order/store', params).then(response => {
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

export function getTransferItem(id) {
    return new Promise((resolve, reject) => {
        api.get('/transfer/order/' + id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function closeTransfer(params) {
    return new Promise((resolve, reject) => {
        api.post('/transfer/order/close', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getTransferDealList(params) {
    return new Promise((resolve, reject) => {
        api.post('/transfer/deal', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function storeTransferDeal(params) {
    return new Promise((resolve, reject) => {
        api.post('/transfer/deal/store', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject(e.response.data)
        }).finally(() => {
        })
    })
}

export function acceptTransferDeal(id) {
    return new Promise((resolve, reject) => {
        api.post('/transfer/deal/accept/'+id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function closeTransferDeal(id) {
    return new Promise((resolve, reject) => {
        api.post('/transfer/deal/close/'+id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function signTransferOrder(params) {
    return new Promise((resolve, reject) => {
        api.post('/transfer/order/sign', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getTransferOrderPfs(params) {
    return new Promise((resolve, reject) => {
        api.post('/transfer/order/pfs', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
