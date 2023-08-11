<?php

namespace Modules\Base\Classes\Views;

class Common
{

    /**
     * Set the name of the field
     *
     * @param string $name
     */
    public function getLabel($name)
    {
        if ($name == 'id') {
            return 'ID';
        } else {
            $name = str_replace('_id', '', $name);
            $name = str_replace('_', ' ', $name);
            $name = ucwords($name);
        }

        return $name;
    }

}
