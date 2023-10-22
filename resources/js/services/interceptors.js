/**
 * This code block can be directly in your main.js file if you keep it this simple
 * otherwise extract it to its own file and require it in main.js
 *
 * Don't forget that the code below will be executed within every request.
 *
 */
import api from "../api";
import emitter from "../mitt";

// let apiUrl = 'http://127.0.0.1:8000/app';
let apiUrl = 'https://dev-auto.recycle.kz/app';

let disabled_loaded_content = [
    apiUrl + '/user',
    apiUrl + '/region',
    apiUrl + '/factory',
    apiUrl + '/file/order/get',
    apiUrl + '/fileType',
];

api.interceptors.request.use(function (config) {
    emitter.emit('contentLoaded', true);
    if(disabled_loaded_content.indexOf(config.url) > -1){
        emitter.emit('contentLoaded', false);
    }
    return config;
});

api.interceptors.response.use(
    (response) => {
        if(!disabled_loaded_content.indexOf(response.config.url) > -1) {
            emitter.emit('contentLoaded', false);
        }
        return response
    },
    (err) => {
        if(err.response) {
            if (err.response.statusText === 'Unauthorized') {

            }
        }

        return Promise.reject(err);
    }
)
