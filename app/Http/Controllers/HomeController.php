<?php
namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    // トップ
    public function index(Request $request)
    {
        $rooms = Room::where('status', 1)->orderBy('published_at', 'desc')->get();

        $recentUsers = Room::select('rooms.user_id as user_id', DB::raw('count(rooms.id) as room_count'))
            ->leftJoin('users', 'users.id', '=', 'rooms.user_id')
            ->where('published_at', '>=', date('Y-m-d H:i:s', strtotime('-7 day')))
            ->where('stream_time', '>=', '00:10:00')
            ->where(function($query) {
                $query->orWhere('rooms.status', 1)
                    ->orWhere('rooms.status', 2);
            })
            ->groupBy('rooms.user_id')
            ->orderBy('room_count', 'desc')->limit(10)->get();

        foreach ($recentUsers as $recentUser) {
            $user = User::where('id', $recentUser->user_id)->first();
            $recentUser->name = $user->name;
            $recentUser->user_image_path = $user->getImagePath();
        }

        // 公認配信者
        $officialUsers = User::select('users.id as user_id', 'users.name as name')
            ->leftJoin('user_datas', 'users.id', '=', 'user_datas.user_id')
            ->where(['user_datas.rank' => 5])->get();
        foreach ($officialUsers as $officialUser) {
            $user = User::where('id', $officialUser->user_id)->first();
            $officialUser->user_image_path = $user->getImagePath();
        }

        // フォロー数の多いユーザー
        $recentRooms = Room::select('rooms.user_id as user_id')
            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-7 day')))
            ->groupBy('user_id')
            ->get();
        $followerUsers = [];
        $sortKey = [];
        foreach ($recentRooms as $recentRoom) {
            $user = $recentRoom->user;
            $followerUsers[] = $user;
            $sortKey[] = $user->followers->count();
        }
        if ($followerUsers) {
            array_multisort($sortKey, SORT_DESC, $followerUsers);
        }

        $archiveRooms = Room::where('status', 2)->orderBy('finished_at', 'desc')->limit(4)->get();

        $groups = Group::where('is_publish', '1')->inRandomOrder()->get();
        foreach($groups as $group) {
            $user = User::where('id', $group->user_id)->first();
            $group->user_image_path = $user->getImagePath();
        }

        return view('home', [
            'archiveRooms' => $archiveRooms,
            'followerUsers' => $followerUsers,
            'officialUsers' => $officialUsers,
            'recentUsers' => $recentUsers,
            'rooms' => $rooms,
            'groups' => $groups,
        ]);
    }

}
