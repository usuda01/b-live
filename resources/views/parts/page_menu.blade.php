<div class="sidebar">
    <ul class="menu-list">
        <li class="menu01 @if (Request::is('page/notice')) active @endif"><a href="/page/notice"><span>お知らせ</span></a></li>
        <li class="menu02 @if (Request::is('page/benefits')) active @endif"><a href="/page/benefits"><span>配信者特典</span></a></li>
        <li class="menu02 @if (Request::is('page/level')) active @endif"><a href="/page/level"><span>レベルについて</span></a></li>
        <li class="menu02 @if (Request::is('page/ranking')) active @endif"><a href="/page/ranking"><span>ランキングについて</span></a></li>
        <li class="menu02 @if (Request::is('page/liver')) active @endif"><a href="/page/liver"><span>配信者の方へ</span></a></li>
        <li class="menu03 @if (Request::is('page/howto')) active @endif"><a href="/page/howto"><span>配信方法</span></a></li>
        <li class="menu04 @if (Request::is('page/terms')) active @endif"><a href="/page/terms"><span>利用規約</span></a></li>
        <li class="menu05 @if (Request::is('page/company')) active @endif"><a href="/page/company"><span>運営者</span></a></li>
        <li class="menu06 @if (Request::is('contact')) active @endif"><a href="/contact"><span>お問い合わせ</span></a></li>
        <li class="menu07 "><a href="/column/column.html"><span>コラム</span></a></li>
    </ul>
</div>
