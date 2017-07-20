<?php

// autoload_static.php @generated by Composer

namespace WbsVendors\Composer\Autoload;

class ComposerStaticInit3d2de2f2ace99de36b3d7b2030d830f6
{
    public static $files = array (
        'b411d774a68934fe83360f73e6fe640f' => __DIR__ . '/..' . '/dangoodman/composer-capsule-runtime/autoload.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Wbs\\' => 4,
            'WbsVendors\\Dgm\\WcTools\\' => 23,
            'WbsVendors\\Dgm\\Shengine\\Woocommerce\\Model\\Item\\' => 47,
            'WbsVendors\\Dgm\\Shengine\\Woocommerce\\Converters\\' => 47,
            'WbsVendors\\Dgm\\Shengine\\' => 24,
            'WbsVendors\\Dgm\\Range\\' => 21,
            'WbsVendors\\Dgm\\NumberUnit\\' => 26,
            'WbsVendors\\Dgm\\Comparator\\' => 26,
            'WbsVendors\\BoxPacking\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Wbs\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'WbsVendors\\Dgm\\WcTools\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/wc-tools/src',
        ),
        'WbsVendors\\Dgm\\Shengine\\Woocommerce\\Model\\Item\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/shengine-wc-item/src',
        ),
        'WbsVendors\\Dgm\\Shengine\\Woocommerce\\Converters\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/shengine-wc-converters/src',
        ),
        'WbsVendors\\Dgm\\Shengine\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/shengine/src',
        ),
        'WbsVendors\\Dgm\\Range\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/range/src',
        ),
        'WbsVendors\\Dgm\\NumberUnit\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/number-unit/src',
        ),
        'WbsVendors\\Dgm\\Comparator\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/comparator/src',
        ),
        'WbsVendors\\BoxPacking\\' => 
        array (
            0 => __DIR__ . '/..' . '/dangoodman/boxpacking/src',
        ),
    );

    public static $classMap = array (
        'WbsVendors\\Deferred\\Deferred' => __DIR__ . '/..' . '/dangoodman/deferred/Deferred.php',
        'WbsVendors\\Dgm\\Arrays\\Arrays' => __DIR__ . '/..' . '/dangoodman/arrays/Arrays.php',
        'WbsVendors\\Dgm\\ClassNameAware\\ClassNameAware' => __DIR__ . '/..' . '/dangoodman/class-name-aware/ClassNameAware.php',
        'WbsVendors\\Dgm\\SimpleProperties\\SimpleProperties' => __DIR__ . '/..' . '/dangoodman/simple-properties/SimpleProperties.php',
        'WbsVendors_DgmWpPrerequisitesChecker' => __DIR__ . '/..' . '/dangoodman/wp-prerequisites-check/DgmWpPrerequisitesChecker.php',
    );

    public static function getInitializer(\WbsVendors\Composer\Autoload\ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = \WbsVendors\Composer\Autoload\ComposerStaticInit3d2de2f2ace99de36b3d7b2030d830f6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = \WbsVendors\Composer\Autoload\ComposerStaticInit3d2de2f2ace99de36b3d7b2030d830f6::$prefixDirsPsr4;
            $loader->classMap = \WbsVendors\Composer\Autoload\ComposerStaticInit3d2de2f2ace99de36b3d7b2030d830f6::$classMap;

        }, null, \WbsVendors\Composer\Autoload\ClassLoader::class);
    }
}
