<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageContent extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'message_contents';
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function scopeReadBySupporter($query)
    {
        return $query->whereIn('message_contributor', ['実行者', '管理者'])->where('is_read', false)->update(['is_read' => true]);
    }

    public function scopeReadByExecutor($query)
    {
        return $query->where('message_contributor', '支援者')->where('is_read', false)->update(['is_read' => true]);
    }

    public function scopeCheckReadBySupporter($query)
    {
        return $query->whereIn('message_contributor', ['実行者', '管理者'])->where('is_read', false)->exists();
    }

    public function scopeCheckReadByExecutor($query)
    {
        return $query->where('message_contributor', '支援者')->where('is_read', false)->exists();
    }
}
