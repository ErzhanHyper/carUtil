/**
 * This code block can be directly in your main.js file if you keep it this simple
 * otherwise extract it to its own file and require it in main.js
 *
 * Don't forget that the code below will be executed within every request.
 *
 */
import api from "../api";
import emitter from "../mitt";

let apiUrl = 'http://localhost:8060/app';
// let apiUrl = 'https://dev-auto.recycle.kz/app';

// let disabled_loaded_content = [
//     '/preorder/checkVehicle',
//     '/user',
//     '/region',
//     '/factory',
//    '/file/order/get',
//     '/file/preorder/get',
//     '/fileType',
//     '/booking/order',
//     '/file/preorder/store',
//     '/file/order/store',
//     '/exchange/deleteFile',
//     '/exchange/getFile',
//     '/exchange/storeFile',
// ];

api.interceptors.request.use(function (config) {
    // console.log(config.url)
    // if(disabled_loaded_content.indexOf(config.url) > -1){
    //     emitter.emit('contentLoaded', false);
    // }else{
    //     emitter.emit('contentLoaded', true);
    // }
    return config;
});

api.interceptors.response.use(
    (response) => {
        // if(!disabled_loaded_content.indexOf(response.config.url) > -1) {
            emitter.emit('contentLoaded', false);
        // }
        return response
    },
    (err) => {
        emitter.emit('contentLoaded', false);
        if(err.response) {
            if (err.response.statusText === 'Unauthorized') {

            }
        }

        return Promise.reject(err);
    }
)
