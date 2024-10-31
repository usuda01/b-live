/*
HLSのURLを変更
https://5f1ee0e19125e.streamlock.net/blive/{stream_key}/playlist.m3u8
↓
https://5f1ee0e19125e.streamlock.net/blive/ngrp:{stream_key}_all/playlist.m3u8
*/

UPDATE
    wowzas
SET
    hls_url = CONCAT(
        'https://5f1ee0e19125e.streamlock.net/blive/ngrp:',
        SUBSTRING_INDEX(
            SUBSTRING_INDEX(hls_url, '/', -2),
            '/',
            1
        ),
        '_all/playlist.m3u8'
    );