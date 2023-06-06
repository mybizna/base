<?php

namespace Modules\Base\Classes;

use Illuminate\Support\Facades\DB;

class Migration
{

    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    //Data Modules
    public static function checkKeyExist($table, $field, $type = 'foreign')
    {
        $keys = DB::select(DB::raw("SHOW KEYS from $table"));
        $key_name = $table . $field . $type;

        foreach ($keys as $item) {
            if ($item->Key_name == $key_name) {
                return true;
            }
        }

        return false;
    }

    public static function addForeign($table, $foreign_name, $field_name, $type = 'foreign')
    {
        $table_name = $table->getTable();

        if (self::checkKeyExist($table_name, $field_name)) {
            $table->foreign($field_name)->references('id')->on($foreign_name)->nullOnDelete();
        }
    }
}
