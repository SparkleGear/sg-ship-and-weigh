<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite570aca46fcf1a059870d616bd8d526b
{
    public static $prefixesPsr0 = array (
        'E' => 
        array (
            'EasyPost' => 
            array (
                0 => __DIR__ . '/..' . '/easypost/easypost-php/lib',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInite570aca46fcf1a059870d616bd8d526b::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
