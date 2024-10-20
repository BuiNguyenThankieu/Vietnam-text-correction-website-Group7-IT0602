<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf4cbb8d8b25a2245796f3746e311e3c8
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PhpOffice\\PhpWord\\' => 18,
            'PhpOffice\\Math\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PhpOffice\\PhpWord\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/phpword/src/PhpWord',
        ),
        'PhpOffice\\Math\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/math/src/Math',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf4cbb8d8b25a2245796f3746e311e3c8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf4cbb8d8b25a2245796f3746e311e3c8::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf4cbb8d8b25a2245796f3746e311e3c8::$classMap;

        }, null, ClassLoader::class);
    }
}
