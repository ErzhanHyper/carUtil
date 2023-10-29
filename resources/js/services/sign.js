import {NCALayerClient} from "ncalayer-js-client";

export async function signData() {
    const ncalayerClient = new NCALayerClient();
    try {
        await ncalayerClient.connect();
    } catch (error) {
        console.log(error.toString());
        alert(`Не удалось подключиться к NCALayer: ${error.toString()}`);
        return;
    }

    let activeTokens;
    try {
        activeTokens = await ncalayerClient.getActiveTokens();
    } catch (error) {
        console.log(error.toString());
        return;
    }

    // getActiveTokens может вернуть несколько типов хранилищ, для простоты проверим первый.
    // Иначе нужно просить пользователя выбрать тип носителя.
    const storageType = activeTokens[0] || NCALayerClient.fileStorageType;

    let base64EncodedSignature;
    try {
        base64EncodedSignature = await ncalayerClient.createCAdESFromBase64(storageType, 'MTEK');
    } catch (error) {
        return;
    }

    return base64EncodedSignature
}


export async function secureData() {
    const ncalayerClient = new NCALayerClient();

    try {
        await ncalayerClient.connect();
    } catch (error) {
        alert(`Не удалось подключиться к NCALayer: ${error.toString()}`);
        return;
    }

    let activeTokens;
    try {
        activeTokens = await ncalayerClient.getActiveTokens();
    } catch (error) {
        console.log(error.toString());
        return;
    }

    // getActiveTokens может вернуть несколько типов хранилищ, для простоты проверим первый.
    // Иначе нужно просить пользователя выбрать тип носителя.
    const storageType = activeTokens[0] || NCALayerClient.fileStorageType;

    let base64EncodedSignature;
    try {
        base64EncodedSignature = await ncalayerClient.getKeyInfo(storageType, 'MTEK');
    } catch (error) {
        return;
    }

    return base64EncodedSignature
}
