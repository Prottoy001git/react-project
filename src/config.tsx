import axios from "axios";

const baseApiUrl = "http://localhost/php-react-api/api/";   //use hosting URL for online project

const api = axios.create({
    baseURL: baseApiUrl,
    headers:{
        "Content-Type": "application/json"
    }
});


export default api;