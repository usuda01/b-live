<template>
    <div class="group-detail">
        <div class="main-content">
            <div class="group-info">
                <div class="group-profile" v-bind:style="{ backgroundImage: 'url(' + group.user_image_path + ')' }"></div>
                <div class="group-name">{{ group.name }}</div>
            </div>
            <div class="game-title">{{ group.game_title }}</div>
            <div class="user-name">作成者：<a v-bind:href="'/user/' + group.user.id">{{ group.user.name }}</a></div>
            <div class="group-member">クラン人数：{{ group.member_number }}人</div>
            <div class="description" v-html="description"></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            group: Object
        },
        data () {
            return {
                description: this.group.description
            }
        },
        methods: {
            replaceLink() {
                // アカウント名(スクリーンネーム)をリンクに置き換える
                let regex = /(^|[^@\w])@(\w{1,15})\b/g;
                let replace = '$1<a href="http://Twitter.com/$2" target="_blank">@$2</a>';
                this.description = this.group.description.replace(regex, replace);
            },
        },
        mounted () {
            this.replaceLink();
        }
    }
</script>
