<?php

namespace App\Models;

use App\Traits\SearchFunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Request;

class Comment extends Model
{
    use HasFactory, SoftDeletes, SearchFunctions;

    protected $fillable = [
        'project_id',
        'payment_id',
        'content'
    ];

    public static function boot()
    {
        parent::boot();
        static::deleting(function (Comment $comment) {
            $comment->reply()->delete();
        });
    }

    public function reply()
    {
        return $this->hasOne('App\Models\Reply');
    }

    public function payment()
    {
        return $this->belongsTo('App\Models\Payment');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    public function scopeGetOwnComments($query)
    {
        return $query->whereIn(
            'payment_id',
            Payment::query()->select('id')
                ->where('user_id', \Auth::id())
        );
    }

    public function scopeNarrowDownWithProject($query)
    {
        if (Request::get('project')) {
            return $query->where('project_id', Request::get('project'));
        }
    }

    public function scopeSearch($query)
    {
        if ($this->getSearchWordInArray()) {
            foreach ($this->getSearchWordInArray() as $word) {
                $query->where(function ($query) use ($word) {
                    $query->where('content', 'like', "%$word%")
                    ->orWhereIn('payment_id', Payment::select('id')
                    ->whereIn('user_id',User::select('id')->where('name','like',"%$word%")))
                    ->orWhereIn('id', Reply::select('comment_id')->where('content', 'like', "%$word%"))
                    ->orWhereIn('project_id', Project::select('id')->where('title', 'like', "%$word%"))
                    ->orWhereIn('id', Reply::select('comment_id')
                    ->whereIn('user_id',User::select('id')->where('name','like',"%$word%")));
                }); 
            }
        }
    }

    public function scopeSearchWithPostDates($query, $from_date, $to_date)
    {
        if ($from_date !== null && $to_date !== null) {
            $query->whereBetween('created_at', [$from_date, $to_date])
                ->orderBy('created_at', 'asc');
        } elseif ($from_date !== null) {
            $query->where('created_at', '>=', $from_date)
                ->orderBy('created_at', 'asc');
        } elseif ($to_date !== null) {
            $query->where('created_at', '<=', $to_date)
                ->orderBy('created_at', 'desc');
        }
        return $query;
    }
}
