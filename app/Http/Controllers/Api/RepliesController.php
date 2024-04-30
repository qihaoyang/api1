<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Queries\ReplyQuery;
use App\Http\Requests\Api\ReplyRequest;
use App\Http\Resources\ReplyResource;
use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
    public function store(ReplyRequest $request, Topic $topic, Reply $reply)
    {
        $reply->content  = $request->input('content');
        $reply->topic_id = $topic->id;
        $reply->user_id  = auth('api')->id();
        $reply->save();

        return new ReplyResource($reply);
    }

    public function destroy(Topic $topic, Reply $reply)
    {
        if ($topic->id != $reply->topic_id) {
            abort('404');
        }

        $this->authorize('destroy', $reply);

        return response(null, 204);

    }

    public function index(Topic $topic, ReplyQuery $query)
    {
        $replies = $query->where('topic_id', $topic->id)
            ->paginate();
        return ReplyResource::collection($replies);

    }

    public function userIndex(User $user,ReplyQuery $query)
    {
        $replies = $query->where('user_id',$user->id)
            ->paginate();
        return ReplyResource::collection($replies);
    }
}
