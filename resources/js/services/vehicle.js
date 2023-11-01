import api from "../api";

export function getVehicleList(params) {
    return new Promise((resolve, reject) => {
        api.get('/vehicle', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getVehicleById(id) {
    return new Promise((resolve, reject) => {
        api.get('/vehicle/'+id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function updateVehicle(id, params) {
    return new Promise((resolve, reject) => {
        api.put('/vehicle/'+id, params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function deleteVehicle(id) {
    return new Promise((resolve, reject) => {
        api.delete('/vehicle/'+id).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
