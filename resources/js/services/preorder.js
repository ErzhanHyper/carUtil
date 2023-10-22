import api from "../api";

export function getOrderList(params) {
    return new Promise((resolve, reject) => {
        api.get('/preorder', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getOrderItem(id, params) {
    return new Promise((resolve, reject) => {
        api.get('/preorder/' + id + '/get', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function storeOrder(params) {
    return new Promise((resolve, reject) => {
        api.post('/preorder/store', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function sendOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.post('/preorder/'+id+'/send', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject(e.response.data)
        }).finally(() => {
        })
    })
}

export function deleteOrder(params) {
    return new Promise((resolve, reject) => {
        api.post('/preorder/delete', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function checkVehicle(params) {
    return new Promise((resolve, reject) => {
        api.post('/preorder/checkVehicle', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function moderatorApproveOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.post('/preorder/'+id+'/moderator/approve', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function bookingOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.post('/preorder/' + id + '/booking', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject(e)
        }).finally(() => {
        })
    })
}
