<template>
    <div class="followers">
        <div v-for="follower in followers" :key="follower.id" class="follower-box">
            <a class="user-profile" v-bind:href="'/user/' + follower.follower_id" v-bind:style="{ backgroundImage: 'url(' + follower.user_image_path + ')' }"></a>
            <div class="user-right">
                <div class="user-name"><a v-bind:href="'/user/' + follower.follower_id">{{ follower.follower_user.name }}</a></div>
                <a v-if="follower.is_follow === true" v-on:click.prevent="followCancel(follower)" class="follow on" href="#"><span>フォロー</span></a>
                <a v-else-if="follower.is_follow === false" class="follow" v-on:click.prevent="follow(follower)" href="#"><span>フォロー</span></a>
            </div>
        </div>
        <infinite-loading @infinite="infiniteHandler"></infinite-loading>
    </div>
</template>

<script>
    import InfiniteLoading from 'vue-infinite-loading';

    export default {
        props: {
            //followers: Array,
        },
        data () {
            return {
                page: 1,
                followers: []
                //followersData: this.followers
            }
        },
        mounted () {
        },
        methods: {
            follow(follower) {
                const url = '/api/followers/follow';
                const params = {
                    follow_id: follower.follower_id
                };
                follower.is_follow = true;
                axios.post(url, params)
                    .then((response) => {
                    });
            },
            followCancel(follower) {
                const url = '/api/followers/follow-cancel';
                const params = {
                    follow_id: follower.follower_id
                };
                follower.is_follow = false;
                axios.post(url, params)
                    .then((response) => {
                    });
            },
            infiniteHandler($state) {
                axios.get('/api/followers/followers', {
                    params: {
                        page: this.page,
                        per_page: 1,
                    },
                }).then(({ data }) => {
                    // そのままだと読み込み時にカクつくので1500毎に読み込む
                    setTimeout(() => {
                        if (data.data.length == 0 && this.page == 1) {
                            // 1件もデータがない場合
                        }
                        if (data.data.length) {
                            this.page += 1
                            this.followers.push(...data.data)
                            //console.log(this.followers);
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
