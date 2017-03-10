<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbbeb0cc843a73eef65447e1cb1f9f47c
{
    public static $files = array (
        '2c102faa651ef8ea5874edb585946bce' => __DIR__ . '/..' . '/swiftmailer/swiftmailer/lib/swift_required.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Component\\Process\\' => 26,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Component\\Process\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/process',
        ),
    );

    public static $prefixesPsr0 = array (
        'K' => 
        array (
            'Knp\\Snappy' => 
            array (
                0 => __DIR__ . '/..' . '/knplabs/knp-snappy/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbbeb0cc843a73eef65447e1cb1f9f47c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbbeb0cc843a73eef65447e1cb1f9f47c::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitbbeb0cc843a73eef65447e1cb1f9f47c::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}