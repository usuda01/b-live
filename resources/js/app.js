require('./bootstrap');

import { createApp } from 'vue'
import LoginComponent from './components/LoginComponent.vue'
import RoomComponent from './components/RoomComponent.vue'

createApp({
    components: {
        LoginComponent,
        RoomComponent
    }
}).mount('#app')
