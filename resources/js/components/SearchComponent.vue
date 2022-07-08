<template>
    <div>
        <ul class="tabs">
            <li v-bind:class="{'active': activeTab === 1}"><a href="#" v-on:click.prevent="tabChange(1)">ショート動画</a></li>
            <li v-bind:class="{'active': activeTab === 3}"><a href="#" v-on:click.prevent="tabChange(3)">ユーザー</a></li>
            <!--<li><a href="#">配信動画</a></li>-->
        </ul>

        <div v-if="activeTab===1" class="movie-wrapper" v-bind:class="{'active': activeTab === 1}">
            <div class="movie-content">
                <div class="movie-box" v-for="movie in movies" :key="movie.id">
                    <div class="movie-image">
                        <a v-bind:href="'/movie/detail/' + movie.id" v-bind:style="{ backgroundImage: 'url(' + movie.image_path + ')' }"></a>
                        <div class="time">{{ timeFormat(movie.duration) }}</div>
                        <div class="views">{{ countFormat(movie.views) }} 回視聴</div>
                    </div>
                    <div class="movie-info">
                        <a v-bind:href="'/movie/detail/' + movie.id" class="movie-name">{{ movie.name }}</a>
                        <div class="user-name"><a v-bind:href="'/user/' + movie.user.id">{{ movie.user.name }}</a></div>
                    </div>
                </div>
            </div><!--// .movie-content -->
            <infinite-loading :identifier="1" @infinite="tab1InfiniteHandler"></infinite-loading>
        </div><!--// .movie-wrapper -->

        <div v-else-if="activeTab === 2" class="room-wrapper" v-bind:class="{'active': activeTab === 2}">
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
            <infinite-loading :identifier="2" @infinite="tab2InfiniteHandler"></infinite-loading>
        </div><!--// .room-wrapper -->

        <div v-else-if="activeTab === 3" class="user-wrapper" v-bind:class="{'active': activeTab === 3}">
            <div class="user-content">
                <div class="user-box" v-for="user in users" :key="user.id">
                    <div class="user-image">
                        <a v-bind:href="'/user/' + user.id" v-bind:style="{ backgroundImage: 'url(' + user.user_image_path + ')' }"></a>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><a v-bind:href="'/user/' + user.id">{{ user.name }}</a></div>
                        <div class="user-profile">{{ user.profile }}</div>
                    </div>
                </div>
            </div><!-- .user-content -->
            <infinite-loading :identifier="3" @infinite="tab3InfiniteHandler"></infinite-loading>
        </div><!--// .user-wrapper -->
    </div>
</template>

<script>
    import InfiniteLoading from 'vue-infinite-loading';

    export default {
        props: {
            q: String,
        },
        computed: {
        },
        data () {
            return {
                activeTab: 1,
                movies: [],
                rooms: [],
                users: [],
                tab1Page: 1,
                tab2Page: 1,
                tab3Page: 1,
            }
        },
        mounted () {
        },
        methods: {
            tab1InfiniteHandler($state) {
                axios.get('/api/search-movie', {
                    params: {
                        page: this.tab1Page,
                        per_page: 1,
                        q: this.q
                    },
                }).then(({ data }) => {
                    if (data.data.length) {
                        this.tab1Page += 1
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
                        page: this.tab2Page,
                        per_page: 1,
                        q: this.q
                    },
                }).then(({ data }) => {
                    if (data.data.length) {
                        this.tab2Page += 1
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
                axios.get('/api/search-user', {
                    params: {
                        page: this.tab3Page,
                        per_page: 1,
                        q: this.q
                    },
                }).then(({ data }) => {
                    if (data.data.length) {
                        this.tab3Page += 1
                        this.users.push(...data.data)
                        $state.loaded();
                    } else {
                        $state.complete();
                    }
                }).catch((err) => {
                    $state.complete()
                })
            },
            tabChange(num) {
                this.activeTab = num
            },
            timeFormat(time) {
                let formatTime;
                if (time !== null) {
                    formatTime = time.slice(-5)
                }
                return formatTime;
            },
            countFormat(views) {
                let formatViews = views;
                return formatViews;
            },
        }
    }
</script>

