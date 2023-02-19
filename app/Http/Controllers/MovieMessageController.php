<?php

namespace App\Http\Controllers;

use App\Events\MovieMessageReceived;
use App\Models\MovieMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MovieMessageController extends Controller
{

    /**
     * メッセージの一覧取得
     * 【注意】認証が通っていない場合もある
     */
    public function show(Request $request)
    {
//        $user = User::where('api_token', $request->input('api_token'))->first();

        $movieId = $request->input('movie_id');
        $messages = MovieMessage::with(['user:id,image,name,profile', 'user.user_data'])
            ->where('movie_id', $movieId)->orderBy('created_at', 'desc')->get();
        foreach ($messages as $message) {
            $message->user->image_path = $message->user->getImagePath();
        }
        if (empty($messages)) {
            abort(404);
        }

        return $messages;
    }

    /**
     * メッセージ受信
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data.content' => 'required|max:128',
        ]);
        if ($validator->fails()) {
            return false;
        }

        $message = new MovieMessage();
        if (Auth::guard('api')->check()) {
            $message->user_id = Auth::guard('api')->id();
        } else {
            return false;
        }
        $message->movie_id = $request->input('data.movie_id');
        $message->content = $request->input('data.content');
        $message->save();

        // イベントを発火
        MovieMessageReceived::dispatch($message);

        return response($message, 201);
    }

    /**
     * メッセージ削除
     */
    public function delete(Request $request)
    {
        $messageId = $request->input('message_id');
        $message = MovieMessage::where('id', $messageId)->first();
        $movieId = $message->movie_id;
        $message->delete();

        return;
    }

}

