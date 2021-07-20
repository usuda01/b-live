require('./bootstrap');

import { createApp } from 'vue'
import LoginComponent from './components/LoginComponent.vue'

createApp({
    components: {
        LoginComponent
    }
}).mount('#app')
