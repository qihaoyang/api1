<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ImageRequest;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Resources\ImageResource;

class ImagesController extends Controller
{
    public function store(ImageRequest $request, ImageUploadHandler $uploadHandler, Image $image)
    {
        $user = $request->user();

        $size   = $request->type == 'avatar' ? '416' : '1024';
        $result = $uploadHandler->save($request->image, str()->plural($request->type), $user->id, $size);

        $image['path']    = $result['path'];
        $image['type']    = $request->type;
        $image['user_id'] = $user->id;
        $image->save();

        return new ImageResource($image);

    }
}
