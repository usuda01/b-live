<template>
    <div>
        <div class="follow-wrapper">
            <a v-if="isFollow" v-on:click.prevent="followCancel" class="follow on" href="#"><span>フォロー　</span><span>{{ followerCount }}</span></a>
            <a v-else class="follow" v-on:click.prevent="follow" href="#"><span>フォロー　</span><span>{{ followerCount }}</span></a>
        </div>
        <ul class="tabs">
            <li v-on:click="tabChange(1)" v-bind:class="{'active': activeTab === 1}">プロフィール</li>
            <li v-on:click="tabChange(2)" v-bind:class="{'active': activeTab === 2}">動画</li>
            <li v-on:click="tabChange(3)" v-bind:class="{'active': activeTab === 3}">サポーター</li>
        </ul>
        <div>
        <div class="profile" v-if="activeTab === 1" v-html="profile"></div>
        <div class="room-wrapper" v-else-if="activeTab === 2">
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
            <infinite-loading @infinite="infiniteHandler"></infinite-loading>
        </div>
        <div class="supporter" v-else-if="activeTab === 3">
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
        </div>
    </div>
</template>

<script>
    import InfiniteLoading from 'vue-infinite-loading';

    export default {
        props: {
            isWebView: Boolean,
            liveRooms: Array,
            targetUser: Object,
            supporters: Array,
            user: Object
        },
        data () {
            return {
                page: 1,
                profile: '',
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
                // アカウント名(スクリーンネーム)をリンクに置き換える
                let regex = /(^|[^@\w])@(\w{1,15})\b/g;
                let replace = '$1<a href="http://Twitter.com/$2">@$2</a>';
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
            infiniteHandler($state) {
                axios.get('/api/user/', {
                    params: {
                        page: this.page,
                        per_page: 1,
                        target_user: this.targetUser.id
                    },
                }).then(({ data }) => {
                    // そのままだと読み込み時にカクつくので1500毎に読み込む
                    setTimeout(() => {
                        if (data.data.length == 0 && this.page == 1) {
                            // 1件もデータがない場合
                            this.hasRoomData = false;
                        }
                        if (data.data.length) {
                            this.page += 1
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
            }
        }
    }
</script>

