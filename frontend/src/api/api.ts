import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost:8000', // Update this based on your environment
    headers: {
        'Content-Type': 'application/json',
    },
});

// Injection of headers from localStorage
api.interceptors.request.use((config) => {
    const customerId = localStorage.getItem('customerId');
    const role = localStorage.getItem('role');

    if (customerId) {
        config.headers['X-Customer-Id'] = customerId;
    }
    if (role) {
        config.headers['X-Role'] = role;
    }

    return config;
});

export default api;
