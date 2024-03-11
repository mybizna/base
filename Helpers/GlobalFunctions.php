<?php

use Modules\Base\Jobs\AppMailerJob;
use Modules\Core\Classes\Language;
use Modules\Core\Entities\LanguageTranslation;

if (!function_exists('___')) {

    function ___($slug)
    {

        $string = $slug;

        $language = new Language();

        $default_language = $language->getDefaultLanguage();
        $language_id = $default_language->id;

        if (Cache::has("core_language_translation_" . $language_id . '_' . $slug)) {
            $translation = Cache::get("core_language_translation_" . $language_id . '_' . $slug);
            return $translation;
        } else {
            try {
                $translation = LanguageTranslation::where('language_id', $language_id)
                    ->where('slug', $slug)
                    ->first();

                if ($translation) {
                    Cache::put("core_language_translation_" . $language_id . '_' . $slug, $translation->phrase, 3600);
                    return $translation->phrase;
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        return $string;
    }
}

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

if (!function_exists('console_log')) {

    function console_log($message)
    {
        try {
            $output = new \Symfony\Component\Console\Output\ConsoleOutput();
            $output->writeln("<info>my " . $message . "</info>");
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

if (!function_exists('rendercss')) {

    function rendercss($assets)
    {
        try {
            $css = '';

            if (isset($assets['css'])) {
                foreach ($assets['css'] as $asset) {

                    $tmp_css = ($css == '') ? '<' : '    <';
                    array_unshift($asset, "link");
                    // Merge all array values into a string
                    foreach ($asset as $key => $value) {
                        if ($key == 0) {
                            $tmp_css .= $value . ' ';
                        } else {
                            $tmp_css .= $key . '="' . $value . '" ';
                        }
                    }
                    $css .= $tmp_css .= "> \n";

                }
            }

            return $css;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

if (!function_exists('renderjs')) {

    function renderjs($assets)
    {
        try {
            $js = '';

            if (isset($assets['js'])) {
                foreach ($assets['js'] as $asset) {

                    $tmp_js = ($js == '') ? '<' : '    <';
                    array_unshift($asset, "script");
                    // Merge all array values into a string
                    foreach ($asset as $key => $value) {
                        if ($key == 0) {
                            $tmp_js .= $value . ' ';
                        } else {
                            $tmp_js .= $key . '="' . $value . '" ';
                        }
                    }
                    $js .= $tmp_js .= "></script> \n";

                }
            }

            return $js;
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

        $result = $query->toSql();

        foreach ($params as $key => $param) {
            $result = preg_replace('/\?/', $param, $sql, 1);
        }

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
