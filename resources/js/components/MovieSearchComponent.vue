<template>
    <div>
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
        <infinite-loading  @infinite="infiniteHandler"></infinite-loading>
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
                page: 1,
            }
        },
        mounted () {
        },
        methods: {
            infiniteHandler($state) {
                axios.get('/api/search-movie', {
                    params: {
                        page: this.page,
                        per_page: 1,
                        q: this.game.name
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
        }
    }
</script>

