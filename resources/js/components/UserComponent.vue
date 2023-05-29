<template>
    <div>
        <div class="follow-wrapper">
            <a v-if="isFollow" v-on:click.prevent="followCancel" class="follow on" href="#"><span>フォロー　</span><span>{{ followerCount }}</span></a>
            <a v-else class="follow" v-on:click.prevent="follow" href="#"><span>フォロー　</span><span>{{ followerCount }}</span></a>
        </div>
        <ul class="tabs">
            <li v-on:click="tabChange(1)" v-bind:class="{'active': activeTab === 1}">プロフィール</li>
            <li v-on:click="tabChange(2)" v-bind:class="{'active': activeTab === 2}">ショート動画</li>
            <li v-on:click="tabChange(3)" v-bind:class="{'active': activeTab === 3}">配信履歴</li>
            <li v-on:click="tabChange(4)" v-bind:class="{'active': activeTab === 4}">サポーター</li>
            <li v-on:click="tabChange(5)" v-bind:class="{'active': activeTab === 5}">リスナー</li>
        </ul>

        <div>
            <div class="profile" v-if="activeTab === 1" v-html="profile"></div>
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
                <div class="vue-infinite-loading">
                    <infinite-loading :identifier="1" @infinite="tab2InfiniteHandler"></infinite-loading>
                </div>
            </div>

            <div class="room-wrapper" v-else-if="activeTab === 3">
                <h2 v-if="liveRooms.length" class="main-title">配信中</h2>
                <div class="room-content">
                    <div v-for="room in liveRooms" :key="room.id" class="room-box">
                        <div class="room-image"><a v-bind:href="'/room/' + room.id" v-bind:style="{ backgroundImage: 'url(' + room.stream_image_path + ')' }"></a></div>
                        <div class="room-info">
                            <a class="room-name" v-bind:href="'/room/' + room.id">{{ room.name }}</a>
                            <div class="user-name">{{ room.user.name }}</div>
                        </div>
                    </div>
                </div>

                <h2 v-if="hasRoomData==true" class="main-title">過去の動画</h2>
                <div class="room-content">
                    <div v-for="room in rooms" :key="room.id" class="room-box">
                        <div class="room-image"><a v-bind:href="'/room/' + room.id" v-bind:style="{ backgroundImage: 'url(' + room.image_path + ')' }"></a></div>
                        <div class="room-info">
                            <a class="room-name" v-bind:href="'/room/' + room.id">{{ room.name }}</a>
                            <div class="user-name">{{ room.user.name }}</div>
                        </div>
                    </div>
                </div>
                <div class="vue-infinite-loading">
                    <infinite-loading :identifier="2" @infinite="tab3InfiniteHandler"></infinite-loading>
                </div>
            </div>

            <div class="supporter" v-else-if="activeTab === 4">
                <div v-for="supporter in supporters" :key="supporter.id" class="supporter-box">
                    <a class="user-profile" v-bind:href="'/user/' + supporter.id" v-bind:style="{ backgroundImage: 'url(' + supporter.user_image_path + ')' }"></a>
                    <div class="user-right">
                        <div class="user-name"><a v-bind:href="'/user/' + supporter.id">{{ supporter.user_name }}</a></div>
                        <div class="coin">
                            <img src="/images/icon-coin.png">
                            <div class="price">{{ supporter.total_price }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="listener" v-else-if="activeTab === 5">
                <div class="title">今月のリスナー</div>
                <div v-for="listener in listeners" :key="listener.id" class="listener-box">
                    <a class="user-profile" v-bind:href="'/user/' + listener.id" v-bind:style="{ backgroundImage: 'url(' + listener.user_image_path + ')' }"></a>
                    <div class="user-right">
                        <div class="user-name"><a v-bind:href="'/user/' + listener.id">{{ listener.user_name }}</a></div>
                        <div class="view-time">{{ listener.view_time }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import InfiniteLoading from "v3-infinite-loading";
    import "v3-infinite-loading/lib/style.css";

    export default {
        components: {
            'infinite-loading': InfiniteLoading,
        },
        props: {
            isWebView: Boolean,
            liveRooms: Array,
            targetUser: Object,
            supporters: Array,
            listeners: Array,
            user: Object
        },
        data () {
            return {
                tab2Page: 1,
                tab3Page: 1,
                profile: '',
                movies: [],
                rooms: [],
                hasRoomData: true,
                activeTab: 1,
                isLoggedIn: Object.keys(this.user).length > 0,
                isFollow: false,
                followerCount: 0
            }
        },
        mounted () {
            this.createProfileLink();
            this.getFollowers();
        },
        methods: {
            createProfileLink() {
                // URLをリンクに置き換える
                let regex = /((h?)(ttps?:\/\/[a-zA-Z0-9.\-_@:/~?%&;=+#',()*!]+))/g; // ']))/;
                let replace = function(all, url, h, href) {
                    return '<a href="h' + href + '" target="_blank">' + url + '</a>';
                }
                if (this.targetUser.profile) {
                    this.profile = this.targetUser.profile.replace(regex, replace);
                }
            },
            getFollowers() {
                const url = '/api/followers/' + this.targetUser.id;
                const params = { params: {
                }};
                axios.get(url, params)
                    .then((response) => {
                        this.followerCount = response.data.length;

                        // 自分がフォローしているかどうかのチェック
                        for (var i = 0; i < response.data.length; i ++) {
                            if (response.data[i].follower_id == this.user.id) {
                                this.isFollow = true;
                                break;
                            }
                        }
                    });
            },
            follow() {
                if (!Object.keys(this.user).length) {
                    // 未ログイン
                    if (this.isWebView) {
                        location.href = 'jsalert://';
                    } else {
                        alert('ログインしてください');
                    }
                    return false;
                }
                const url = '/api/followers/follow';
                const params = {
                    follow_id: this.targetUser.id
                };
                axios.post(url, params)
                    .then((response) => {
                        this.isFollow = true;
                        this.followerCount ++;
                    });
            },
            followCancel() {
                if (!Object.keys(this.user).length) {
                    // 未ログイン
                    //location.href = 'jsalert://';
                    return false;
                }
                const url = '/api/followers/follow-cancel';
                const params = {
                    follow_id: this.targetUser.id
                };
                axios.post(url, params)
                    .then((response) => {
                        this.isFollow = false;
                        this.followerCount --;
                    });
            },
            tabChange(num) {
                this.activeTab = num
            },
            tab2InfiniteHandler($state) {
                axios.get('/api/search-movie', {
                    params: {
                        page: this.tab2Page,
                        per_page: 1,
                        user_id: this.targetUser.id
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
                    $state.complete()
                })
            },
            tab3InfiniteHandler($state) {
                axios.get('/api/user/', {
                    params: {
                        page: this.tab3Page,
                        per_page: 1,
                        target_user: this.targetUser.id
                    },
                }).then(({ data }) => {
                    // そのままだと読み込み時にカクつくので1500毎に読み込む
                    setTimeout(() => {
                        if (data.data.length == 0 && this.tab3Page == 1) {
                            // 1件もデータがない場合
                            this.hasRoomData = false;
                        }
                        if (data.data.length) {
                            this.tab3Page += 1
                            this.rooms.push(...data.data)
                            //console.log(this.rooms);
                            $state.loaded();
                        } else {
                            $state.complete();
                        }
                    }, 1500)
                }).catch((err) => {
                    $state.complete()
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

