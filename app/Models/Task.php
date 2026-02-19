<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'assigned_user_id',
        'created_by',
        'due_date'
    ];

    protected $casts = [
        'due_date' => 'date'
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id')
                    ->select(['id', 'name', 'email']);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')
                    ->select(['id', 'name', 'email']);
    }
}