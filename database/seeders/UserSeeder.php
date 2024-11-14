<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'ゲスト',
                'email' => 'gtSYULvc1FIXMO4@pseudo.twitter.com',
                'password' => '1',
                'profile' => null,
                'twitter_url' => 'https://twitter.com/BLIVE77191685',
                'api_token' => null,
                'twitter_id' => null,
                'apple_id' => null,
                'status' => 2,
                'image' => 'mdhYzvjTCyq6qPgzRdcgXxMwMmcN37uaQZi95TSh.png'
            ],
            [
                'id' => 2,
                'name' => '【公式】B-LIVE',
                'email' => 'BLIVE77191685@pseudo.twitter.com',
                'password' => '1',
                'profile' => 'とにかく赤字にならなければいい。',
                'twitter_url' => null,
                'api_token' => null,
                'twitter_id' => '1247529279342039042',
                'apple_id' => null,
                'status' => 2,
                'image' => 'iq17OfCyvQAjuINKgnwiPNUAG0Eyjq5pYN4Hyps9.jpeg'
            ],
            [
                'id' => 3,
                'name' => 'うっすー',
                'email' => 'sumaburaness12@gmail.com',
                'password' => '1',
                'profile' => "※このサイトの管理人です。\nスマブラ配信してますー",
                'twitter_url' => 'https://twitter.com/sumaburaness12',
                'api_token' => '2nKX6Jy9UdqhUcw8ME9rE840IErPqCZaBndA20orkR5HDS6ILBSuglBL9yjkwiReOMZUs0EScaCMuoD0',
                'twitter_id' => '1143331834702381056',
                'apple_id' => null,
                'status' => 2,
                'image' => 'IVPSIwr5K27CLFHlNhqs0dnALeVQFMXsMcdJvspX.png'
            ],
            [
                'id' => 4,
                'name' => 'イヌシファー　〈ゆるゆる西洋妖怪系Vtuber〉',
                'email' => 'dog_cipher@pseudo.twitter.com',
                'password' => '1',
                'profile' => "いぬのようなナニカ、あなたの後ろに転がるワンワン、ネットに住みついた“なんだかよくわからないもの”。バ美肉系新人Vtuber、イヌシファーと申します。",
                'twitter_url' => 'https://twitter.com/dog_cipher',
                'api_token' => 'i0Xr7tTnat9Im7TjgW6hJwzhyia96hWuRHaqdbMFcWuE5jvjh0tOoKCU7MlA7DGr2AiBtt38CBPjlaE4',
                'twitter_id' => '1175326695454195712',
                'apple_id' => null,
                'status' => 2,
                'image' => 'jjuadM3aMBjcf3qROmDTr3uH5Dne7fHhv6sVT2sb.png'
            ],
            [
                'id' => 5,
                'name' => 'ちゃんリト＠GCL',
                'email' => 'romi.21085@outlook.jp',
                'password' => '1',
                'profile' => "APEXや大乱闘、さらに音ゲーやモンハンなどいろいろなゲームをやっています。\nGCLはいつも遊んでるグループの名前です。\nよろしく！気軽にコメントしてね。",
                'twitter_url' => null,
                'api_token' => 'zIKJOdZIk0xvDPF6hDEzeZQYEg3qknsOXtOAtCvv4PiiS3vHSRxDSqDV0ThMIbtsP5lpVBaWATcU6tt4',
                'twitter_id' => '901397056262086656',
                'apple_id' => null,
                'status' => 2,
                'image' => 'RgqCW7xPUJKS8krUJxW6iOK1XTYZ7Y1FNi4bPux4.jpeg'
            ],
            [
                'id' => 6,
                'name' => 'エリー',
                'email' => 'EllieOrz@pseudo.twitter.com',
                'password' => '1',
                'profile' => null,
                'twitter_url' => 'https://twitter.com/EllieOrz',
                'api_token' => 'HnfgNIeVXOM1Psx5wpvCyvQ42pSqRB7eG6jazl6GLw0WuAgk2ROWY7t0pwTy56tAsWjUSF2wcqE2ymTb',
                'twitter_id' => '1234501705930133504',
                'apple_id' => null,
                'status' => 2,
                'image' => 'pu7UrNfArmjW9aS0JCRkSqX04Var2QM2c2kdd2FS.jpg'
            ],
            [
                'id' => 7,
                'name' => 'いずさん。',
                'email' => 'izuna.kuwai@gmail.com',
                'password' => '1',
                'profile' => "もう少し上に書いて主張してもいいかなと思ったので書きますね。\n\nB-LIVEさんのトップページ上のほうで流れている動画を作らせて頂いた者でございます！\n\n 不定期に配信してる人だよ。\n\n基本深夜に。\n\n基本的にFPSしかしないよ。\n\nたぶん。\n\nそう、きっと。\n\n\n\nサイトを作成。\n→https://izusanwhatever.myportfolio.com/",
                'twitter_url' => 'https://twitter.com/izu_gamer',
                'api_token' => 'Wx3cdfsVhXbp5LyLf2jOjXRYtolhL1B27DEPWVFdLFBKC6kwxZC0VDjUl55Q6sQlM3z5Ew84plZsK5mR',
                'twitter_id' => '979402618144407552',
                'apple_id' => null,
                'status' => 2,
                'image' => 'QoeUliDUK2N3xQdR1ZlsqDNDKuPakcyCAQNTz4s2.png'
            ],
            [
                'id' => 8,
                'name' => 'こんぶの実況ちゃんねる',
                'email' => 'ruichika@icloud.com',
                'password' => '1',
                'profile' => "YouTubeで主に配信してます！\nこんぶの実況ちゃんねるです😘",
                'twitter_url' => null,
                'api_token' => 'S7HPJh8nw4ANoFDhPV3w8VAFR6sN0IdcHV9UB1m4Rw23TNVOo0pTjCnvRFzYwFpS2XBNdYYwnJT12VPK',
                'twitter_id' => null,
                'apple_id' => '001432.b3fa3b01605e48f59bf3e2aa4be900e6.0228',
                'status' => 2,
                'image' => 'WVO0h8jGEaSsmuhl3CCVppRq9BIqN67sAUz46Aej.jpg'
            ],
            [
                'id' => 9,
                'name' => '世界一美しい日本一汚い男',
                'email' => 'rcb78vyxbg@privaterelay.appleid.com',
                'password' => '1',
                'profile' => "ゲーム誘ってね",
                'twitter_url' => null,
                'api_token' => 'EcComFp8rtCYFsqcM0hlNxF8Elm7sMXzDiVatgBApiT8ucJCizZIue2k5lRtizZbFRDttcN5n3yENoGi',
                'twitter_id' => null,
                'apple_id' => '000101.837d3dd7d9b049b799d3897ab51242bd.1227',
                'status' => 2,
                'image' => 'ND3DuNs22XAsJpY2Jvk5fG8EInVXva5L0Vy6RW7W.jpg'
            ],
            [
                'id' => 10,
                'name' => 'みちゃそ',
                'email' => 'michaso0606@pseudo.twitter.com',
                'password' => '1',
                'profile' => null,
                'twitter_url' => 'https://twitter.com/michaso0606',
                'api_token' => 'xAvcNepp1oiXDhyYFxqrkqRR3W9diPcWZNTQZtJL4shB0nFQEJQ5coxNRUCA0H7UlK2YSYe3ZUip0Uoe',
                'twitter_id' => '876635324448874496',
                'apple_id' => null,
                'status' => 2,
                'image' => 'PRUPVLnNxFwXo4eNTyNWfgNTM46hH6DErVcpqsom.png'
            ],
            [
                'id' => 11,
                'name' => 'Harpy(はーぴぃ)',
                'email' => 'harpyy@outlook.jp',
                'password' => '1',
                'profile' => "こんにちは!はーぴぃと申します！B-LIVERやってます。基本ゲーム配信してます。やってほしいゲームがあったらコメントしてください！できるだけ希望に答えます。よろしく！\n[Twitter]\n@Harpy_B_LIVE\n[YouTube]\nhttps://www.youtube.com/channel/UCIV8BSseMdmURTks2wBHSBw",
                'twitter_url' => 'https://twitter.com/Harpy_B_LIVE',
                'api_token' => 'Wdvf9pUYdA3IdAty3io1SLkYUXAAXCjKa6sckHxJhRTcqg3FLdPqz035LpmYHE45oRNC4Cmm2nN2jO5F',
                'twitter_id' => '1144432903364857856',
                'apple_id' => null,
                'status' => 2,
                'image' => 'XoJ7BGgomAkerE70ygdBvypeyVW02WP8xNC3TBi4.jpeg'
            ],
            [
                'id' => 12,
                'name' => 'B-LIVE 中の人',
                'email' => 'hiroshi0104@gmail.com',
                'password' => '1',
                'profile' => "B-LIVEの管理人です。\nデザイン〜開発まで全てやってます。\n不具合、ご要望などあれば\nhttps://mobile.twitter.com/birthdadan\nまでお願いします",
                'twitter_url' => 'https://twitter.com/birthdadan',
                'api_token' => 'RMAz0SmET87ONVM6QH3MnH1xDwF57W9aUcKsbRCS1mkOtxFneji5lhsw7HFC0WS5OIiUMLZszM5zjcF9',
                'twitter_id' => '134083492',
                'apple_id' => null,
                'status' => 2,
                'image' => 'ZnxG4sKW8LaiivLplIx2nI5UEl96TVf9dNjkNWhV.jpeg'
            ],
            [
                'id' => 13,
                'name' => 'システム',
                'email' => 'runyanya@boxfi.uk',
                'password' => '1',
                'profile' => null,
                'twitter_url' => null,
                'api_token' => '5IjFlDYYLjaPYutLpgmGAakhYIypgkGtZm9BGmoYUwbvJLJ8XXLSuyIYgNKjQZ747Ks64Ge5sP7wc6Ed',
                'twitter_id' => '1856978140507377666',
                'apple_id' => null,
                'status' => 2,
                'image' => 'EoeRWAi5i1NV0ZhRHObOtNOpgwBDH5WJfc0XZWuJ.png'
            ],
        ]);
    }
}
