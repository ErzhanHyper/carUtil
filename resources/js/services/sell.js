import api from "../api";

export function getSellList(params) {
    return new Promise((resolve, reject) => {
        api.get('/sell', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getSellById(id) {
    return new Promise((resolve, reject) => {
        api.get('/sell/'+ id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getSellFilesById(id) {
    return new Promise((resolve, reject) => {
        api.get('/sell/'+ id + '/files').then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function storeSell(params) {
    return new Promise((resolve, reject) => {
        api.post('/sell', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function updateSell(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/sell/'+id, params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function updateToGetClose(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/sell/'+id+'/getClose', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function storeSellFile(params) {
    return new Promise((resolve, reject) => {
        api.post('sellFile', params, {headers: {'Content-Type': 'multipart/form-data'}}).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function deleteSellFile(id) {
    return new Promise((resolve, reject) => {
        api.delete('sellFile/'+id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function approveSell(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/sell/'+id+'/approve', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}


export function declineSell(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/sell/'+id+'/decline', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function messageSell(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/sell/'+id+'/message', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function closeSell(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/sell/'+id+'/close', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
