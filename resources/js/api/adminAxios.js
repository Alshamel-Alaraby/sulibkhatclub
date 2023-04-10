import axios from "axios";
import Cookies from "js-cookie";


const adminApi = axios.create({
    baseURL: `${process.env.MIX_APP_URL}api/`
});

adminApi.interceptors.request.use(
    function (config) {
        config.headers['lang'] = localStorage.getItem("lang") || 'ar';
        config.headers['Authorization'] = "Bearer " + (Cookies.get("token") || '');
        config.headers['company-id'] = JSON.parse(localStorage.getItem("company_id"));
        if (JSON.parse(localStorage.getItem("type"))){
            config.headers['admin_id'] =  JSON.parse(localStorage.getItem("type")) == "admin" ? JSON.parse(localStorage.getItem("partner")).id : null;
            config.headers['user_id'] = JSON.parse(localStorage.getItem("type")) == "admin" ? null : JSON.parse(localStorage.getItem("user")).id;
        }
        return config;
    },
    function (error) {
        return Promise.reject(error);
    }
);
adminApi.defaults.headers.common['secretApi'] = 'Snr92EUKCmrE06PiJ';
adminApi.defaults.headers.common['Accept'] = 'application/json';

// end axios
export default adminApi
