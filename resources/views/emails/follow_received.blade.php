<p>{{ $data['recipientName'] }}さん</p>
<p>{{ $data['followerName'] }}さんにフォローされました。</p>
<p><a href="{{ url('/user/' . $data['followerId']) }}">{{ $data['followerName'] }}さんのプロフィールを見る</a></p>
<br>
<div>フォロー通知メールは、ログイン後<a href="{{ url('/setting/notice') }}">こちらのページより解除できます。</a></div>
