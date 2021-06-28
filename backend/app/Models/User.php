<?php

namespace App\Models;

use App\Casts\ImageCast;
use App\Casts\HashMake;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'birthday',
        'gender',
        'introduction',
        'phone_number',
        'birthday_is_published',
        'gender_is_published',
        'image_url',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'image_url' => ImageCast::class,
        'password' => HashMake::class,
    ];

    protected $dates = ['deleted_at'];

    public static function boot()
    {
        parent::boot();

        static::deleting(function (User $user) {
            $user->snsUser()->delete();
            $user->address()->delete();
            $user->snsLinks()->delete();
            $user->bankAccount()->delete();
            $user->profile()->delete();

            // 中間テーブルの削除
            UserProjectLiked::where('user_id', $user->id)
                ->update(['deleted_at' => Carbon::now()]);
            $comment_ids = Comment::where('user_id', $user->id)->pluck('id')->toArray();
            Reply::whereIn('comment_id', $comment_ids)->delete();
            Comment::destroy($comment_ids);
            $payment_ids = $user->payments()->pluck('id');
            MessageContent::whereIn('payment_id', $payment_ids)->delete();
            PlanPaymentIncluded::whereIn('payment_id', $payment_ids)->delete();
            Payment::destroy($payment_ids);
        });
    }

    public function supportComments()
    {
        return $this->hasMany('App\Models\SupporterComment');
    }

    public function userSupporterCommentLiked()
    {
        return $this->belongsToMany('App\Models\SupporterComment', 'App\Models\UserSupporterCommentLiked');
    }

    public function projects()
    {
        return $this->hasMany('App\Models\Project');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }

    public function likedProjects()
    {
        return $this->belongsToMany('App\Models\Project', 'user_project_liked')
            ->using('App\Models\UserProjectLiked')
            ->withTimestamps();
    }

    public function address()
    {
        return $this->hasOne('App\Models\Address');
    }

    public function profile()
    {
        return $this->hasOne('App\Models\Profile');
    }

    public function snsUser()
    {
        return $this->hasOne('App\Models\SnsUser');
    }

    public function replies()
    {
        return $this->hasMany('App\Models\Reply');
    }

    public function snsLinks()
    {
        return $this->hasMany('App\Models\SnsLink');
    }

    public function bankAccount()
    {
        return $this->hasOne('App\Models\BankAccount');
    }

    //--------------- local scopes -------------
    public function scopeGetUsers()
    {
        return $this->paginate(10);
    }

    public function scopeSearchWord($query, $words)
    {
        return $query->where(function ($user) use ($words) {
            foreach ($words as $word) {
                $user->Where('name', 'like', "%$word%");
                $user->orWhere('email', 'like', "%$word%");
            }
        })->paginate(10);
    }

    public function scopeSearchUsersToArray($query, $word): array
    {
        return $query->where('name', 'like', "%$word%")->pluck('id')->toArray();
    }

    public function scopePluckNameAndId($query)
    {
        return $query->pluck('name', 'id');
    }

    //--------------- local scopes -------------



    //--------------- functions -------------
    public function deleteImageIfSample(): void
    {
        if (strpos($this->image_url, 'sampleImage') === false) {
            Storage::delete($this->image_url);
        };
    }
    //--------------- functions -------------
}
