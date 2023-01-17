require('./bootstrap');

import Vue from 'vue'
window.Vue = Vue;

Vue.component('carousel-component', require('./components/CarouselComponent.vue').default);
Vue.component('game-select-component', require('./components/GameSelectComponent.vue').default);
Vue.component('group-component', require('./components/GroupComponent.vue').default);
Vue.component('login-component', require('./components/LoginComponent.vue').default);
Vue.component('follow-component', require('./components/FollowComponent.vue').default);
Vue.component('follower-component', require('./components/FollowerComponent.vue').default);
Vue.component('movie-component', require('./components/MovieComponent.vue').default);
Vue.component('movie-search-component', require('./components/MovieSearchComponent.vue').default);
Vue.component('room-component', require('./components/RoomComponent.vue').default);
Vue.component('room-message-component', require('./components/RoomMessageComponent.vue').default);
Vue.component('room-message-viewer-component', require('./components/RoomMessageViewerComponent.vue').default);
Vue.component('room-ranking-component', require('./components/RoomRankingComponent.vue').default);
Vue.component('search-component', require('./components/SearchComponent.vue').default);
Vue.component('user-component', require('./components/UserComponent.vue').default);

const app = new Vue({
    el: '#app',
});
