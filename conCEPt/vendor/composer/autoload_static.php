<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit69c39a7b514063208f45cb1d751f7c2f
{
    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'Klein\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Klein\\' => 
        array (
            0 => __DIR__ . '/..' . '/klein/klein/src/Klein',
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

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit69c39a7b514063208f45cb1d751f7c2f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit69c39a7b514063208f45cb1d751f7c2f::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit69c39a7b514063208f45cb1d751f7c2f::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}