<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfce7af38855ebe167f6bfcdac84cdd1b
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '6a47392539ca2329373e0d33e1dba053' => __DIR__ . '/..' . '/symfony/polyfill-intl-icu/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Twig\\Extra\\Intl\\' => 16,
            'Twig\\' => 5,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Symfony\\Component\\Intl\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Twig\\Extra\\Intl\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/intl-extra/src',
        ),
        'Twig\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/twig/src',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Symfony\\Component\\Intl\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/intl',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Twig_' => 
            array (
                0 => __DIR__ . '/..' . '/twig/twig/lib',
            ),
        ),
    );

    public static $classMap = array (
        'Collator' => __DIR__ . '/..' . '/symfony/intl/Resources/stubs/Collator.php',
        'IntlDateFormatter' => __DIR__ . '/..' . '/symfony/intl/Resources/stubs/IntlDateFormatter.php',
        'Locale' => __DIR__ . '/..' . '/symfony/intl/Resources/stubs/Locale.php',
        'NumberFormatter' => __DIR__ . '/..' . '/symfony/intl/Resources/stubs/NumberFormatter.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfce7af38855ebe167f6bfcdac84cdd1b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfce7af38855ebe167f6bfcdac84cdd1b::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitfce7af38855ebe167f6bfcdac84cdd1b::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitfce7af38855ebe167f6bfcdac84cdd1b::$classMap;

        }, null, ClassLoader::class);
    }
}
