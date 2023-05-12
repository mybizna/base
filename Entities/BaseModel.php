<?php

namespace Modules\Base\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Base\Events\ModelCreated;
use Modules\Base\Events\ModelDeleted;
use Modules\Base\Events\ModelUpdated;
use Wildside\Userstamps\Userstamps;

class BaseModel extends \Illuminate\Database\Eloquent\Model

{

    use Userstamps;
    //use SoftDeletes;
    //use SoftDeletes;
    use Notifiable;

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

    public static function getSlug($slug)
    {
        $slug = preg_replace('/\s+/', ' ', $slug);

        $slug = str_replace(' ', '-', $slug);

        $slug = preg_replace('/[^A-Za-z0-9\-\_]/', '', $slug);

        return strtolower($slug);
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

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'DESC');
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

    public function getAllRecords($args)
    {
        $result = [
            'module' => $this->module,
            'model' => $this->model,
            'status' => 0,
            'total' => 0,
            'error' => 1,
            'records' => [],
            'message' => 'No Records',
        ];

        $defaults = [
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'id',
            'order' => 'DESC',
            'count' => true,
            's' => [],

        ];

        $params = array_merge($defaults, $args);

        $query = $this->generateQuery($params);

        if ($params['count']) {
            $result['total'] = $query->count();
        }

        if (isset($params['offset']) && $params['offset'] > 1) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && $params['limit'] > 1) {
            $query->limit($params['limit']);
        }

        try {
            $result['error'] = 0;
            $result['status'] = 1;
            $result['records'] = $query->get();
            $result['message'] = 'Records Found Successfully.';
        } catch (\Throwable $th) {
            throw $th;
        }

        return $result;
    }

    public function getRecord($id, $args = [])
    {
        $result = [
            'module' => $this->module,
            'model' => $this->model,
            'id' => $id,
            'status' => 0,
            'error' => 1,
            'record' => [],
            'message' => 'No Record',
        ];

        $args['s'] = ['id' => $id];

        $query = $this->generateQuery($args);

        try {
            $result['error'] = 0;
            $result['status'] = 1;
            $result['record'] = $query->first();
            $result['message'] = 'Record Found Successfully.';
        } catch (\Throwable $th) {
            throw $th;
        }

        return $result;
    }

    public function getRecordSelect($args)
    {
        $result = [
            'module' => $this->module,
            'model' => $this->model,
            'status' => 0,
            'total' => 0,
            'error' => 1,
            'records' => [],
            'message' => 'No Records',
        ];

        $defaults = [
            'limit' => 200,
            'offset' => 0,
            'orderby' => 'id',
            'order' => 'DESC',
            'count' => false,
            's' => [],

        ];

        $params = array_merge($defaults, $args);

        $query = $this->generateQuery($params);

        if (isset($params['limit']) && $params['limit'] > 1) {
            $query->limit($params['limit']);
        }

        try {
            $records = $query->get();
            $list = collect();
            $list->push(['value' => '', 'label' => '--- Please Select ---']);

            $fields = $params['f'] ?? [];

            foreach ($records as $key => $record) {
                $template_str = $params['template'] ?? '';
                foreach ($fields as $key1 => $value) {
                    $template_str = str_replace('[' . $value . ']', $record->{$value}, $template_str);
                }

                $list->push(['value' => $record->id, 'label' => $template_str]);
            }

            $result['error'] = 0;
            $result['status'] = 1;
            $result['records'] = $list;
            $result['message'] = 'Records Found Successfully.';
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $result;
    }

    public function createRecord($args = [])
    {
        $result = [
            'module' => $this->module,
            'model' => $this->model,
            'status' => 0,
            'error' => 1,
            'record' => [],
            'message' => 'No Record',
        ];

        try {
            $result['error'] = 0;
            $result['status'] = 1;
            $result['record'] = $this->create($args);
            $result['message'] = 'Record Created Successfully.';
        } catch (\Throwable $th) {
            throw $th;
        }

        return $result;
    }

    public function updateRecord($id, $args = [])
    {
        $result = [
            'module' => $this->module,
            'model' => $this->model,
            'status' => 0,
            'error' => 1,
            'record' => [],
            'message' => 'No Record',
        ];

        try {
            $record = $this->where('id', $id)->first();

            $record->fill($args);

            $result['error'] = 0;
            $result['status'] = 1;
            $result['record'] = $record->save();
            $result['message'] = 'Record Updated Successfully.';
        } catch (\Throwable $th) {
            throw $th;
        }

        return $result;
    }
    public function deleteRecord($id)
    {
        $result = [
            'module' => $this->module,
            'model' => $this->model,
            'status' => 0,
            'error' => 1,
            'record' => [],
            'message' => 'No Record.',
        ];

        if (!isset($this->can_delete) || $this->can_delete) {
            try {
                $result['error'] = 0;
                $result['status'] = 1;
                $result['record'] = $this->where('id', $id)->firstorfail()->delete();
                $result['message'] = "ID:$id Record Delete Successfully.";
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        return $result;
    }

    public function generateQuery($params)
    {
        $this->alias = collect([]);
        $query = $this::query();

        $main_table_name = $this->table;
        list($main_table_alias, $alias_exist) = $this->getAlias($main_table_name);
        $select = collect([$main_table_alias . '.*']);

        $query->from($main_table_name . ' as ' . $main_table_alias);

        if (isset($params['f']) && is_array($params['f'])) {
            $select = collect([$main_table_alias . '.id']);
            $main_field = '';
            $as_arr = ['as', 'AS', 'As', 'aS'];

            foreach ($params['f'] as $field => $field_str) {

                $tables_concats = '';
                $parent_field_name = '';
                $field_name = '';
                $parent_alias = $main_table_alias;

                $f_alias = $f = trim(preg_replace("/\s+/", " ", $field_str), ' ');

                $field_arr = explode(' ', $f);
                if (!empty(array_intersect($as_arr, $field_arr))) {
                    $f = $field_arr[0];
                    $f_alias = $field_arr[2];
                }

                if (strpos($f, '__')) {
                    $table_levels = [$f];

                    if (strpos($f, '___')) {
                        $table_levels = explode('___', $f);
                    }

                    foreach ($table_levels as $key => $table_level) {

                        $table_parts = explode('__', $table_level);

                        $parent_field_name = (count($table_parts) == 2 && $key) ? $parent_field_name : $table_parts[0];
                        $sub_table_name = (count($table_parts) == 2 && $key) ? $table_parts[0] : $table_parts[1];
                        $field_name = (count($table_parts) == 2 && $key) ? $table_parts[1] : $table_parts[2];

                        $table_concat = $parent_field_name . '__' . $sub_table_name;

                        $tables_concats = ($tables_concats != '') ? $tables_concats . '___' . $table_concat : $table_concat;

                        list($table_alias, $alias_exist) = $this->getAlias($tables_concats);

                        if (!$alias_exist) {
                            $query->leftJoin($sub_table_name . ' AS ' . $table_alias, $table_alias . '.id', '=', $parent_alias . '.' . $parent_field_name);
                        }

                        $parent_alias = $table_alias;
                        $parent_field_name = $field_name;
                    }

                    $select->push($parent_alias . '.' . $field_name . ' AS ' . $f_alias);

                } else {
                    $main_field = $f;
                    $select->push($main_table_alias . '.' . $f . ' AS ' . $f_alias);
                }

            }

        }

        $query = $query->select($select->all());

        if (isset($params['order'])) {
            ($params['order'] == 'DESC') ? $query->orderByDesc($main_table_alias . '.' . $params['orderby']) : $query->orderBy($main_table_alias . '.' . $params['orderby']);
        }
        if (isset($params['s']) && is_array($params['s'])) {
            foreach ($params['s'] as $field => $s) {
                if (is_array($s)) {
                    $query->where($main_table_alias . '.' . $field, $s['ope'], $s['str']);
                } else {
                    $query->where($main_table_alias . '.' . $field, $s);
                }
            }
        }

        return $query;
    }

    private function getAlias($table_name, $field = '')
    {
        $table_name_field = ($field != '') ? $field . '_' . $table_name : $table_name;
        $alias_exist = false;
        $alpha = range('a', 'z');
        $key = $this->alias->search($table_name_field);

        if (!$key) {
            $this->alias->push($table_name_field);
            $key = $this->alias->search($table_name_field);
        } else {
            $alias_exist = true;
        }

        return [$alpha[$key], $alias_exist];
    }
}
