<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,
            'due_date'    => $this->due_date?->toDateString(),
            'assigned_user' => $this->whenLoaded('assignedUser', fn() => [
                'id'    => $this->assignedUser->id,
                'name'  => $this->assignedUser->name,
                'email' => $this->assignedUser->email,
            ]),
            'created_by' => $this->whenLoaded('creator', fn() => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}