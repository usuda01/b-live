<p>{{ $data['followerName'] }}さん</p>
<p>{{ $data['userName'] }}さんの配信が始まりました！</p>
<p><a href="{{ url('/room/' . $data['roomId']) }}">{{ $data['roomName'] }}</a></p>
<p><a href="{{ url('/room/' . $data['roomId']) }}"><img src="{{ url($data['imageUrl']) }}" width="222px"></a></p>
<br>
<div>配信通知メールは、ログイン後<a href="{{ url('/setting/notice') }}">こちらのページより解除できます。</a></div>
