import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-with'] = 'XMLHttpRequest';
