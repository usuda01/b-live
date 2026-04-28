<p>{{ $data['recipientName'] }}さん</p>
<p>もうすぐ{{ $data['streamerName'] }}さんの配信が始まります。</p>
<p><strong>{{ $data['title'] }}</strong></p>
<p>開始予定: {{ $data['scheduledAt'] }}</p>
<p><a href="{{ url('/schedule/' . $data['scheduleId']) }}"><img src="{{ url($data['imageUrl']) }}" width="222px"></a></p>
<p><a href="{{ url('/schedule/' . $data['scheduleId']) }}">予定を見る</a></p>
<br>
<div>このリマインドは <a href="{{ url('/schedule/' . $data['scheduleId']) }}">予定ページ</a> から解除できます。</div>
