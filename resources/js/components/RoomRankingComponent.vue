<template>
    <div class="room-ranking">
        <div class="select-month">
            <h2 v-if="targetMonth==0">今月のランキング</h2>
            <h2 v-else-if="targetMonth==1">先月のランキング</h2>
            <h2 v-else-if="targetMonth==2">先々月のランキング</h2>
            <select name="targetMonth" v-model="selectedMonth" v-on:change="changeMonth($event)">
                <option value="0">今月</option>
                <option value="1">先月</option>
                <option value="2">先々月</option>
            </select>
        </div>
        <ul class="rank-list">
            <li class="rank4" v-bind:class="{ active: targetRank==4 }"><a v-bind:href="'/room-ranking/' + targetMonth + '/4'">A</a></li>
            <li class="rank3" v-bind:class="{ active: targetRank==3 }"><a v-bind:href="'/room-ranking/' + targetMonth + '/3'">B</a></li>
            <li class="rank2" v-bind:class="{ active: targetRank==2 }"><a v-bind:href="'/room-ranking/' + targetMonth + '/2'">C</a></li>
            <li class="rank1" v-bind:class="{ active: targetRank==1 }"><a v-bind:href="'/room-ranking/' + targetMonth + '/1'">D</a></li>
        </ul>
        <div class="room-content">
            <div v-for="(room, index) in rooms" :key="room.index" class="room-box">
                <div class="room-rank">
                    <div class="rank-icon" v-if="index==0"><img src="/images/icon-rank1.png"></div>
                    <div class="rank-icon" v-if="index==1"><img src="/images/icon-rank2.png"></div>
                    <div class="rank-icon" v-if="index==2"><img src="/images/icon-rank3.png"></div>
                    <div class="rank-count">{{ index + 1 }}位</div>
                </div>
                <div class="room-image">
                    <a v-bind:href="'/room/' + room.room_id" v-bind:style="{ backgroundImage: 'url(' + room.image_path + ')' }"></a>
                    <span v-if="room.status==1" class="live">LIVE</span>
                </div>
                <div class="room-info">
                    <div class="room-name"><a v-bind:href="'/room/' + room.room_id">{{ room.name }}</a></div>
                    <div class="user-info">
                        <a v-bind:href="'/user/' + room.user_id" v-bind:style="{ backgroundImage: 'url(' + room.user_image_path + ')' }" class="user-profile"></a>
                        <a v-bind:href="'/user/' + room.user_id" class="user-name">{{ room.user_name }}</a>
                    </div>
                    <div class="max-view"><span class="number">{{ room.max_view }}</span><span class="text">view</span></div>
                </div>
            </div>
        </div>
        <div v-if="hasData==false" class="no-data">
            <span v-if="targetRank==4">A</span>
            <span v-if="targetRank==3">B</span>
            <span v-if="targetRank==2">C</span>
            <span v-if="targetRank==1">D</span>
            <span>ランクの動画はありません。</span>
        </div>
        <div class="vue-infinite-loading">
            <infinite-loading @infinite="infiniteHandler"></infinite-loading>
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
            targetMonth: Number,
            targetRank: Number,
        },
        data () {
            return {
                page: 1,
                rooms: [],
                hasData: true,
                selectedMonth: this.targetMonth
            }
        },
        methods: {
            infiniteHandler($state) {
                axios.get('/api/room-ranking/' + this.targetMonth + '/' + this.targetRank, {
                    params: {
                        page: this.page,
                        per_page: 1
                    },
                }).then(({ data }) => {
                    // そのままだと読み込み時にカクつくので1500毎に読み込む
                    setTimeout(() => {
                        if (data.data.length == 0 && this.page == 1) {
                            // 1件もデータがない場合
                            this.hasData = false;
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
            },
            changeMonth(event) {
                window.location.href = '/room-ranking/' + event.target.value + '/' + this.targetRank;
            }
        }
    }
</script>
