<?php

use Illuminate\Support\Facades\DB;

use Modules\Base\Jobs\AppMailerJob;

if (!function_exists('sendmail')) {

    function sendmail(array $data)
    {
        try {
            dispatch(new AppMailerJob($data));
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

if (!function_exists('getRealQuery')) {
    function getRealQuery($query, $dumpIt = false)
    {
        $params = array_map(function ($item) {
            return "'{$item}'";
        }, $query->getBindings());
        $result = str_replace_array('\?', $params, $query->toSql());
        if ($dumpIt) {
            dd($result);
        }
        return $result;
    }
}

function messageBag($key, $message)
{
    $messageBag = [];

    if (config()->has('core.messageBag')) {
        $messageBag = config('core.messageBag');
    }

    config(['kernel.messageBag' => $messageBag]);
}


