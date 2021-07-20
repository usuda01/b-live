<div class="sidebar">
    <ul class="menu-list">
        <li class="menu01 @if (Request::is('setting/stream')) active @endif"><a href="/setting/stream"><span>配信する</span></a></li>
        <li class="menu02 @if (Request::is('setting/archive')) active @endif"><a href="/setting/archive"><span>過去の配信</span></a></li>
    </ul>
</div>
