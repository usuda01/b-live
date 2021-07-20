<?php

namespace App\Http\Controllers;

use App\Room;
use App\RoomRanking;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomRankingController extends Controller
{

    public function index(Request $request, $targetMonth, $targetRank) {
        if ($targetMonth == 0) {
        }
        return view('room-ranking.index', [
            'targetMonth' => $targetMonth,
            'targetRank' => $targetRank
        ]);
    }

    public function getRooms(Request $request, $targetMonth, $targetRank) {
        // 当月の場合はリアルタイムで集計する必要があるため、別ロジック
        if ($targetMonth == 0) {
            return $this->getRoomsCurrentMonth($request, $targetMonth, $targetRank);
        } else if ($targetMonth == 1) {
            $rooms = RoomRanking::where('room_rankings.created_at', '>=', date('Y-m-d H:i:s', strtotime('-1 month')));
        } else if ($targetMonth == 2) {
            $rooms = RoomRanking::where('room_rankings.created_at', '>=', date('Y-m-d H:i:s', strtotime('-2 month')))
                ->where('room_rankings.created_at', '<=', date('Y-m-d H:i:s', strtotime('-1 month')));
        }
        $rooms->join('rooms', 'room_rankings.room_id', '=', 'rooms.id');

        if ($targetRank == 4) {
            // Aランク
            $rooms->where('room_rankings.rank', 4);
        } else if ($targetRank == 3) {
            // Bランク
            $rooms->where('room_rankings.rank', 3);
        } else if ($targetRank == 2) {
            // Cランク
            $rooms->where('room_rankings.rank', 2);
        } else if ($targetRank == 1) {
            // Dランク
            $rooms->where('room_rankings.rank', 1);
        }

        $rooms = $rooms->with(['user' => function ($q) {
            $q->select('id', 'name');
        }]);

        $rooms = $rooms->orderBy('room_rankings.max_view', 'desc')->orderBy('rooms.finished_at', 'desc')->paginate(10);
        foreach ($rooms as $room) {
            $room->name = $room->room->name;
            $room->image_path = $room->room->getImagePath();
            $room->user_name = $room->user->name;
            $room->user_image_path = $room->user->getImagePath();
        }
        return $rooms;
    }

    public function getRoomsCurrentMonth(Request $request, $targetMonth, $targetRank) {

        $rooms = Room::where(function($query) {
            $query->where('rooms.status', 1)
                ->orWhere('rooms.status', 2);
            })
            ->where(function($query) {
                $query->where('published_at', '>=', date('Y-m-1 00:00:00'))
                    ->where('published_at', '<', date('Y-m-1 00:00:00', strtotime('+1 months')));
            });

        if ($targetRank == 4) {
            // Aランク
            $rooms->where('rooms.rank', 4);
        } else if ($targetRank == 3) {
            // Bランク
            $rooms->where('rooms.rank', 3);
        } else if ($targetRank == 2) {
            // Cランク
            $rooms->where('rooms.rank', 2);
        } else if ($targetRank == 1) {
            // Dランク
            $rooms->where('rooms.rank', 1);
        }

        $rooms = $rooms
            ->leftJoin('users', 'users.id', '=', 'rooms.user_id')
            ->join('user_datas', function($join) {
                $join->on('users.id', '=', 'user_datas.user_id')
                    ->where('user_datas.join_ranking',  1);
            })
            ->select(
                'rooms.id as room_id',
                'rooms.max_view',
                'rooms.name',
                'rooms.status',
                'users.id as user_id',
                'users.name as user_name',
                'user_datas.id',
                'user_datas.point',
                'user_datas.join_ranking'
            );

        $rooms = $rooms
            ->orderBy('max_view', 'desc')
            ->orderBy('status', 'asc')
            ->orderBy('finished_at', 'desc')
            ->paginate(10);

        foreach ($rooms as $data) {
            $user = User::where('id', $data->user_id)->first();
            $room = Room::where('id', $data->room_id)->first();

            $data->room_id = $data->room_id;
            $data->user_name = $data->user_name;
            $data->image_path = $room->getImagePath();
            $data->user_image_path = $user->getImagePath();
        }

        return $rooms;
    }
}
