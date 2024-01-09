<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2a30686aac843215c02097c468fe5b6e
{
    public static $files = array (
        'e40631d46120a9c38ea139981f8dab26' => __DIR__ . '/..' . '/ircmaxell/password-compat/lib/password.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2a30686aac843215c02097c468fe5b6e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2a30686aac843215c02097c468fe5b6e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2a30686aac843215c02097c468fe5b6e::$classMap;

        }, null, ClassLoader::class);
    }
}
