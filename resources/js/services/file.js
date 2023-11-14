import api from "../api";

export function getFileTypeList(params) {
    return new Promise((resolve, reject) => {
        api.get('/fileType', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getFileTypeAgroList(params) {
    return new Promise((resolve, reject) => {
        api.get('/fileTypeAgro', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getPreOrderFileList(params) {
    return new Promise((resolve, reject) => {
        api.post('/file/preorder/get', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getOrderFileList(params) {
    return new Promise((resolve, reject) => {
        api.post('/file/order/get', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function deletePreOrderFile(params) {
    return new Promise((resolve, reject) => {
        api.post('/file/preorder/delete', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function deleteOrderFile(id) {
    return new Promise((resolve, reject) => {
        api.delete('/file/' + id + '/order').then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function storePreOrderFile(params) {
    return new Promise((resolve, reject) => {
        api.post('/file/preorder/store', params,  {headers: {'Content-Type': 'multipart/form-data'}}).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function storeOrderFile(params) {
    return new Promise((resolve, reject) => {
        api.post('/file/order/store', params,  {headers: {'Content-Type': 'multipart/form-data'}}).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function generateOrderPFS(id, params) {
    return new Promise((resolve, reject) => {
        api.get('file/order/'+id+'/generatePFS/', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

