<template>
    <div>
        <ul class="tabs">
            <li v-on:click="tabChange(1)" v-bind:class="{'active': activeTab === 1}">人気順</li>
            <li v-on:click="tabChange(2)" v-bind:class="{'active': activeTab === 2}">新着順</li>
        </ul>

        <div class="movie-wrapper" v-if="activeTab === 1">
            <div class="movie-content">
                <div class="movie-box" v-for="movie in movies" :key="movie.id">
                    <div class="game-title">
                        <template v-if="movie.game">
                            <a v-bind:href="'/movie/search?game_id=' + movie.game.id">{{ movie.game.name }}</a>
                        </template>
                    </div>
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
        </div>

        <div class="movie-wrapper" v-else-if="activeTab === 2">
            <div class="movie-content">
                <div class="movie-box" v-for="movie in movies" :key="movie.id">
                    <div class="game-title">
                        <template v-if="movie.game">
                            <a v-bind:href="'/movie/search?game_id=' + movie.game.id">{{ movie.game.name }}</a>
                        </template>
                    </div>
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
            <infinite-loading :identifier="2" @infinite="tab2InfiniteHandler"></infinite-loading>
        </div>

    </div>
</template>

<script>
    import InfiniteLoading from 'vue-infinite-loading';

    export default {
        props: {
            game: Object,
        },
        data () {
            return {
                activeTab: 1,
                movies: [],
                tab1Page: 1,
                tab2Page: 1,
            }
        },
        mounted () {
        },
        methods: {
            tabChange(num) {
                this.activeTab = num

                // 初期化する
                this.movies = [];
                this.tab1Page = 1;
                this.tab2Page = 1;
            },
            tab1InfiniteHandler($state) {
                axios.get('/api/search-movie', {
                    params: {
                        page: this.tab1Page,
                        per_page: 1,
                        q: this.game.name,
                        sort: 'popular'
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
                    $state.complete();
                })
            },
            tab2InfiniteHandler($state) {
                axios.get('/api/search-movie', {
                    params: {
                        page: this.tab2Page,
                        per_page: 1,
                        q: this.game.name,
                        sort: 'latest'
                    },
                }).then(({ data }) => {
                    if (data.data.length) {
                        this.tab2Page += 1
                        this.movies.push(...data.data)
                        $state.loaded();
                    } else {
                        $state.complete();
                    }
                }).catch((err) => {
                    $state.complete();
                })
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

