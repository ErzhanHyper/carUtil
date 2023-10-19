import api from "../api";

export function getStatementDoc(params) {
    return new Promise((resolve, reject) => {
        api.post('/document/order/statement', params).then(response => {
            resolve(response.data)
        }).catch((e) => {
            reject('Ошибка при загрузке')
        }).finally(() => {
        })
    })
}
