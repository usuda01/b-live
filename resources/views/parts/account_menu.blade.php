<div class="account-sidebar">
    <ul class="menu-list">
        <li class="menu01 @if (Request::is('setting/profile')) active @endif"><a href="/setting/profile"><span>アカウント</span></a></li>
        <li class="menu02 @if (Request::is('setting/line')) active @endif"><a href="/setting/line"><span>LINE連携</span></a></li>
        <li class="menu03 @if (Request::is('setting/notice')) active @endif"><a href="/setting/notice"><span>通知設定</span></a></li>
        <li class="menu04 @if (Request::is('setting/coin')) active @endif"><a href="/setting/coin"><span>所持コイン</span></a></li>
        <li class="menu05 @if (Request::is('setting/group-list') || Request::is('setting/group') || Request::is('setting/group/*')) active @endif"><a href="/setting/group-list"><span>クラン掲載</span></a></li>
    </ul>
</div>
