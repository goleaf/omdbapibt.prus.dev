<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

class HtmlSanitizer
{
    protected static ?HTMLPurifier $purifier = null;

    public static function clean(string $value): string
    {
        if (static::$purifier === null) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('Core.Encoding', 'UTF-8');

            $cachePath = storage_path('app/purifier-cache');

            if (! is_dir($cachePath)) {
                mkdir($cachePath, 0755, true);
            }

            $config->set('Cache.SerializerPath', $cachePath);
            $config->set('HTML.Allowed', 'p,strong,em,ul,ol,li,a[href|title],blockquote,code,pre');
            $config->set('Attr.AllowedFrameTargets', ['_blank']);
            $config->set('Attr.AllowedRel', ['noopener', 'noreferrer']);
            $config->set('AutoFormat.RemoveEmpty', true);

            static::$purifier = new HTMLPurifier($config);
        }

        return static::$purifier->purify($value);
    }
}
