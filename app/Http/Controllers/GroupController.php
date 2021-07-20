<?php
namespace App\Http\Controllers;

use App\Models\Group;

class GroupController extends Controller
{
    public function detail($groupId) {
        $group = Group::where('id', $groupId)->first();
        if (!$group) {
            abort(404);
        }
        $group->user_image_path = $group->user->getImagePath();

        return view('group.detail', [
            'group' => $group,
        ]);
    }
}
