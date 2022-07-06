<template>
    <div>
<!--
        <ul class="tabs">
            <li class="active"><a href="#">ショート動画</a></li>
            <li><a href="#">ユーザー</a></li>
            <li><a href="#">配信動画</a></li>
        </ul>
-->

        <div class="movie-wrapper" v-if="activeTab === 1">
            <div class="movie-content">
                <div class="movie-box" v-for="movie in movies" :key="movie.id">
                    <div class="movie-image">
                        <a v-bind:href="'/movie/detail/' + movie.id" v-bind:style="{ backgroundImage: 'url(' + movie.image_path + ')' }"></a>
                        <div class="time">{{ timeFormat(movie.duration) }}</div>
                    </div>
                    <div class="movie-info">
                        <a v-bind:href="'/movie/detail/' + movie.id" class="movie-name">{{ movie.name }}</a>
                        <div class="user-name"><a v-bind:href="'/user/' + movie.user.id">{{ movie.user.name }}</a></div>
                    </div>
                </div>
            </div><!--// .movie-content -->
            <infinite-loading @infinite="tab1InfiniteHandler"></infinite-loading>
        </div><!--// .movie-wrapper -->

        <div class="room-wrapper" v-if="activeTab === 2">
            <div class="room-content">
                <div class="room-box" v-for="room in rooms" :key="room.id">
                    <div v-if="room.status=='1'" class="room-image">
                        <a v-bind:href="'/room/' + room.id" v-bind:style="{ backgroundImage: 'url(' + room.stream_image_path + ')' }"></a>
                        <span class="live">LIVE</span>
                    </div>
                    <div v-else-if="room.status=='2'" class="room-image">
                        <a v-bind:href="'/room/' + room.id" v-bind:style="{ backgroundImage: 'url(' + room.image_path + ')' }"></a>
                    </div>
                    <div class="room-info">
                        <a class="room-name" v-bind:href="'/room/' + room.id">{{ room.name }}</a>
                        <div class="user-name"><a v-bind:href="'/user/' + room.user.id">{{ room.user.name }}</a></div>
                    </div>
                </div>
            </div><!--// .room-content -->
            <infinite-loading @infinite="tab2InfiniteHandler"></infinite-loading>
        </div><!--// .room-wrapper -->

        <div class="user-wrapper" v-if="activeTab === 3">
            <div classs="user-content">
            </div><!-- .user-content -->
            <infinite-loading @infinite="tab3InfiniteHandler"></infinite-loading>
        </div><!--// .user-wrapper -->
    </div>
</template>

<script>
    import InfiniteLoading from 'vue-infinite-loading';

    export default {
        props: {
            activeTab: Number,
            q: String,
        },
        computed: {
        },
        data () {
            return {
                page: 1,
                movies: [],
                rooms: [],
                users: [],
            }
        },
        mounted () {
        },
        methods: {
            timeFormat(time) {
                let formatTime;
                if (time !== null) {
                    formatTime = time.slice(-5)
                }
                return formatTime;
            },
            tab1InfiniteHandler($state) {
                axios.get('/api/search-movie', {
                    params: {
                        page: this.page,
                        per_page: 1,
                        q: this.q
                    },
                }).then(({ data }) => {
                    if (data.data.length) {
                        this.page += 1
                        this.movies.push(...data.data)
                        $state.loaded();
                    } else {
                        $state.complete();
                    }
                }).catch((err) => {
                    $state.complete()
                })
            },
            tab2InfiniteHandler($state) {
                axios.get('/api/search-room', {
                    params: {
                        page: this.page,
                        per_page: 1,
                        q: this.q
                    },
                }).then(({ data }) => {
                    if (data.data.length) {
                        this.page += 1
                        this.rooms.push(...data.data)
                        $state.loaded();
                    } else {
                        $state.complete();
                    }
                }).catch((err) => {
                    $state.complete()
                })
            },
            tab3InfiniteHandler($state) {
            },
        }
    }
</script>

