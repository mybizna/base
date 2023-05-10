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
            $output = new \Symfony\Component\Console\Output\ConsoleOutput ();
            $output->writeln("<info>my " . $message . "</info>");
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
