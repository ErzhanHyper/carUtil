import api from "../api";

export function getStatementDoc(id, params) {
    return new Promise((resolve, reject) => {
        api.get('/document/order/'+id+'/statement', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getContractDoc(id, params) {
    return new Promise((resolve, reject) => {
        api.get('/document/order/'+id+'/contract', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getExchangeApp(id, params) {
    return new Promise((resolve, reject) => {
        api.get('/document/exchange/'+id+'/application', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}

export function getTransferContract(id, params) {
    return new Promise((resolve, reject) => {
        api.get('/document/transfer/'+id+'/contract', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
