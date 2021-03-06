<?php

namespace Modules\Base\Entities;

use Illuminate\Support\Str;

class BaseModel extends \Illuminate\Database\Eloquent\Model
{


    /**
     * Get the table associated with the model. Copies getTable() in Model
     *
     * @return string
     */
    public function getTableName(): string
    {
        $table =  $this->table;
        return $table ?? Str::snake(Str::pluralStudly(class_basename(static::class)));
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
            'module'  => $this->module,
            'model'   => $this->model,
            'status'  => 0,
            'total'   => 0,
            'error'   => 1,
            'records'    => [],
            'message' => 'No Records'
        ];

        $defaults = [
            'limit'   => 20,
            'offset'  => 0,
            'orderby' => 'id',
            'order'   => 'DESC',
            'count'   => false,
            's'       => [],

        ];

        $params = array_merge($defaults, $args);

        $query = $this->generateQuery($params);

        if ($params['count']) {
            $result['total'] = $query->count();
        }

        try {
            //code.. $result['error'] = 0;
            $result['status'] = 1;
            $result['records'] = $query->get();
            $result['message'] = 'Records Found Successfully.';
        } catch (\Throwable $th) {
            //throw $th;
        }


        return $result;
    }

    public function getRecord($id, $args = [])
    {
        $result = [
            'module'  => $this->module,
            'model'   => $this->model,
            'id'  => $id,
            'status'  => 0,
            'error'   => 1,
            'record'    => [],
            'message' => 'No Record'
        ];

        $query = $this->generateQuery($args);

        try {
            //code..
            $result['error'] = 0;
            $result['status'] = 1;
            $result['record'] = $query->first();
            $result['message'] = 'Record Found Successfully.';
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $result;
    }

    public function getRecordSelect($args)
    {
        $result = [
            'module'  => $this->module,
            'model'   => $this->model,
            'status'  => 0,
            'total'   => 0,
            'error'   => 1,
            'records'    => [],
            'message' => 'No Records'
        ];

        $defaults = [
            'limit'   => 200,
            'offset'  => 0,
            'orderby' => 'id',
            'order'   => 'DESC',
            'count'   => false,
            's'       => [],

        ];

        $params = array_merge($defaults, $args);

        $query = $this->generateQuery($params);

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

            //code.. $result['error'] = 0;
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
            'module'  => $this->module,
            'model'   => $this->model,
            'status'  => 0,
            'error'   => 1,
            'record'    => [],
            'message' => 'No Record'
        ];

        try {
            //code..
            $result['error'] = 0;
            $result['status'] = 1;
            $result['record'] = $this->create($args);
            $result['message'] = 'Record Created Successfully.';
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function updateRecord($args = [])
    {
        $result = [
            'module'  => $this->module,
            'model'   => $this->model,
            'status'  => 0,
            'error'   => 1,
            'record'    => [],
            'message' => 'No Record'
        ];

        $this->fill($args);
        $this->save();

        try {
            //code..
            $result['error'] = 0;
            $result['status'] = 1;
            $result['record'] = $this->save();
            $result['message'] = 'Record Updated Successfully.';
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public function deleteRecord($id)
    {
        $result = [
            'module'  => $this->module,
            'model'   => $this->model,
            'status'  => 0,
            'error'   => 1,
            'record'    => [],
            'message' => 'No Record'
        ];

        try {
            //code..
            $result['error'] = 0;
            $result['status'] = 1;
            $result['record'] = $this->where('id', $id)->firstorfail()->delete();
            $result['message'] = 'Record Found Successfully.';
        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    public function generateQuery($params)
    {

        $alias = collect(['']);
        $query = $this::query();

        $table_name = $this->table;
        list($main_table_alias, $alias_exist, $alias) = $this->getAlias($table_name, $alias);
        $select = collect([$main_table_alias . '.*']);

        $query->from($table_name . ' as ' . $main_table_alias);

        if (isset($params['f']) && is_array($params['f'])) {
            $select = collect([$main_table_alias . '.id']);
            $main_field = '';


            foreach ($params['f'] as $field => $f) {
                list($table_alias, $alias_exist, $alias) = $this->getAlias($table_name, $alias);

                if (strpos($f, '.')) {
                    $tables_concat = '';
                    $sub_main_field =  $main_field;
                    $pre_leftjoin_alias = $table_alias;

                    $table_levels = explode('.', $f);
                    foreach ($table_levels as $key => $table_level) {
                        $table_parts = explode('__', $table_level);
                        $tables_concat = ($tables_concat == '') ? $sub_main_field . '_' . $table_parts[0] : $tables_concat . '_' .  $sub_main_field . '_' .  $table_parts[0];

                        list($leftjoin_alias, $alias_exist, $alias) = $this->getAlias($tables_concat, $alias);
                        $leftjoin_table = $table_parts[0] . ' as ' . $leftjoin_alias;

                        if (!$alias_exist) {
                            $query->leftJoin($leftjoin_table, $leftjoin_alias . '.id', '=', $pre_leftjoin_alias . '.' . $sub_main_field);
                        }

                        $sub_main_field = $table_parts[1];
                        $pre_leftjoin_alias = $leftjoin_alias;
                    }

                    $select->push($pre_leftjoin_alias . '.' . $sub_main_field . ' as ' . $main_field . '.' . $f);
                } elseif (strpos($f, '__')) {
                    $table_parts = explode('__', $f);

                    list($leftjoin_alias, $alias_exist, $alias) = $this->getAlias($main_field . '_' . $table_parts[0], $alias);
                    $leftjoin_table = $table_parts[0] . ' as ' . $leftjoin_alias;

                    if (!$alias_exist) {
                        $query->leftJoin($leftjoin_table, $leftjoin_alias . '.id', '=', $main_table_alias . '.' . $main_field);
                    }

                    $select->push($leftjoin_alias . '.' . $table_parts[1] . ' as '  . $main_field . '.' . $f);
                } else {
                    $main_field = $f;
                    $select->push($main_table_alias . '.' . $f);
                }
            }
        }

        $query = $query->select($select->all());

        if (isset($params['limit']) && $params['limit'] > 1) {
            $query->limit($params['limit']);
        }

        if (isset($params['order'])) {
            ($params['order'] == 'DESC') ? $query->orderByDesc($main_table_alias . '.' . $params['orderby']) : $query->orderBy($main_table_alias . '.' . $params['orderby']);
        }
        if (isset($params['s']) && is_array($params['s'])) {
            foreach ($params['s'] as $field => $s) {
                if (is_array($s)) {
                    $query->where($field, $s['ope'], $s['str']);
                } else {
                    $query->where($field, $s);
                }
            }
        }

        return $query;
    }

    private function getAlias($table_name, $alias)
    {
        $alias_exist = false;
        $alpha = range('a', 'z');
        $key = $alias->search($table_name);

        if (!$key) {
            $alias->push($table_name);
            $key = $alias->search($table_name);
        } else {
            $alias_exist = true;
        }

        return [$alpha[$key], $alias_exist, $alias];
    }
}
