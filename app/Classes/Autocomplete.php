<?php

namespace Modules\Base\Classes;

use Illuminate\Support\Facades\DB;

/**
 * Autocomplete class
 *
 * This class is used to get the data for the autocomplete
 *
 * @package Modules\Base\Classes
 */
class Autocomplete
{
    /**
     * Data result
     *
     * This function is used to get the data result
     *
     * @param string $search
     * @param string $table_name
     * @param array $display_fields
     * @param string $search_fields
     * @param array $leftjoin_criteria
     *
     * @return object
     */
    public function dataResult($search, $table_name, $display_fields, $search_fields, $leftjoin_criteria = [])
    {

        $query = DB::table($table_name . ' as amm');
        $query->select($display_fields);
        $query->whereRaw($search_fields);

        if (!empty($leftjoin_criteria)) {
            foreach ($leftjoin_criteria as $key => $leftjoin) {
                $query->leftJoin($leftjoin->table_name, $leftjoin->condition1, $leftjoin->operator, $leftjoin->condition2);
            }
        }
        $records = $query->get();

        return $records;

    }

}
