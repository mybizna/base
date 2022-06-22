<?php

namespace Modules\Base\Entities;

use Modules\Core\Entities\BaseModel AS Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;
use Illuminate\Database\Schema\Blueprint;
use Modules\Core\Classes\Migration;

class Timezone extends Model
{

    /**
     * Trait to allow softdeleting of records.
     */
    use SoftDeletes;

    /**
     * Traits for managing user modification details
     */
    use Userstamps;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_by', 'updated_by', 'deleted_at'];

    public $migrationDependancy = ['base_country'];

    protected $fillable = [
        "name",
        "country_id",
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'base_timezone';

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

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $table->increments('id');
        $table->string('name', 255);
        $table->unsignedInteger('country_id')->nullable()->default(null);
    }

    public function post_migration(Blueprint $table)
    {
        if (Migration::checkKeyExist('base_country', 'country_id')) {
            $table->foreign('country_id')->references('id')->on('base_country')->nullOnDelete();
        }
    }
}
