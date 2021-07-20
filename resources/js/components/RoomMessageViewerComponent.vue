<template>
    <div class="room-stream webview">
        <input type="hidden" id="room_id" name="room_id" :value="room.id">

        <div class="message-wrapper" v-bind:class="{'active': activeTab === 2}">
            <div class="message-area">
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
                        <div v-if="message.payment_product_id === '1'" class="user-message payment purchase1"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}</a><span class="amount">&yen;{{ message.payment.price }}</span><span class="message">{{ message.content }}</span></div>
                        <div v-else-if="message.payment_product_id === '2'" class="user-message payment purchase2"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}</a><span class="amount">&yen;{{ message.payment.price }}</span><span class="message">{{ message.content }}</span></div>
                        <div v-else-if="message.payment_product_id === '3'" class="user-message payment purchase3"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}</a><span class="amount">&yen;{{ message.payment.price }}</span><span class="message">{{ message.content }}</span></div>
                        <div v-else-if="message.payment_product_id === '4'" class="user-message payment purchase4"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}</a><span class="amount">&yen;{{ message.payment.price }}</span><span class="message">{{ message.content }}</span></div>
                        <div v-else-if="message.payment_product_id === '5'" class="user-message payment purchase5"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}</a><span class="amount">&yen;{{ message.payment.price }}</span><span class="message">{{ message.content }}</span></div>
                        <div v-else-if="message.payment_product_id === '6'" class="user-message payment purchase6"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}</a><span class="amount">&yen;{{ message.payment.price }}</span><span class="message">{{ message.content }}</span></div>
                        <div v-else-if="message.user.id === adminUserId" class="user-message admin"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}：</a><span class="message">{{ message.content }}</span></div>
                        <div v-else class="user-message"><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-profile" v-bind:style="{ backgroundImage: 'url(' + message.user.image_path + ')' }"></a><a v-on:click.prevent="showUserInfo(message.user)" href="#" class="user-name">{{ message.user.name }}：</a><span class="message">{{ message.content }}</span></div>
                      </div>
                    </transition-group>
                </div>
            </div>
        </div><!--// .message-wrapper -->

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

    </div>
</template>


<script>
    import moment from 'moment';

    export default {
        props: {
            adminUserId: Number,
            room: Object,
            user: Object
        },
        data () {
            return {
                activeTab: 2,
                isLoggedIn: Object.keys(this.user).length > 0,
                isBlockUser: false,
                isFlag: false,
                isFlagUser: false,
                blockUsers: [],
                messages: [],
                selectedUser: {
                    id: '',
                    name: '',
                    image_path: ''
                },
                showingUserInfo: false,
                showingUserFlagModal: false,
            }
        },
        filters: {
            moment: function (date) {
                return moment(date).format('MM/DD HH:mm');
            }
        },
        mounted () {
            this.connectChannel();
            this.receiveMessage();
            this.getBlockUsers();
        },
        methods: {
            connectChannel() {
                Echo.channel('message.received.'+this.room.id).listen('MessageReceived', e => {
                    this.receiveMessage();
                })
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
                    });
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
                    location.href = 'jsalert://';
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
                    location.href = 'jsalert://';
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
                    location.href = 'jsalert://';
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
        }
    }
</script>
