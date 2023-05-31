<template>
    <div class="room-stream" :class="{'webview': isApp === true, 'rotate': isRotate === true, 'controller-active': isControllerClassActive === true}">
        <input type="hidden" id="room_id" name="room_id" :value="room.id">
        <div class="main-area">
            <div class="bar">
                <a v-bind:href="'/user/' + room.user.id" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + room.user.image_path + ')' }"></a>
                <a v-bind:href="'/user/' + room.user.id" class="user-name">{{ room.user.name }}</a>
                <span v-if="room.user.user_data.rank==2" class="user-rank rank2">推し</span>
                <span v-else-if="room.user.user_data.rank==5" class="user-rank rank5">公認配信者</span>
                <span class="tag" v-if="room.status == 1">LIVE</span>
            </div>

            <template v-if="isApp === false">
                <div v-if="room.status === 2" class="expired"><img v-bind:src="room.image_path"></div>
                <div v-if="room.status === 1" class="video-container" v-on:click="toggleControllerClass">
                    <video id="main-video" class="main-video" muted autoplay playsinline></video>
                    <div class="controls">
                        <button v-on:click="toggleMute"><i v-if="isMute === true" class="fas fa-volume-mute"></i><i v-if="isMute === false" class="fas fa-volume-up"></i></button>
                        <button v-on:click="toggleRotate"><i class="fas fa-sync-alt"></i></button>
                        <button v-on:click="togglePiP"><i class="fas fa-compress-alt"></i></button>
                        <button v-on:click="toggleFullScreen"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
            </template>

            <div class="video-footer">
                <div class="left-content">
                    <div class="view-content">
                        <img src="/images/icon-views.png" class="icon-views">
                        <span class="view-count-wrapper"><span class="view-count" id="view-count">0</span><span>人が視聴中</span></span>
                    </div>
                    <div v-if="room.status === 1" class="video-time">{{ videoTime }}</div>
                    <div v-else-if="room.status === 2" class="video-time">{{ videoTime }}</div>
                </div>
                <div class="right-content">
                    <template v-if="isApp === false">
                        <div class="share-content"><a v-bind:href="'http://twitter.com/intent/tweet?text=' + encodeURIComponent(room.name + '\n' + locationUrl + '\n' + snsTags + '\n@BLIVE77191685 にて')" target="_blank"><img src="/images/btn-share-twitter.png"></a></div>
                    </template>
                    <div class="flag-content">
                        <template v-if="isApp === true">
                            <a v-if="isLoggedIn" href="#" class="js-modal-open" data-target="modal03"><img src="/images/btn-flag.png"></a>
                            <a v-else href="jsalert://"><img src="/images/btn-flag.png"></a>
                        </template>
                        <template v-else-if="isApp === false">
                            <a href="#" class="js-modal-open" data-target="modal03"><img src="/images/btn-flag.png"></a>
                        </template>
                    </div>
                </div>
            </div>
        </div><!--// .main-area -->

        <ul class="tabs">
            <li v-on:click="tabChange(1)" v-bind:class="{'active': activeTab === 1, 'tab-movie': true}">動画情報</li>
            <li v-on:click="tabChange(2)" v-bind:class="{'active': activeTab === 2}">チャット</li>
            <li v-on:click="tabChange(3)" v-bind:class="{'active': activeTab === 3}">サポーター</li>
            <li v-on:click="tabChange(4)" v-bind:class="{'active': activeTab === 4}">リスナー</li>
        </ul>

        <div class="video-info" v-bind:class="{'active': activeTab === 1}">
            <div class="from-now"><span class="icon"><img src="/images/icon-time.png"></span><span class="date">{{ moment(room.created_at, 'MM/DD HH:mm') }}</span></div>
            <div class="headline">
                <div class="room-name">{{ room.name }}</div>
                <a v-if="isFollow" v-on:click.prevent="followCancel" class="follow on" href="#"><span>フォロー　</span><span>{{ followerCount }}</span></a>
                <a v-else class="follow" v-on:click.prevent="follow" href="#"><span>フォロー　</span><span>{{ followerCount }}</span></a>
            </div>
            <div v-if="this.room.status === 2" class="expired">
                ※このライブは終了しました。
            </div>
            <div class="description" v-html="roomDescription"></div>
            <div v-if="this.room.user.twitter_url" class="twitter-content">
                <a class="twitter-timeline" data-width="100%" data-height="400" data-theme="dark" v-bind:href="room.user.twitter_url + '?ref_src=twsrc%5Etfw'">Tweets by b-live</a>
            </div>
        </div>

        <div class="message-wrapper" v-bind:class="{'active': activeTab === 2}">
            <div class="message-area">
                <div class="title-area">
                    <template v-if="isApp === true">
                        <div class="title">チャット</div>
                    </template>
                    <template v-else-if="isApp === false">
                        <div class="title">
                            <a class="mic" v-on:click.prevent="toggleSpeak"><img v-if="isSpeak === false" src="/images/btn-mic-off.png"><img v-if="isSpeak === true" src="/images/btn-mic-on.png"></a><span>読み上げ</span>
                        </div>
                        <button class="message-menu" v-on:click="toggleMenu"><i class="fas fa-ellipsis-v"></i></button>
                    </template>
                </div>
                <template v-if="isApp === false">
                    <div class="menu-list" v-if="showMenu === true">
                        <ul>
                            <li><a href="#" v-on:click.prevent="linkToOtherWindow('/room-message-viewer/' + room.id)"><i class="fas fa-external-link-alt"></i><span>チャットをポップアウト</span></a></li>
                        </ul>
                    </div>
                </template>
                <div class="chat-area">
                    <div v-if="showingUserInfo" class="user-detail">
                        <a v-on:click.prevent="closeUserInfo" href="#" class="close"><img src="/images/btn-close.png"></a>
                        <a class="user-profile" v-bind:href="'/user/' + selectedUser.id" v-bind:style="{ backgroundImage: 'url(' + selectedUser.image_path + ')' }"></a>
                        <span class="user-name"><a v-bind:href="'/user/' + selectedUser.id">{{ selectedUser.name }}</a></span>
                        <ul class="control">
                            <li class="block">
                                <a v-if="isBlockUser" v-on:click.prevent="unBlock(selectedUser.id)" href="#"><img src="/images/btn-block_on.png"></a>
                                <a v-else v-on:click.prevent="block(selectedUser.id)" href="#"><img src="/images/btn-block.png"></a>
                            </li>
                            <li class="flag">
                                <a v-on:click.prevent="showUserFlagModal(selectedUser)" href="#" ><img src="/images/btn-flag.png"></a>
                            </li>
                        </ul>
                    </div>
                    <transition-group tag="div" class="chat-list" name="list" id="chat-list">
                        <div v-for="message in messages" :key="message.id">
                        <div v-if="message.payment_product_id === '1'" v-bind:class="'user-message payment purchase1 user-rank rank' + message.user.user_data.rank"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}</a><span class="amount">&yen;{{ message.payment.price }}</span><span class="message">{{ message.content }}</span></div>
                        <div v-else-if="message.payment_product_id === '2'" v-bind:class="'user-message payment purchase2 user-rank rank' + message.user.user_data.rank"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}</a><span class="amount">&yen;{{ message.payment.price }}</span><span class="message">{{ message.content }}</span></div>
                        <div v-else-if="message.payment_product_id === '3'" v-bind:class="'user-message payment purchase3 user-rank rank' + message.user.user_data.rank"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}</a><span class="amount">&yen;{{ message.payment.price }}</span><span class="message">{{ message.content }}</span></div>
                        <div v-else-if="message.payment_product_id === '4'" v-bind:class="'user-message payment purchase4 user-rank rank' + message.user.user_data.rank"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}</a><span class="amount">&yen;{{ message.payment.price }}</span><span class="message">{{ message.content }}</span></div>
                        <div v-else-if="message.payment_product_id === '5'" v-bind:class="'user-message payment purchase5 user-rank rank' + message.user.user_data.rank"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}</a><span class="amount">&yen;{{ message.payment.price }}</span><span class="message">{{ message.content }}</span></div>
                        <div v-else-if="message.payment_product_id === '6'" v-bind:class="'user-message payment purchase6 user-rank rank' + message.user.user_data.rank"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}</a><span class="amount">&yen;{{ message.payment.price }}</span><span class="message">{{ message.content }}</span></div>
                        <div v-else-if="message.user.id === adminUserId" v-bind:class="'user-message admin user-rank rank' + message.user.user_data.rank"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}</a><span class="message">{{ message.content }}</span></div>
                        <div v-else v-bind:class="'user-message user-rank rank' + message.user.user_data.rank"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name"><span class="user-level">Lv{{ message.user.user_data.listener_level }}</span>{{ message.user.name }}</a><span class="message">{{ message.content }}</span></div>
                      </div>
                    </transition-group>
                    <form class="send-chat" @submit.prevent="send">
                        <input type="hidden" v-model="messageData.room_id">
                        <div class="user-info">
                            <span v-if="isLoggedIn" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + this.user.image_path + ')' }"></span>
                            <span v-else class="user-profile" style="background-image: url(/images/guest-user.png)"></span>
                            <div v-if="isLoggedIn" class="user-name">{{ this.user.name }}</div>
                            <div v-else class="user-name">ゲスト</div>
                        </div>
                        <div class="send-footer" v-bind:class="{'no-gift': canSendGift === false}">
                            <div class="send-box">
                                <input type="text" placeholder="メッセージを入力" class="send-message" v-model="messageData.content">
                                <input type="image" src="/images/btn-message-send.png" class="send-btn">
                            </div>
                            <a v-if="isLoggedIn" href="#" class="btn-gift js-modal-open" data-target="modal05"><img src="/images/btn-gift.png"></a>
                            <a v-else href="#" class="btn-gift js-modal-open" data-target="modal01"><img src="/images/btn-gift.png"></a>
                        </div>
                    </form>
                </div>
            </div>
        </div><!--// .message-wrapper -->

        <div class="supporter" v-bind:class="{'active': activeTab === 3}">
            <div v-for="supporter in supporters" :key="supporter.id">
                <div v-if="supporter.product_id === '1'" class="user-message payment purchase1"><a v-on:click.prevent="showUserInfo(supporter.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + supporter.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(supporter.user)" href="#" class="user-name">{{ supporter.user.name }}</a><span class="amount">&yen;{{ supporter.point }}</span><span class="message">{{ supporter.message.content }}</span></div>
                <div v-else-if="supporter.product_id === '2'" class="user-message payment purchase2"><a v-on:click.prevent="showUserInfo(supporter.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + supporter.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(supporter.user)" href="#" class="user-name">{{ supporter.user.name }}</a><span class="amount">&yen;{{ supporter.point }}</span><span class="message">{{ supporter.message.content }}</span></div>
                <div v-else-if="supporter.product_id === '3'" class="user-message payment purchase3"><a v-on:click.prevent="showUserInfo(supporter.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + supporter.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(supporter.user)" href="#" class="user-name">{{ supporter.user.name }}</a><span class="amount">&yen;{{ supporter.point }}</span><span class="message">{{ supporter.message.content }}</span></div>
                <div v-else-if="supporter.product_id === '4'" class="user-message payment purchase4"><a v-on:click.prevent="showUserInfo(supporter.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + supporter.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(supporter.user)" href="#" class="user-name">{{ supporter.user.name }}</a><span class="amount">&yen;{{ supporter.point }}</span><span class="message">{{ supporter.message.content }}</span></div>
                <div v-else-if="supporter.product_id === '5'" class="user-message payment purchase5"><a v-on:click.prevent="showUserInfo(supporter.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + supporter.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(supporter.user)" href="#" class="user-name">{{ supporter.user.name }}</a><span class="amount">&yen;{{ supporter.point }}</span><span class="message">{{ supporter.message.content }}</span></div>
                <div v-else-if="supporter.product_id === '6'" class="user-message payment purchase6"><a v-on:click.prevent="showUserInfo(supporter.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + supporter.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(supporter.user)" href="#" class="user-name">{{ supporter.user.name }}</a><span class="amount">&yen;{{ supporter.point }}</span><span class="message">{{ supporter.message.content }}</span></div>
            </div>
        </div><!--// .supporter -->

        <div class="listener" v-bind:class="{'active': activeTab === 4}">
            <div class="title">今月のリスナー</div>
            <div v-for="listener in listeners" :key="listener.id" class="listener-box">
                <a class="user-profile" v-bind:href="'/user/' + listener.id" v-bind:style="{ backgroundImage: 'url(' + listener.user_image_path + ')' }"></a>
                <div class="user-right">
                    <div class="user-name"><a v-bind:href="'/user/' + listener.id">{{ listener.user_name }}</a></div>
                    <div class="view-time">{{ listener.view_time }}</div>
                </div>
            </div>
        </div><!--// .listener -->

        <!-- コンテンツ通報モーダル -->
        <div id="modal03" class="modal js-modal modal03">
            <div class="modal__bg js-modal-close"></div>
            <div class="modal__content">
                <a class="js-modal-close" href="#"><img src="/images/btn-close02.png"></a>
                <div v-if="isFlag === false">運営会社にこのコンテンツを通報します。<br><br></div>
                <div v-else>運営会社にこのコンテンツを通報しました。</div>
                <a v-if="isFlag === false" v-on:click.prevent="flag" href="#" class="btn-default">通報する</a>
            </div><!--modal__inner-->
        </div><!--modal-->
        <!--// コンテンツ通報モーダル -->

        <!-- ユーザー通報モーダル -->
        <div v-bind:class="{open:showingUserFlagModal}" class="modal js-modal modal04">
            <div v-on:click.prevent="hideUserFlagModal" class="modal__bg js-modal-close"></div>
            <div class="modal__content">
                <a v-on:click.prevent="hideUserFlagModal" class="js-modal-close" href="#"><img src="/images/btn-close02.png"></a>
                <div v-if="isFlagUser === false">運営会社にこのユーザーを通報します。<br><br></div>
                <div v-else>運営会社にこのユーザーを通報しました。</div>
                <a v-if="isFlagUser === false" v-on:click.prevent="flagUser" href="#" class="btn-default">通報する</a>
            </div><!--modal__inner-->
        </div><!--modal-->
        <!--// ユーザー通報モーダル -->

        <!-- ギフトメッセージ送信モーダル -->
        <div id="modal05" class="modal js-modal modal05">
            <div class="modal__bg js-modal-close"></div>
            <div class="modal__content" v-if="isLoggedIn">
                <a class="js-modal-close" href="#"><img src="/images/btn-close02.png"></a>
                <div class="gift-wrapper">
                    <div class="title">ギフトメッセージ</div>
                    <div class="gift-error" v-if="isGiftError">{{ giftErrorMessage}}</div>
                    <div class="my-coin">
                        <div class="coin-info"><img src="/images/icon-coin.png"><span>所持コイン：{{ this.user.user_data.point }}</span></div>
                        <div class="charge" v-if="isApp === true"><a v-on:click="showChargeWindow" href="/xcode-charge">チャージする</a></div>
                        <div class="charge" v-else><a v-on:click="showChargeWindow" class="js-modal-open" data-target="modal06" href="#">チャージする</a></div>
                    </div>
                    <form class="send-gift-chat" @submit.prevent>
                        <input type="hidden" v-model="giftMessageData.room_id">
                        <input type="hidden" v-model="giftMessageData.product_id">
                        <ul class="gift-list">
                            <li v-bind:class="{'active': selectedGift===1}" class="gift1"><a href="#" v-on:click.prevent="selectGift(1)">10 コイン</a></li>
                            <li v-bind:class="{'active': selectedGift===2}" class="gift2"><a href="#" v-on:click.prevent="selectGift(2)">100 コイン</a></li>
                            <li v-bind:class="{'active': selectedGift===3}" class="gift3"><a href="#" v-on:click.prevent="selectGift(3)">200 コイン</a></li>
                            <li v-bind:class="{'active': selectedGift===4}" class="gift4"><a href="#" v-on:click.prevent="selectGift(4)">500 コイン</a></li>
                            <li v-bind:class="{'active': selectedGift===5}" class="gift5"><a href="#" v-on:click.prevent="selectGift(5)">1000 コイン</a></li>
                            <li v-bind:class="{'active': selectedGift===6}" class="gift6"><a href="#" v-on:click.prevent="selectGift(6)">2000 コイン</a></li>
                        </ul>
                        <div class="user-info">
                            <span class="user-profile" v-bind:style="{ backgroundImage: 'url(' + this.user.image_path + ')' }"></span>
                            <div class="user-name">{{ this.user.name }}</div>
                        </div>
                        <div class="send-footer">
                            <div class="send-box">
                                <input type="text" placeholder="メッセージを入力" class="send-message" v-model="giftMessageData.content">
                                <button v-on:click.prevent="sendGift" type="button" class="btn-default">送信</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!--modal__inner-->
        </div><!--modal-->
        <!--// ギフトメッセージ送信モーダル -->

        <!-- コインチャージモーダル -->
        <div id="modal06" class="modal js-modal modal06">
            <div v-on:click.prevent="hideChargeWindow" class="modal__bg js-modal-close"></div>
            <div class="modal__content" v-if="isLoggedIn">
                <a v-on:click.prevent="hideChargeWindow" class="js-modal-close" href="#"><img src="/images/btn-close02.png"></a>
                <div class="gift-wrapper">
                    <div class="title">コインチャージ</div>
                    <div class="my-coin">
                        <div class="coin-info"><img src="/images/icon-coin.png"><span>所持コイン：{{ this.user.user_data.point }}</span></div>
                    </div>
                    <ul class="charge-list">
                        <li><div class="coin">200 コイン</div><button v-on:click="showCardWindow(220)" id="purchase1" class="js-modal-open btn-purchase" data-target="modal07">&yen;220</button></li>
                        <li><div class="coin">1,000 コイン</div><button v-on:click="showCardWindow(1100)" id="purchase2" class="js-modal-open btn-purchase" data-target="modal07">&yen;1,100</button></li>
                        <li><div class="coin">2,000 コイン</div><button v-on:click="showCardWindow(2200)" id="purchase3" class="js-modal-open btn-purchase" data-target="modal07">&yen;2,200</button></li>
                    </ul>
                </div>
            </div><!--modal__inner-->
        </div><!--modal-->
        <!--// コインチャージモーダル -->

        <!-- カード入力 -->
        <div id="modal07" class="modal js-modal modal07">
            <div class="modal__bg js-modal-close"></div>
            <div class="modal__content" v-if="isLoggedIn">
                <a class="js-modal-close" href="#"><img src="/images/btn-close02.png"></a>
                <div class="card-wrapper">
                    <div class="title">カード情報</div>
                    <form id="card-form">
                        <input type="hidden" id="charge-amount" v-model="chargeAmount">
                        <input type="hidden" name="charge_email" v-bind:value="this.user.email" id="charge-email">
                        <div class="email">{{ this.user.email }}</div>
                        <div id="card-element"></div>
                        <div id="card-errors" role="alert"></div>
                        <button type="button" id="card-submit" class="btn-default card-submit">&yen;{{ this.chargeAmount }}を支払う</button>
                    </form>
                </div>
            </div><!--modal__inner-->
        </div>
        <!-- //カード入力 -->

    </div>
</template>


<script>
    import moment from 'moment';
    import Hls from 'hls.js';

    export default {
        props: {
            adminUserId: Number,
            isApp: Boolean,
            room: Object,
            listeners: Array,
            user: Object,
        },
        computed: {
            snsTags: function () {
                let result = '#BLIVE #ゲーム実況 #ゲーム配信';
                if (this.room.game) {
                    let tagsArray = this.room.game.sns_tags.split(',');
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
                activeTab: 2,
                roomDescription: '',
                hls: new Hls(),
                isMute: true, // 動画音声
                isRotate: false, // 横回転
                isControllerClassActive: false, // 動画の上に被せるコントローラーのクラス名制御用
                isLoggedIn: Object.keys(this.user).length > 0,
                isBlockUser: false,
                isFlag: false,
                isFlagUser: false,
                isSpeak: true, // チャット読み上げ
                locationUrl: location.href,
                blockUsers: [],
                isFollow: false,
                followerCount: 0,
                canSend: true, // 連投を防ぐ
                messageData: {
                    room_id: '',
                    content: ''
                },
                messages: [],
                supporters: [],
                selectedUser: {
                    id: '',
                    name: '',
                    image_path: ''
                },
                showingUserInfo: false,
                showingUserFlagModal: false,
                showMenu: false, // チャットメニュー表示
                canSendGift: true, // ギフトメッセージを送れるか
                isGiftError: false,
                giftErrorMessage: '',
                selectedGift: 0,
                giftMessageData: {
                    product_id: '',
                    room_id: '',
                    content: ''
                },
                chargeAmount: 0,
                videoTime: '',
            }
        },
        mounted () {
            let self = this;
            if (this.isApp === false) {
                if (this.room.status === 1) {
                    this.videoPlay();
                }
            }
            this.createDescriptionLink();
            this.getVideoTime();
            this.giftMessageData.room_id = this.room.id;
            this.messageData.room_id = this.room.id;
            this.connectChannel();
            this.receiveMessage();
            this.receiveSupporters();
            this.getFollowers();
            this.getBlockUsers();
            if (this.room.game) {
                if (this.room.game.sales_agency == '任天堂') {
                    this.canSendGift = false;
                }
            }

            // 視聴時間を計測する
            if (this.room.status === 1) {
                this.addViewTimeEvent();
            }
        },
        methods: {
            connectChannel() {
                Echo.channel('message.received.'+this.room.id).listen('MessageReceived', e => {
                    this.receiveMessage();
                    this.receiveSupporters();
                })
            },
            moment(date, format) {
                return moment(date).format(format);
            },
            msToTime(s) {

                // Pad to 2 or 3 digits, default is 2
                function pad(n, z) {
                  z = z || 2;
                  return ('00' + n).slice(-z);
                }

                var ms = s % 1000;
                s = (s - ms) / 1000;
                var secs = s % 60;
                s = (s - secs) / 60;
                var mins = s % 60;
                var hrs = (s - mins) / 60;

                return pad(hrs) + ':' + pad(mins) + ':' + pad(secs);
            },
            getVideoTime() {
                let self = this;
                if (this.room.status == 1) {
                    setInterval(function() {
                        var startTime = self.room.created_at;
                        startTime = Date.parse(startTime);
                        var currentTime = Date.now();
                        var diffTime = currentTime - startTime;
                        diffTime = self.msToTime(diffTime);
                        self.videoTime = diffTime;
                    }, 1000);
                } else if (this.room.status == 2) {
                    var startTime = self.room.created_at;
                    startTime = Date.parse(startTime);
                    var endTime = self.room.finished_at;
                    endTime = Date.parse(endTime);
                    var diffTime = endTime - startTime;
                    diffTime = self.msToTime(diffTime);
                    self.videoTime = diffTime;
                }
            },
            createDescriptionLink() {
                // URLをリンクに置き換える
                let regex = /((h?)(ttps?:\/\/[a-zA-Z0-9.\-_@:/~?%&;=+#',()*!]+))/g; // ']))/;
                let replace = function(all, url, h, href) {
                    return '<a href="h' + href + '" target="_blank">' + url + '</a>';
                }
                if (this.room.description) {
                    this.roomDescription = this.room.description.replace(regex, replace);
                }
            },
            getFollowers() {
                const url = '/api/followers/' + this.room.user_id;
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
                    if (this.isApp === true) {
                        location.href = 'jsalert://';
                    } else {
                        alert('ログインしてください');
                    }
                    return false;
                }
                const url = '/api/followers/follow';
                const params = {
                    follow_id: this.room.user_id
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
                    if (this.isApp === true) {
                        location.href = 'jsalert://';
                    } else {
                        alert('ログインしてください');
                    }
                    return false;
                }
                const url = '/api/followers/follow-cancel';
                const params = {
                    follow_id: this.room.user_id
                };
                axios.post(url, params)
                    .then((response) => {
                        this.isFollow = false;
                        this.followerCount --;
                    });
            },
            toggleMenu() {
                this.showMenu = !this.showMenu;
            },
            linkToOtherWindow(url) {
                window.open(url, null, 'top=100,left=100,width=350,height=400');
            },
            receiveMessage() {
                const url = '/api/message';
                const params = { params: {
                    room_id: this.room.id,
                    api_token: this.user.api_token
                }};
                axios.get(url, params)
                    .then((response) => {
                        this.messages = response.data;
                        if (response.data.length > 0) {
                            if (this.isApp == false) {
                                if (this.isSpeak === true) {
                                    this.speakMessage(response.data[0].content);
                                }
                            }
                        }
                    });
            },
            toggleSpeak() {
                this.isSpeak = !this.isSpeak;
            },
            speakMessage(text) {
                if (!"speechSynthesis" in window) {
                    console.log('Sorry. Your browser does not support speech synthesis.');
                } else {
                    console.log('Your browser supports speech synthesis.');
                    const uttr = new SpeechSynthesisUtterance();
                    uttr.volume = 0.7;
                    uttr.rate = parseFloat(1.5);
                    // iOS Safariだと日本語を認識してくれない
//                    uttr.lang = 'Google 日本語';
                    uttr.text = text;
                    speechSynthesis.speak(uttr);
                }
            },
            receiveSupporters() {
                const url = '/api/room-supporters';
                const params = { params: {
                    room_id: this.room.id,
                    api_token: this.user.api_token
                }};
                axios.get(url, params)
                    .then((response) => {
                        this.supporters = response.data;
                    });
            },
            send() {
                if (this.messageData.content.length == 0) {
                    return false;
                }
                if (this.canSend === true) {
                    this.canSend = false;
                    const url = '/api/message';
                    const params = { data: this.messageData };
                    axios.post(url, params)
                        .then((response) => {
                            // 成功したらメッセージをクリア
                            this.messageData.content = '';
                            this.canSend = true;
                        });
                }
            },
            closeUserInfo() {
                this.showingUserInfo = false;
            },
            showUserInfo(user) {
                this.selectedUser.id = user.id;
                this.selectedUser.name = user.name;
                this.selectedUser.image_path = user.image_path;
                this.showingUserInfo = true;
                this.checkBlockUser(user.id);
            },
            showUserFlagModal(user) {
                if (this.isLoggedIn == false) {
                    // 未ログイン
                    if (this.isApp === true) {
                        location.href = 'jsalert://';
                    } else {
                        alert('ログインしてください');
                    }
                    return;
                }
                this.showingUserFlagModal = true;
            },
            hideUserFlagModal() {
                this.showingUserFlagModal = false;
            },
            getBlockUsers() {
                if (this.isLoggedIn == false) {
                    return;
                }
                const url = '/api/block/get-block-users';
                const params = { params: {
                    api_token: this.user.api_token
                }};
                axios.get(url, params)
                    .then((response) => {
                        this.blockUsers = response.data;
                    });
            },
            checkBlockUser(userId) {
                for (let i = 0; i < this.blockUsers.length; i ++) {
                    if (userId == this.blockUsers[i].id) {
                        this.isBlockUser = true;
                        return;
                    }
                }
                this.isBlockUser = false;
            },
            block(userId) {
                if (this.isLoggedIn == false) {
                    // 未ログイン
                    if (this.isApp === true) {
                        location.href = 'jsalert://';
                    }
                    return;
                }
                const url = '/api/block/block';
                const params = {
                    blocked_id: userId
                };
                axios.post(url, params)
                    .then((response) => {
                        this.isBlockUser = true;
                        this.receiveMessage();
                        this.getBlockUsers();
                    });
            },
            unBlock(userId) {
                if (this.isLoggedIn == false) {
                    // 未ログイン
                    if (this.isApp === true) {
                        location.href = 'jsalert://';
                    }
                    return;
                }
                const url = '/api/block/un-block';
                const params = {
                    blocked_id: userId
                };
                axios.post(url, params)
                    .then((response) => {
                        this.isBlockUser = false;
                        this.receiveMessage();
                        this.getBlockUsers();
                    });
            },
            flag() {
                if (this.isLoggedIn == false) {
                    return;
                }
                const url = '/api/block/flag';
                const params = {
                    room_id: this.room.id
                };
                axios.post(url, params)
                    .then((response) => {
                        this.isFlag = true;
                    });
            },
            flagUser() {
                if (this.isLoggedIn == false) {
                    return;
                }
                const url = '/api/block/flag-user';
                const params = {
                    user_id: this.selectedUser.id 
                };
                axios.post(url, params)
                    .then((response) => {
                        this.isFlagUser = true;
                    });
            },
            tabChange(num) {
                this.activeTab = num
            },
            showChargeWindow() {
                // やむなくjquery使う
                $('#modal05').fadeOut();
            },
            selectGift(productId) {
                this.selectedGift = productId;
                this.giftMessageData.product_id = productId;
            },
            sendGift() {
                if (this.giftMessageData.content.length == 0) {
                    this.isGiftError = true;
                    this.giftErrorMessage = 'メッセージを入力してください';
                    return false;
                }

                if (this.selectedGift == 0) {
                    this.isGiftError = true;
                    this.giftErrorMessage = 'ギフトを選択してください';
                    return false;
                }

                if (this.selectedGift == 1) {
                    var point = 10;
                } else if (this.selectedGift == 2) {
                    var point = 100;
                } else if (this.selectedGift == 3) {
                    var point = 200;
                } else if (this.selectedGift == 4) {
                    var point = 500;
                } else if (this.selectedGift == 5) {
                    var point = 1000;
                } else if (this.selectedGift == 6) {
                    var point = 2000;
                }
                if (this.user.user_data.point < point) {
                    this.isGiftError = true;
                    this.giftErrorMessage = 'コインが不足しています';
                    return false;
                }

                if (this.canSend === true) {
                    this.canSend = false;
                    const url = '/api/message';
                    const params = { data: this.giftMessageData };
                    axios.post(url, params)
                        .then((response) => {
                            // 成功したらメッセージをクリア
                            this.giftMessageData.content = '';
                            // やむなくjquery使う
                            $('#modal05').fadeOut();
                            this.canSend = true;
                            location.reload()
                        });
                }
            },
            hideChargeWindow() {
            },
            showCardWindow(amount) {
                this.chargeAmount = amount;
                // やむなくjquery使う
                $('#modal06').fadeOut();
            },
            videoPlay() {
                const video = document.getElementById('main-video');
                const videoUrl = this.room.wowza.hls_url;
                if (Hls.isSupported()) {
                    var config = {
                        enableWorker: true,
                        maxBufferLength: 1,
                        liveBackBufferLength: 1,
                        liveSyncDurationCount: 1.5,
                        liveMaxLatencyDurationCount: 2,
                        liveDurationInfinity: true,
                        highBufferWatchdogPeriod: 1
                    };
                    this.hls = new Hls(config);
                    this.hls.loadSource(videoUrl);
                    this.hls.attachMedia(video);
                    video.play();
                } else if (video.canPlayType("application/vnd.apple.mpegurl")) {
                    video.src = videoUrl;
                    //video.addEventListener("loadedmetadata", () => {
                    //video.addEventListener("canplaythrough", () => {
                    video.addEventListener('canplay', () => {
                      video.play();
                    });
                }
            },
            toggleControllerClass() {
                this.isControllerClassActive = !this.isControllerClassActive;
            },
            toggleMute() {
                const video = document.getElementById('main-video');
                video.muted = !video.muted
                this.isMute = video.muted;
            },
            toggleRotate() {
                this.isRotate = !this.isRotate;
            },
            togglePiP() {
                const video = document.getElementById('main-video');
                if ('pictureInPictureEnabled' in document) {
                    video.requestPictureInPicture();
                } else if ('msPictureInPictureEnabled' in document) {
                    video.msRequestPictureInPicture();
                } else if ('mozPictureInPictureEnabled' in document) {
                    video.mozRequestPictureInPicture();
                } else if ('webkitPictureInPictureEnabled' in document) {
                    video.webkitRequestPictureInPicture();
                }
            },
            toggleFullScreen() {
                const video = document.getElementById('main-video');
                if (video.requestFullscreen) {
                    video.requestFullscreen();
                } else if (video.mozRequestFullScreen) {
                    /* Firefox */
                    video.mozRequestFullScreen();
                } else if (video.webkitRequestFullscreen) {
                    /* Chrome, Safari and Opera */
                    video.webkitRequestFullscreen();
                } else if (video.msRequestFullscreen) {
                    /* IE/Edge */
                    video.msRequestFullscreen();
                } else if (video.webkitEnterFullscreen) {
                    /* iOS */
                    video.webkitEnterFullscreen();
                }
            },
            addViewTimeEvent() {
                if (this.isLoggedIn == false) {
                    return;
                }
                // 自分の動画に対しては記録しない
                if (this.user.id === this.room.user_id) {
                    return;
                }
                let intervalTime = 30000; // ミリ秒
                setInterval(() => {
                    this.storeViewTime(intervalTime);
                }, intervalTime);
            },
            storeViewTime(intervalTime) {
                let duration = parseInt(intervalTime / 1000);

                // サーバーに送信する
                const url = '/api/room/store-view-time';
                const params = {
                    duration: duration,
                    viewer_user_id: this.user.id,
                    viewed_user_id: this.room.user_id
                };
                axios.post(url, params)
                    .then((response) => {
                    });
            },
        }
    }
</script>
