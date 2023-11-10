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

export function getPreorderById(id) {
    return new Promise((resolve, reject) => {
        api.get('/preorder/' + id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function storeOrder(params) {
    return new Promise((resolve, reject) => {
        api.post('/preorder', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function sendOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/preorder/'+id+'/send', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject(e.response.data)
        }).finally(() => {
        })
    })
}

export function deletePreorder(id) {
    return new Promise((resolve, reject) => {
        api.delete('/preorder/'+id).then(response => {
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

export function approveOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/preorder/'+id+'/approve', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function declineOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/preorder/'+id+'/decline', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function revisionOrder(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/preorder/'+id+'/revision', params).then(response => {
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
