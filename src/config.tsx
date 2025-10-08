import axios from "axios";

const baseApiUrl = "http://localhost/php-react-api/api/";   //use hosting URL for online project

const api = axios.create({
    baseURL: baseApiUrl,
    headers:{
        "Content-Type": "application/json",
        "Authorization": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJuYW1lIjoiUHJvdHRveSIsImVtYWlsIjoicHJvdHRveUBtYWlsLmNvbSIsInJvbGUiOiJBZG1pbiIsImlhdCI6MTc1OTkwMTYyOSwiZXhwIjoxNzU5OTAxNjg5fQ.NDrW_s_Bij9DcmQSxsmIEEIxqnewaCrz9I8eFSCEqGk"
    }
});


export default api;