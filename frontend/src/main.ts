import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './core/router';
import { setupPrimeVue } from './core/plugins/primevue';
import './style.css';
import 'primeicons/primeicons.css';

const app = createApp(App);

app.use(createPinia());
app.use(router);
setupPrimeVue(app);

app.mount('#app');
