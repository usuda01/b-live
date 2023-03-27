<template>
    <div class="movie-detail">
        <div class="main-area">
            <div class="bar">
                <a v-bind:href="'/user/' + movie.user.id" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + movie.user.image_path + ')' }"></a>
                <a v-bind:href="'/user/' + movie.user.id" class="user-name">{{ movie.user.name }}</a>
                <span v-if="movie.user.user_data.rank==2" class="user-rank rank2">推し</span>
                <span v-else-if="movie.user.user_data.rank==5" class="user-rank rank5">公認配信者</span>
            </div>

            <video v-on:play="onPlay" :poster="movie.image_path" controls playsinline>
                <source v-bind:src="'/storage/movies/'+this.movie.path">
            </video>

            <div class="video-footer">
                <div class="left-content">
                    <div class="view-content">
                        <img src="/images/icon-views.png" class="icon-views">
                        <span class="view-count-wrapper"><span class="view-count">{{ countFormat(movie.views) }} 回視聴</span></span>
                    </div>
                </div>
                <div class="right-content">
                    <div class="share-content"><a v-bind:href="'http://twitter.com/intent/tweet?text=' + encodeURIComponent(movie.name + '\n' + locationUrl + '\n' + snsTags + '\n@BLIVE77191685 にて')" target="_blank"><img src="/images/btn-share-twitter.png"></a></div>
                </div>
            </div>
        </div><!--// .main-area -->

        <ul class="tabs">
            <li v-on:click="tabChange(1)" v-bind:class="{'active': activeTab === 1}">動画情報</li>
            <li v-on:click="tabChange(2)" v-bind:class="{'active': activeTab === 2}">コメント</li>
        </ul>

        <div class="video-info" v-bind:class="{'active': activeTab === 1}">
            <div class="date">{{ moment(movie.created_at, 'YYYY/MM/DD') }}</div>
            <div class="movie-title">{{ movie.name }}</div>
            <div class="headline">
                <a v-if="isLoggedIn===false" class="good js-modal-open" data-target="modal01" href="#"><i class="far fa-heart"></i></a>
                <a v-else-if="isGood" class="good" v-on:click.prevent="goodCancel" href="#"><i class="fas fa-heart"></i></a>
                <a v-else class="good" v-on:click.prevent="good" href="#"><i class="far fa-heart"></i></a>
                <div class="good-count">{{ goodCount }}</div>
            </div>
            <div>
                <div class="movie-wrapper">
                    <h2 class="main-title">関連動画</h2>
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
                    <div class="vue-infinite-loading">
                        <infinite-loading @infinite="infiniteHandler"></infinite-loading>
                    </div>
                </div><!--// .movie-wrapper -->
            </div>
        </div>

        <div class="message-wrapper" v-bind:class="{'active': activeTab === 2}">
            <div class="message-area">
                <div class="title-area"><div class="title">コメント</div></div>
                <div class="chat-area">
                    <div v-if="showingUserInfo" class="user-detail">
                        <a v-on:click.prevent="closeUserInfo" href="#" class="close"><img src="/images/btn-close.png"></a>
                        <a class="user-profile" v-bind:href="'/user/' + selectedUser.id" v-bind:style="{ backgroundImage: 'url(' + selectedUser.image_path + ')' }"></a>
                        <span class="user-name"><a v-bind:href="'/user/' + selectedUser.id">{{ selectedUser.name }}</a></span>
                    </div>
                    <transition-group tag="div" class="chat-list" name="list" id="chat-list">
                        <div v-for="(message, index) in messages" :key="message.id">
                            <div v-bind:class="'user-message user-rank rank' + message.user.user_data.rank">
                                <a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}</a><span class="message">{{ message.content }}</span>
                                <button class="delete" v-if="canDelete(message)" v-on:click="showMessageModal(message)"><i class="fas fa-ellipsis-v"></i></button>
                                <div class="delete-modal" v-if="message.showingMessageModal">
                                    <a v-on:click.prevent="closeMessageModal(message)" href="#" class="close"><img src="/images/btn-close.png"></a>
                                    <a v-on:click.prevent="deleteMessage(message.id)" href="#" class="delete-btn">削除</a>
                                </div>
                            </div>
                        </div>
                    </transition-group>
                    <form class="send-chat" @submit.prevent="send">
                        <div class="user-info" v-if="isLoggedIn">
                            <span class="user-profile" v-bind:style="{ backgroundImage: 'url(' + this.user.image_path + ')' }"></span>
                            <div class="user-name">{{ this.user.name }}</div>
                        </div>
                        <div class="send-footer no-gift">
                            <div class="send-box">
                                <input v-if="isLoggedIn" type="text" placeholder="メッセージを入力" class="send-message" v-model="messageData.content">
                                <input v-else type="text" placeholder="メッセージを入力" class="send-message js-modal-open" data-target="modal01">
                                <input type="image" src="/images/btn-message-send.png" class="send-btn">
                            </div>
                        </div>
                    </form>
                </div><!--// .chat-area -->
            </div><!--// .message-area -->
        </div><!--// .message-wrapper -->
    </div>
</template>

<script>
    import InfiniteLoading from "v3-infinite-loading";
    import "v3-infinite-loading/lib/style.css";
    import moment from 'moment';

    export default {
        components: {
            'infinite-loading': InfiniteLoading,
        },
        props: {
            movie: Object,
            user: Object
        },
        computed: {
            snsTags: function () {
                let result = '#BLIVEショート #ゲーム動画';
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
                activeTab: 1,
                canSend: true, // 連投を防ぐ
                goodCount: 0,
                isGood: false,
                isLoggedIn: Object.keys(this.user).length > 0,
                locationUrl: location.href,
                messageData: {
                    movie_id: '',
                    content: ''
                },
                messages: [],
                movies: [],
                page: 1,
                selectedUser: {
                    id: '',
                    name: '',
                    image_path: ''
                },
                showingUserInfo: false,
            }
        },
        mounted () {
            this.getMovieGoods();
            this.messageData.movie_id = this.movie.id;
            this.connectChannel();
            this.receiveMessage();
        },
        methods: {
            canDelete(message) {
                let result = false;
                if (message.user_id == this.user.id) {
                    result = true;
                }
                if (this.movie.user_id == this.user.id) {
                    result = true;
                }
                return result;
            },
            connectChannel() {
                Echo.channel('movie-message.received.'+this.movie.id).listen('MovieMessageReceived', e => {
                    this.receiveMessage();
                })
            },
            closeMessageModal(message) {
                message.showingMessageModal = false;
            },
            closeUserInfo() {
                this.showingUserInfo = false;
            },
            deleteMessage(messageId) {
                const url = '/api/movie-message-delete';
                const params = {
                    message_id: messageId
                };
                axios.post(url, params)
                    .then((response) => {
                        this.receiveMessage();
                    });
            },
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
            infiniteHandler($state) {
                if (this.movie.game == null) {
                    $state.complete();
                    return;
                }
                axios.get('/api/search-movie', {
                    params: {
                        page: this.page,
                        per_page: 1,
                        q: this.movie.game.name
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
            moment(date, format) {
                return moment(date).format(format);
            },
            onPlay() {
                const url = '/api/movie/play/';
                const params = {
                    movie_id: this.movie.id
                };
                axios.post(url, params)
                    .then((response) => {
                        // console.log(response.data);
                    });
            },
            receiveMessage() {
                const url = '/api/movie-message';
                const params = { params: {
                    movie_id: this.movie.id,
                    api_token: this.user.api_token
                }};
                axios.get(url, params)
                    .then((response) => {
                        this.messages = response.data;
                    });
            },
            send() {
                if (this.messageData.content.length == 0) {
                    return false;
                }
                if (this.canSend === true) {
                    this.canSend = false;
                    const url = '/api/movie-message';
                    const params = { data: this.messageData };
                    axios.post(url, params)
                        .then((response) => {
                            // 成功したらメッセージをクリア
                            this.messageData.content = '';
                            this.canSend = true;
                        });
                }
            },
            showUserInfo(user) {
                this.selectedUser.id = user.id;
                this.selectedUser.name = user.name;
                this.selectedUser.image_path = user.image_path;
                this.showingUserInfo = true;
            },
            showMessageModal(message) {
                message.showingMessageModal = true;
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

