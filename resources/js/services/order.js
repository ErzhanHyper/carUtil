import api from "../api";

export function getOrderList(params) {
    return new Promise((resolve, reject) => {
        api.get('/order', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getOrderItem(id, params) {
    return new Promise((resolve, reject) => {
        api.get('/order/' + id + '/get', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function sendToSignOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/order/'+id+'/sign', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function sendToApproveOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/order/'+id+'/send', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function storeCertOrder(params) {
    return new Promise((resolve, reject) => {
        api.post('/order/cert', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function approveOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/order/'+id+'/approve', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function declineOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/order/'+id+'/decline', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function revisionOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/order/'+id+'/revision', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function revisionVideoOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/order/'+id+'/revisionVideo', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function executeRunOrder(id) {
    return new Promise((resolve, reject) => {
        api.put('/order/'+id+'/executeRun').then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function executeCloseOrder(id) {
    return new Promise((resolve, reject) => {
        api.put('/order/'+id+'/executeClose').then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function sendVideoOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.post('/order/'+id+'/video', params, {headers: {'Content-Type': 'multipart/form-data'}}).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
