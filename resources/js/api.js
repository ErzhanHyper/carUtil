import axios from 'axios';

export default axios.create({
    withCredentials: true,
    // baseURL: 'http://localhost:8060/app',
    baseURL: 'https://dev-auto.recycle.kz/app',
    headers: {
        "X-Requested-With": "XMLHttpRequest",
        "Access-Control-Allow-Origin": "*",
        "Content-Type": 'application/json',
    },
});
