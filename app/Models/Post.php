<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public $casts = [
		'published_at' => 'datetime:d, M Y H:i',
	];

    public const EXCERPT_LENGTH = 100;
    public const DRAFT = 0;
    public const ACTIVE = 1;

    public const STATUS = [
        self::DRAFT => 'draft',
        self::ACTIVE => 'published'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at');
    }

    public function excerpt() 
    {
        return Str::limit($this->body, Post::EXCERPT_LENGTH);
    }

    public function scopeActivePost($query)
    {
        return $query->where('published', self::ACTIVE)
            ->where('published_at', '<=', Carbon::now());
    }

    public function getNextPostAttribute()
    {
        $next_post = self::activePost()
            ->where('published_at', '>', $this->published_at)
            ->orderBy('published_at', 'asc')
            ->first();

        return $next_post;
    }

    public function getPrevPostAttribute()
    {
        $prev_post = self::activePost()
            ->where('published_at', '<', $this->published_at)
            ->orderBy('published_at', 'desc')
            ->first();

        return $prev_post;
    }

    public function scopeFilter($query, array $filter)
    {
        $query->when($filter['search'] ?? null, function($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        })->when($filter['trashed'] ?? null, function($query, $trashed) {
            if ($trashed == 'with') {
                $query->withTrashed();
            } else if ($trashed == 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function getPathAttribute()
    {
        return "/posts/$this->slug";
    }

    public function getStatusAttribute()
    {
        return self::STATUS[$this->published];
    }
}
