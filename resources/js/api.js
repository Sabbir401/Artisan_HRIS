import axios from "axios";
import router from "./router";
import { useStore } from 'vuex'

const store = useStore();

const api = axios.create({ baseURL: "/api/" });

api.interceptors.request.use(
  function (config) {
    const token = localStorage.getItem("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
      config.headers["X-CSRF-TOKEN"] = csrfToken.content;
    }
    return config;
  },
  function (error) {
    return Promise.reject(error);
  }
);

api.interceptors.response.use(
  function (response) {
    return response;
  },
  function (error) {
    if (error.response.status === 401) {
      router.push({ name: "Login" });
      localStorage.removeItem("token");
      store.dispatch('removeToken', 0);
    }
    return Promise.reject(error);
  }
);

export default api;



// import axios from "axios";
// import router from "./router";

// const api = axios.create({ baseURL: "/api/" });

// if (localStorage.getItem("token")) {
//     let access_token = localStorage.getItem("token");
//     api.defaults.headers.common["Authorization"] = `Bearer ${access_token}`;

//     let token = document.querySelector('meta[name="csrf-token"]');
//     if (token) {
//         api.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
//     }
// }


// api.interceptors.response.use(
//     function (response) {
//         return response;
//     },
//     function (error) {
//         if (error.response.status == 401) {
//             localStorage.removeItem("token");
//             router.push({ name: "Login" });
//         }
//         return Promise.reject(error);
//     }
// );

// export default api;
