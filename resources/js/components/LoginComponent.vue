<template>
    <div class="login-index">
        <div class="content">
            <h2 class="logo"><a href="/"><img src="/images/logo.png"></a></h2>
            <div class="sns-login">
                <h3 class="login-title">SNSでログイン</h3>
                <ul>
                    <li><a v-if="isXcode == false" href="/auth/line"><img src="/images/btn-line.png"></a></li>
                    <li><a v-if="isXcode == false" href="/auth/twitter"><img src="/images/btn-twitter.png"></a></li>
                    <li><a v-if="isXcode == false" href="/auth/facebook"><img src="/images/btn-facebook.png"></a></li>
                    <li><a  href="/auth/apple-signin"><img src="/images/btn-apple.png"></a></li>
                </ul>
                <div class="desc">※SNSに勝手にツイートや投稿をすることはありません</div>
                <div class="agree"><a href="#" class="js-modal-open" data-target="modal02">利用規約</a>に同意の上、ご利用ください</div>
            </div>
        </div>

        <!-- 利用規約モーダル -->
        <div class="modal js-modal" id="modal02">
            <div class="modal__bg js-modal-close"></div>
            <div class="modal__content">
                <a class="js-modal-close" href="#"><img src="/images/btn-close02.png"></a>
                <div class="terms">
                    <h2>利用規約</h2>
                    <div class="terms-content">
                        <div class="section">
                            <h3>1. サービス内容</h3>
                            <div>当サービスでは、利用者がライブストリーミングを配信、または視聴することができます。不適切なコンテンツ（ヌード、ポルノグラフィ、不適切な表現など）は見つかり次第削除いたしますが、一時的に表示されてしまう可能性があります。</div>
                        </div>
                        <div class="section">
                            <h3>2. 禁止事項</h3>
                            <div class="title">利用者は、以下の行為のいずれかを行った場合、これにより運営会社又は第三者が被った一切の損害を賠償することに同意します。</div>
                            <p>・法律に違反する行為又は犯罪行為し、若しくは犯罪行為を助長に関連する行為</p>
                            <p>・過度に暴力的又は残虐な表現を含む情報</p>
                            <p>・特許権、実用新案権、意匠権、商標権、著作権、肖像権その他の他人の権利を侵害する行為</p>
                            <p>・わいせつな表現を含む情報</p>
                            <p>・児童買春・ポルノ、無修正ビデオ動画を投稿し、又はこれらのダウンロードサイト等へのリンクを掲載する行為</p>
                            <p>・公序良俗に反する行為</p>
                            <p>・コンピューター・ウイルスその他の有害なコンピュータ・プログラムを含む情報</p>
                            <p>・運営会社が不適切であると考える行為</p>
                        </div>
                        <div class="section">
                            <h3>3. 運営会社の対応</h3>
                            <p>利用者が「2. 禁止事項」に該当した場合、運営会社は、利用者に対する事前の告知なく、アカウントの削除、本サービスの利用を停止、配信コンテンツ若しくはコメント等を削除し若しくは送信防止措置をとること、その他運営会社が適当と考える対応を採ることができるものとします。</p>
                            <p>「2. 禁止事項」に該当した場合、運営会社の確認後、24時間以内に適当な措置をとります。</p>
                        </div>
                        <div class="section">
                            <h3>4. サービスの変更、停止、終了</h3>
                            <p>運営会社は、運営会社の都合により、本サービスの全部または一部について、その内容を変更、停止、若しくは終了することができます。この場合、運営会社は原則として利用者に対して、本サービスのウェブサイト上において事前に通知又は公表します。 運営会社は、提供するサービスの内容を変更又は提供を終了したことに起因して利用者が損害を被ったとしても一切責任を負いません。</p>
                        </div>
                    </div>
                </div>
            </div><!--modal__inner-->
        </div><!--modal-->
        <!--// 利用規約モーダル -->
    </div>
</template>


<script>
    export default {
        data () {
            return {
                isXcode: false
            }
        },
        mounted () {
            this.checkUserAgent();
        },
        methods: {
            checkUserAgent() {
                let userAgent = window.navigator.userAgent.toLowerCase();
                if (userAgent.indexOf('webview') != -1) {
                    // twitterログインの要望が多いため、iPhoneアプリでもログインできるようにする
//                    this.isXcode = true;
                }
            }
        }
    }
</script>

