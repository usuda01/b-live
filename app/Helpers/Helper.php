<?php

namespace App\Helpers;

class Helper
{

    /*
     * Time型文字列をISO 8601 形式に変換
     *
     * @param string $time
     * @return string
     */
    public static function timeToIso($time) {
        $seconds = (int)substr($time, -2); //秒
        $duration = "PT{$seconds}S";
        return $duration;
    }

    /**
     * 秒数をTime型に変換
     *
     * @param int $seconds
     * @return string
     */
    public static function secToStr($sec) {
        $hours = floor($sec / 3600); //時間
        $minutes = floor(($sec / 60) % 60); //分
        $seconds = floor($sec % 60); //秒
        if ($hours == 0) {
            $hms = '00:' . sprintf('%02d', $minutes) .':'. sprintf('%02d', $seconds);
        } else if ($minutes == 0) {
            $hms = '00:00:' . sprintf('%02d', $seconds);
        } else {
            $hms = sprintf('%02d', $hours) . ':' . sprintf('%02d', $minutes) .':'. sprintf('%02d', $seconds);
        }
        return $hms;
    }

    /**
     * 時間の差分を計算する
     *
     * @param string $from
     * @param string $to
     * @return string
     */
    public static function timeDiff($from, $to) {
        $from = strtotime($from);
        $to = strtotime($to);
        $diff = $to - $from;
        $hours = floor($diff / 3600);
        $minutes = floor(($diff / 60) % 60);
        $seconds = $diff % 60;
        if ($hours == 0) {
            $return = '00:' . sprintf('%02d', $minutes) .':'. sprintf('%02d', $seconds);
        } else if ($minutes == 0) {
            $return = '00:00:' . sprintf('%02d', $seconds);
        } else {
            $return = sprintf('%02d', $hours) . ':' . sprintf('%02d', $minutes) .':'. sprintf('%02d', $seconds);
        }
        return $return;
    }

    /**
     * 再生回数をフォーマットする
     *
     * @param int $views
     * @return string
     */
    public static function formatCount($views) {
        return $views;
    }

    // オリジナルサイズのときは $size = 0 とする
    public static function resizeImage($path = null, $size = 750) {
        $save_path = $path;
        $quality = 90; // PNG、JPEG時のクオリティー
        $src = "";

        $row = file_get_contents($path);
        $origSize = getimagesize($path);

        $src = imagecreatefromstring($row);
        $srcWidth = $origSize[0];
        $srcHeight = $origSize[1];

        if ($size == 0) {
            $dstWidth = $srcWidth;
        } else {
            $dstWidth = $size;
        }

        // 元画像の比率を計算し、高さを設定
        $proportion = $srcWidth / $srcHeight;
        $dstHeight = $dstWidth / $proportion;

        // 縦長の画像の場合は、高さをsizeに合わせ、横幅を縮小
        if ($proportion < 1) {
            $dstHeight = $dstWidth;
            $dstWidth = $dstWidth * $proportion;
        }

        // 画像の縮小
        imagecreatetruecolor($srcWidth, $srcWidth * $dstHeight / $dstWidth);
        $dest = imagecreatetruecolor($dstWidth, $dstHeight);
        imagecopyresampled($dest, $src, 0, 0, 0, 0, $dstWidth, $dstHeight, $srcWidth, $srcHeight);

        switch($origSize[2]) {
            case IMAGETYPE_GIF:
                $result = imagegif($dest, $save_path); break;
            case IMAGETYPE_JPEG:
                $result = imagejpeg($dest, $save_path, $quality); break;
            case IMAGETYPE_PNG:
                $result = imagepng($dest, $save_path, floor($quality * 0.09)); break;
        }

        // メモリを開放する
        imagedestroy($dest);
        return $result;
    }

    // LINE トークンによるメッセージ送信
    public static function sendLineMessages($accessToken, $replyToken, $messageType, $returnMessageText) {
        // レスポンスフォーマット
        $response_format_text = [
            'type' => $messageType,
            'text' => $returnMessageText
        ];

        // ポストデータ
        $post_data = [
            'replyToken' => $replyToken,
            'messages' => [$response_format_text]
        ];

        // curl実行
        $ch = curl_init('https://api.line.me/v2/bot/message/reply');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charser=UTF-8',
            'Authorization: Bearer ' . $accessToken
        ));
        $result = curl_exec($ch);
        curl_close($ch);
    }

    // LINE ユーザーIDによるメッセージ送信
    public static function pushLineMessage($to, $text) {
        $accessToken = config('services.line_message.access_token');
        $textObject = [
            [
            'type' => 'text',
            'text' => $text,
            ],
        ];

        $message = [
            'to' => $to,
            'messages' => $textObject,
        ];

        $message = json_encode($message);

        // curl実行
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/message/push');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ));
        $result = curl_exec($ch);
        curl_close($ch);
    }

}
