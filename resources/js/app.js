require('./bootstrap');

import { createApp } from 'vue'

import CarouselComponent from './components/CarouselComponent'
import GameSelectComponent from './components/GameSelectComponent'
import GroupComponent from './components/GroupComponent'
import LoginComponent from './components/LoginComponent'
import FollowComponent from './components/FollowComponent'
import FollowerComponent from './components/FollowerComponent'
import MovieComponent from './components/MovieComponent'
import MovieSearchComponent from './components/MovieSearchComponent'
import RoomComponent from './components/RoomComponent'
import RoomMessageViewerComponent from './components/RoomMessageViewerComponent'
import RoomRankingComponent from './components/RoomRankingComponent'
import SearchComponent from './components/SearchComponent'
import UserComponent from './components/UserComponent'

createApp({
    components: {
        'carousel-component': CarouselComponent,
        'game-select-component': GameSelectComponent,
        'group-component': GroupComponent,
        'login-component': LoginComponent,
        'follow-component': FollowComponent,
        'follower-component': FollowerComponent,
        'movie-component': MovieComponent,
        'movie-search-component': MovieSearchComponent,
        'room-component': RoomComponent,
        'room-message-viewer-component': RoomMessageViewerComponent,
        'room-ranking-component': RoomRankingComponent,
        'search-component': SearchComponent,
        'user-component': UserComponent,
    },
}).mount('#app')
