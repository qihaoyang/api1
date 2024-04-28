<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    //是否展示敏感信息，默认否
    protected $showSensitiveFlag = false;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (!$this->showSensitiveFlag) {
            $this->resource->makeHidden('phone', 'email');
        }

        $data                = parent::toArray($request);
        $data['bound_phone'] = $this->resource->phone ? true : false;
        $data['bound_wechat'] = ($this->resource->weixin_openid || $this->resource->weixin_unionid) ? true : false;
        return $data;
    }

    public function showSensitiveFields()
    {
        $this->showSensitiveFlag = true;
        return $this;
    }
}
