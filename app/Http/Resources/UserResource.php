<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'role'  => $this->whenLoaded('role', fn() => [
                'id'           => $this->role->id,
                'name'         => $this->role->name,
                'display_name' => $this->role->display_name,
            ]),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}