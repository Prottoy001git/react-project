import axios from "axios";

const baseApiUrl = "http://localhost/php-react-api/api/";   //use hosting URL for online project

const api = axios.create({
    baseURL: baseApiUrl,
    headers:{
        "Content-Type": "application/json",
        "Authorization": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJuYW1lIjoiUHJvdHRveSIsImVtYWlsIjoicHJvdHRveUBtYWlsLmNvbSIsInJvbGUiOiJBZG1pbiIsImlhdCI6MTc1OTk5MTA5MiwiZXhwIjoxNzYwNTk1ODkyfQ.-wSBuZDQiT--u9mMoWO2a0BsFytWriqfXnVWXxc8x4g"
    }
});


export default api;