<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('games')->insert([
            ['id' => 1, 'name' => 'ポケモンユナイト', 'sns_tags' => 'ポケモンユナイト', 'sales_agency' => '任天堂'],
            ['id' => 2, 'name' => 'Apex Legends', 'sns_tags' => 'Apex,ApexLegends', 'sales_agency' => 'ELECTRONIC ARTS'],
            ['id' => 3, 'name' => 'ウマ娘 プリティーダービー', 'sns_tags' => 'ウマ娘,ウマ娘プリティーダービー', 'sales_agency' => 'Cygames'],
            ['id' => 4, 'name' => 'Warframe', 'sns_tags' => 'Warframe', 'sales_agency' => 'Digital Extremes'],
            ['id' => 5, 'name' => '大乱闘スマッシュブラザーズ SPECIAL', 'sns_tags' => 'スマブラSP,SmashBrosSP', 'sales_agency' => '任天堂'],
            ['id' => 6, 'name' => 'RED DEAD REDEMPTION II', 'sns_tags' => 'RedDeadRedemption2,RDR2', 'sales_agency' => 'ロックスター・ゲームス'],
            ['id' => 7, 'name' => 'Battlefield V', 'sns_tags' => 'BattlefieldV,BFV,BF5', 'sales_agency' => 'ELECTRONIC ARTS'],
            ['id' => 8, 'name' => 'Final Fantasy VII Remake', 'sns_tags' => 'FINALFANTASYVIIREMAKE,FF7R', 'sales_agency' => 'スクウェア・エニックス'],
            ['id' => 9, 'name' => 'MINECRAFT', 'sns_tags' => 'Minecraft,マイクラ', 'sales_agency' => 'Mojang Studios'],
            ['id' => 10, 'name' => '雀魂', 'sns_tags' => '雀魂,じゃんたま', 'sales_agency' => 'Cat Food Studio'],
            ['id' => 11, 'name' => 'VALORANT', 'sns_tags' => 'VALORANT', 'sales_agency' => 'ライアットゲームズ'],
            ['id' => 12, 'name' => 'Call of Duty：Warzone', 'sns_tags' => 'CoD,codwarzone ,CallOfDuty,Warzone', 'sales_agency' => 'アクティビジョン'],
            ['id' => 13, 'name' => 'Fortnite', 'sns_tags' => 'Fortnite,フォートナイト', 'sales_agency' => 'Epic Games'],
            ['id' => 14, 'name' => 'スプラトゥーン2', 'sns_tags' => 'スプラトゥーン2,スプラトゥーン', 'sales_agency' => '任天堂'],
            ['id' => 15, 'name' => 'Dead by Daylight', 'sns_tags' => 'DBD,DeadbyDaylight,デッドバイデイライト', 'sales_agency' => 'Behaviour Digital Inc.'],
            ['id' => 16, 'name' => 'Grand Theft Auto V', 'sns_tags' => 'GTA5', 'sales_agency' => 'ロックスター・ゲームス'],
            ['id' => 17, 'name' => 'Fall Guys: Ultimate Knockout', 'sns_tags' => 'FallGuys', 'sales_agency' => '任天堂'],
            ['id' => 18, 'name' => 'スーパーマリオ ３Ｄワールド ＋ フューリーワールド', 'sns_tags' => 'スーパーマリオ3Dワールド,マリオ3Dワールド', 'sales_agency' => '任天堂'],
            ['id' => 19, 'name' => 'Tom Clancy`s Rainbow Six Siege', 'sns_tags' => 'レインボーシックスシージ,シージ,RainbowSix,RainbowSixSiege', 'sales_agency' => 'ユービーアイソフト'],
            ['id' => 20, 'name' => 'モンスターハンターライズ', 'sns_tags' => 'モンハンライズ', 'sales_agency' => '任天堂'],
        ]);
    }
}
