<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Util;

final class ClassUtil
{
    /**
     * Keeps a static reference of all requests for a classes traits.
     * The array key is the fully qualified class name and a flag for
     * whether or not to do a deep scan (inherited classes) and autoload.
     *
     * @var array
     */
    private static array $classTraits = [];

    /**
     * Private constructor. This class is not meant to be instantiated.
     */
    private function __construct()
    {
    }

    /**
     * Returns an array of all the traits that a class is using.  This
     * includes all of the extended classes and traits by default.
     *
     * Stores the traits as ['MyTrait' => 1, 'MyOtherTrait' => 2]
     * for optimal checking on the usesTrait method.
     *
     * @param string|object $class
     * @param bool          $deep
     * @param bool          $autoload
     *
     * @return array
     */
    private static function loadTraits($class, bool $deep = true, bool $autoload = true): array
    {
        $cacheKey = is_object($class) ? get_class($class) : (string)$class;
        $cacheKey .= $deep ? ':deep' : '';
        $cacheKey .= $autoload ? ':autoload' : '';

        if (isset(self::$classTraits[$cacheKey])) {
            return self::$classTraits[$cacheKey];
        }

        $traits = class_uses($class, $autoload);

        if (false === $deep) {
            self::$classTraits[$cacheKey] = array_flip($traits);
            return $traits;
        }

        foreach (class_parents($class) as $parent) {
            $traits = array_merge(class_uses($parent, $autoload), $traits);
        }

        foreach ($traits as $trait) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        self::$classTraits[$cacheKey] = array_flip(array_unique($traits));
        return self::$classTraits[$cacheKey];
    }

    /**
     * Returns an array of all the traits that a class is using.  This
     * includes all of the extended classes and traits by default.
     *
     * @param string|object $class
     * @param bool          $deep
     * @param bool          $autoload
     *
     * @return array
     */
    public static function getTraits($class, bool $deep = true, bool $autoload = true): array
    {
        return array_keys(self::loadTraits($class, $deep, $autoload));
    }

    /**
     * Returns true if a class uses a given trait.
     *
     * @param string|object $class
     * @param string        $trait full qualified class name
     *
     * @return bool
     */
    public static function usesTrait($class, string $trait): bool
    {
        return isset(self::loadTraits($class)[$trait]);
    }

    /**
     * Returns the class name of an object, without the namespace
     *
     * @param object|string $objectOrString
     *
     * @return string
     */
    public static function getShortName($objectOrString): string
    {
        $parts = explode('\\', is_object($objectOrString) ? get_class($objectOrString) : $objectOrString);
        return end($parts);
    }

    /**
     * Returns an array of CONSTANT_NAME => value for a given class
     *
     * @param string $className
     *
     * @return array
     */
    public static function getConstants(string $className): array
    {
        return (new \ReflectionClass($className))->getConstants();
    }
}
