<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopicRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Queries\TopicQuery;

class TopicsController extends Controller
{
    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = auth('api')->id();
        $topic->save();

        return new TopicResource($topic);
    }

    public function update(Topic $topic, TopicRequest $request)
    {
        $this->authorize('update', $topic);
        $topic->update($request->all());
        return new TopicResource($topic);
    }

    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);
        $topic->delete();
        return response(null, 204);
    }

    public function index(TopicQuery $query, Request $request)
    {
        $topics = $query->paginate();

        return TopicResource::collection($topics);
    }

    public function userIndex(TopicQuery $query, User $user, Request $request)
    {
        $topics = $query->where('user_id', $user->id)->paginate();
        return TopicResource::collection($topics);
    }

    public function show($topic_id, TopicQuery $query)
    {
        $topic = $query->findOrFail($topic_id);

        return new TopicResource($topic);
    }
}
