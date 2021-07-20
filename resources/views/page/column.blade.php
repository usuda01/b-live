@extends('layouts.app')

@section('content')
<div class="page-content">

@include('parts.page_menu')

<div class="main-content">

<h2>コラム</h2>

<div class="box company">

<div>
<p class="test_text">test</p>
</div>
<style type="text/css">
.test_text {color: #0f0;}
</style>
<script type="text/javascript">
jQuery(function($) {
$('.test_text').html('ここに変更したいHTML要素');
});
</script>

<!--
<ul class="list">
<li><div class="key">会社名</div><div class="value">株式会社CAROL</div></li>
<li><div class="key">住所</div><div class="value">〒171-0044<br>東京都豊島区千早２−１４−８</div></li>
<li><div class="key">代表取締役</div><div class="value">薄田 広志</div></li>
<li><div class="key">TEL</div><div class="value"><a href="tel:050-5373-5388">050-5373-5388</a></div></li>
<li><div class="key">Mail</div><div class="value">support-i@carol-i.com</div></li>
</ul>
<div class="map">
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4280.9379284695615!2d139.68962138692413!3d35.73291123883149!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x601892ab2356a291%3A0x8d19ccf5af7cda87!2z44CSMTcxLTAwNDQg5p2x5Lqs6YO96LGK5bO25Yy65Y2D5pep77yS5LiB55uu77yR77yU4oiS77yY!5e0!3m2!1sja!2sjp!4v1590242837398!5m2!1sja!2sjp" width="600" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
</div>
-->

</div>

</div>

</div>
@endsection

