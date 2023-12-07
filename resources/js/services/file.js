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
            reject(e.response.data)
        }).finally(() => {
        })
    })
}

export function storeOrderFile(params) {
    return new Promise((resolve, reject) => {
        api.post('/file/order/store', params,  {headers: {'Content-Type': 'multipart/form-data'}}).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject(e.response.data)
        }).finally(() => {
        })
    })
}

export function getOrderFile(id, params) {
    return new Promise((resolve, reject) => {
        api.get('file/'+id+'/order/doc', params).then(response => {
            resolve(response)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}


export function getOrderVideo(id, params) {
    return new Promise((resolve, reject) => {
        api.get('file/'+id+'/order/video', params).then(response => {
            resolve(response)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getOrderImage(id, params) {
    return new Promise((resolve, reject) => {
        api.get('file/'+id+'/order/image', params).then(response => {
            resolve(response)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getCarFile(id, params) {
    return new Promise((resolve, reject) => {
        api.get('file/'+id+'/preorder/carFile', params).then(response => {
            resolve(response)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getAgroFile(id, params) {
    return new Promise((resolve, reject) => {
        api.get('file/'+id+'/preorder/agroFile', params).then(response => {
            resolve(response)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getCarFileImage(id, params) {
    return new Promise((resolve, reject) => {
        api.get('file/'+id+'/preorder/carImage', params).then(response => {
            resolve(response)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getAgroFileImage(id, params) {
    return new Promise((resolve, reject) => {
        api.get('file/'+id+'/preorder/agroImage', params).then(response => {
            resolve(response)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function downloadSellFile(id, params) {
    return new Promise((resolve, reject) => {
        api.get('file/'+id+'/sell/download', params).then(response => {
            resolve(response)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
