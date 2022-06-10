<template>
    <div class="movie-detail">
        <div class="main-area">
            <div class="bar">
                <a v-bind:href="'/user/' + movie.user.id" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + movie.user.image_path + ')' }"></a>
                <a v-bind:href="'/user/' + movie.user.id" class="user-name">{{ movie.user.name }}</a>
                <span v-if="movie.user.user_data.rank==2" class="user-rank rank2">推し</span>
                <span v-else-if="movie.user.user_data.rank==5" class="user-rank rank5">公認配信者</span>
            </div>

            <video :poster="movie.image_path" controls playsinline>
                <source v-bind:src="'/storage/movies/'+this.movie.path"></source>
            </video>

            <div class="video-footer">
                <div class="share-content"><a v-bind:href="'http://twitter.com/intent/tweet?text=' + encodeURIComponent(movie.name + '\n' + locationUrl + '\n' + snsTags + '\n@BLIVE77191685 にて')" target="_blank"><img src="/images/btn-share-twitter.png"></a></div>
            </div>
            <div class="video-info">
                <div class="movie-title">{{ movie.name }}</div>
                <div class="headline">
                    <a v-if="isGood" class="good" v-on:click.prevent="goodCancel" href="#"><i class="fas fa-heart"></i></a>
                    <a v-else class="good" v-on:click.prevent="good" href="#"><i class="far fa-heart"></i></a>
                    <div class="good-count">{{ goodCount }}</div>
                </div>
            </div>
        </div>
        <div class="right-area">
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            movie: Object,
            user: Object
        },
        computed: {
            snsTags: function () {
                let result = '#BLIVE動画 #ゲーム動画';
                if (this.movie.game) {
                    let tagsArray = this.movie.game.sns_tags.split(',');
                    let tagsJoin = '';
                    for (let i = 0; i < tagsArray.length; i ++) {
                        tagsJoin += ' #' + tagsArray[i];
                    }
                    result += tagsJoin;
                }
                return result;
            },
        },
        data () {
            return {
                isGood: false,
                goodCount: 0,
                locationUrl: location.href,
            }
        },
        mounted () {
            this.getMovieGoods();
        },
        methods: {
            getMovieGoods() {
                const url = '/api/movie/get-goods/'+this.movie.id;
                const params = { params: {
                }};
                axios.get(url, params)
                    .then((response) => {
                        this.goodCount = response.data.length;

                        // 自分がフォローしているかどうかのチェック
                        for (let i = 0; i < response.data.length; i ++) {
                            if (response.data[i].user_id == this.user.id) {
                                this.isGood = true;
                                break;
                            }
                        }
                    });
            },
            good() {
                if (!Object.keys(this.user).length) {
                    // 未ログイン
                    alert('ログインしてください');
                    return false;
                }
                const url = '/api/movie/good';
                const params = {
                    movie_id: this.movie.id
                };
                axios.post(url, params)
                    .then((response) => {
                        this.isGood = true;
                        this.goodCount ++;
                    });
            },
            goodCancel() {
                if (!Object.keys(this.user).length) {
                    // 未ログイン
                    alert('ログインしてください');
                    return false;
                }
                const url = '/api/movie/good-cancel';
                const params = {
                    movie_id: this.movie.id
                };
                axios.post(url, params)
                    .then((response) => {
                        this.isGood = false;
                        this.goodCount --;
                    });
            },
        }
    }
</script>

