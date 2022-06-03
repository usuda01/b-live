require('./bootstrap');

import Vue from 'vue'
window.Vue = Vue;

Vue.component('game-select-component', require('./components/GameSelectComponent.vue').default);
Vue.component('login-component', require('./components/LoginComponent.vue').default);
Vue.component('follow-component', require('./components/FollowComponent.vue').default);
Vue.component('follower-component', require('./components/FollowerComponent.vue').default);
Vue.component('movie-component', require('./components/MovieComponent.vue').default);
Vue.component('room-component', require('./components/RoomComponent.vue').default);
Vue.component('room-message-component', require('./components/RoomMessageComponent.vue').default);
Vue.component('room-message-viewer-component', require('./components/RoomMessageViewerComponent.vue').default);
Vue.component('room-ranking-component', require('./components/RoomRankingComponent.vue').default);
Vue.component('user-component', require('./components/UserComponent.vue').default);

const app = new Vue({
    el: '#app',
});
