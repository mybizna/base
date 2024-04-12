<?php

namespace Modules\Base\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Views\Fields;
use Modules\Base\Events\ModelCreated;
use Modules\Base\Events\ModelDeleted;
use Modules\Base\Events\ModelUpdated;
use Wildside\Userstamps\Userstamps;

class BaseModel extends \Illuminate\Database\Eloquent\Model

{

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
     * The table associated with the model.
     *
     * @var string
     */

    public $listtable;

    /**
     * The fields that can be filled
     * 
     * @var array<string>
     */
    public $formbuilder;

    /**
     * Intialize fields for display and migration purpose.
     * 
     * @param array $fields
     */
    public $fields = [];

    /**
     * Set structure for displays ie table, form, filter, etc.
     * 
     * @param array $structure
     */
    public $structure = [];

    /**
     * Set if model is visible from frontend.
     *
     * @var bool
     */
    public bool $show_frontend = false;


    /**
     * Set views is visible from frontend.
     *
     * @var array
     */

    public array $show_views = ["list" => true, "create" => false, "edit" => false, "detail" => true, "form" => false, "search" => true, "modify" => false, "update" => false, "new" => false, "delete" => false, "print" => false, "export" => false, "import" => false, "report" => false, "chart" => false, "calendar" => false, "timeline" => false, "kanban" => false, "gantt" => false, "map" => false, "tree" => false, "grid" => false, "table" => false, "card" => false];

    /**
     * Function for defining fields
     */
    public function fields(Blueprint $table = null): void
    {
        //Your actions here
    }

    /**
     * Function for defining structure
     */
    public function structure($structure): array
    {
        //Your actions here
        return $structure;
    }

        /**
     * Define rights for this model.
     *
     * @return array
     */
    public function rights(): array
    {
        return [
            ['administrator' => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true]],
            ['manager' => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true]],
            ['supervisor' => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true]],
            ['staff' => ['view' => true, 'add' => true, 'edit' => true]],
            ['registered' => ['view' => true, 'add' => true]],
            ['guest' => ['view' => true]],
        ];
    }

    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $fields = $this->fields($table);

    }
    /**
     * Handle post migration processes for adding foreign keys.
     *
     * @param Blueprint $table
     *
     * @return void
     */
    public function post_migration(Blueprint $table): void
    {
        $columns = $table->getColumns();

        $arr_columns = [];
        foreach ($columns as $column) {
            $arr_column = $column->toArray();
            if ($arr_column['html'] == 'recordpicker' && isset($arr_column['relation'])) {
                $table_name = implode('_', $arr_column['relation']);
                Migration::addForeign($table, $table_name, $arr_column['name']);
            }
        }

    }

    /**
     * Get both structure and fields
     */
    public function getStructureAndFields($as_array): array
    {
        $fields = $this->getFields($as_array);
        $structure = $this->getStructure($fields);

        return [
            'structure' => $structure,
            'fields' => $fields,
        ];
    }

    /**
     * Array of system structure for all pages
     *
     * @return Array
     *
     */
    public function getStructure($fields = null): array
    {
        if ($fields) {
            $fields = $this->getFields(true);
        }

        $fields_names_arr = array_keys($fields);

        if (($key = array_search('id', $fields_names_arr)) !== false) {
            unset($fields_names_arr[$key]);
        }

        $len = (int) count($fields_names_arr);

        $firsthalf = array_slice($fields_names_arr, 0, $len / 2);
        $secondhalf = array_slice($fields_names_arr, $len / 2);

        $structure = $this->structure([
            'table' => $fields_names_arr,
            'form' => [
                ['label' => '', 'class' => 'col-span-full  md:col-span-6 md:pr-2', 'fields' => $firsthalf],
                ['label' => '', 'class' => 'col-span-full  md:col-span-6 md:pr-2', 'fields' => $secondhalf],
            ],
            'filter' => $fields_names_arr,
        ]);

        return $structure;
    }

    /**
     * Array of system fields for all pages
     *
     * @param boolean $as_array
     *
     * @return Blueprint|Array
     */
    public function getFields($as_array = false): Blueprint | array
    {

        if (empty($this->fields)) {
            $this->fields();
        }

        if ($as_array) {
            $columns = $this->fields->getColumns();

            $arr_columns = [];
            foreach ($columns as $column) {
                $field = $column->toArray();

                if (!isset($field['html']) || $field['html'] == '') {
                    $field['html'] = 'text';
                }
                $arr_columns[$column->get('name')] = $field;
            }

            return $arr_columns;
        }

        return $this->fields;
    }

    /**
     * Get rec name
     */
    public function getRecNames()
    {
        $rec_names = $this->rec_names ?? ['id'];
        return $rec_names;
    }

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
            $query = $this->generateQuery($params);
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

    /**
     * Function for deleting a record.
     *
     * @param int $id
     *
     * @return array
     */
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
