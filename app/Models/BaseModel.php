<?php

namespace Modules\Base\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Base\Events\ModelCreated;
use Modules\Base\Events\ModelDeleted;
use Modules\Base\Events\ModelUpdated;
use Wildside\Userstamps\Userstamps;

class BaseModel extends \Illuminate\Database\Eloquent\Model

{
    use SoftDeletes;
    /**
     * Adding Userstamps trait for tracking user actions
     *
     */
    use Userstamps;

    /**
     * Adding Notifiable trait for sending notifications
     *
     */
    use Notifiable;

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */

    public $alias = [];

    /**
     * Get the table associated with the model. Copies getTable() in Model
     *
     * @return string
     */
    public function getTableName(): string
    {
        $table = $this->table;
        return $table ?? Str::snake(Str::pluralStudly(class_basename(static::class)));
    }

    /**
     * Generate slug from string passed
     */
    public static function getSlug($slug)
    {
        $slug = preg_replace('/\s+/', ' ', $slug);

        $slug = str_replace(' ', '-', $slug);

        $slug = preg_replace('/[^A-Za-z0-9\-\_]/', '', $slug);

        return strtolower($slug);
    }

    /**
     * Get the user that created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the user that created the record.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the user that created the record.
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByCreatedAt($query)
    {
        return $query->orderBy('created_at', 'DESC');
    }

    protected static function boot()
    {
        parent::boot();

        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

        static::creating(function ($model) {

            $model->created_by = is_object(Auth::guard(config('app.guards.web'))->user()) ? Auth::guard(config('app.guards.web'))->user()->id : 1;
            $model->updated_by = null;

            if (isset($model->slug)) {
                $title = (isset($model->title)) ? $model->title : '';
                $title = (isset($model->name) && $title == '') ? $model->name : $title;
                $title = (isset($model->username) && $title == '') ? $model->username : $title;

                $model->slug = self::getSlug($model->slug) ?? self::getSlug($title);
            }
        });

        static::updating(function ($model) {
            $model->updated_by = is_object(Auth::guard(config('app.guards.web'))->user()) ? Auth::guard(config('app.guards.web'))->user()->id : 1;

            if (isset($model->slug)) {
                $title = (isset($model->title)) ? $model->title : '';
                $title = (isset($model->name) && $title == '') ? $model->name : $title;
                $title = (isset($model->username) && $title == '') ? $model->username : $title;

                $model->slug = self::getSlug($model->slug) ?? self::getSlug($title);
            }
        });

        static::saving(function ($model) {
            $model->updated_by = is_object(Auth::guard(config('app.guards.web'))->user()) ? Auth::guard(config('app.guards.web'))->user()->id : 1;

            if (isset($model->slug)) {
                $title = (isset($model->title)) ? $model->title : '';
                $title = (isset($model->name) && $title == '') ? $model->name : $title;
                $title = (isset($model->username) && $title == '') ? $model->username : $title;

                $model->slug = self::getSlug($model->slug) ?? self::getSlug($title);
            }
        });

        static::deleting(function ($model) {
            $model->deleted_by = is_object(Auth::guard(config('app.guards.web'))->user()) ? Auth::guard(config('app.guards.web'))->user()->id : 1;
        });

        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

        static::created(function ($model) {
            event(new ModelCreated($model->getTableName(), $model));
        });

        static::deleted(function ($model) {
            event(new ModelDeleted($model->getTableName(), $model));
        });

        static::updated(function ($model) {
            event(new ModelUpdated($model->getTableName(), $model));
        });

        static::saved(function ($model) {
            if ($model->updated_by == null) {
                event(new ModelCreated($model->getTableName(), $model));
            } else {
                event(new ModelUpdated($model->getTableName(), $model));
            }
        });
    }

    /**
     * Get the table associated with the model. Overrides getTable() in Model
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->getTableName();
    }
}
