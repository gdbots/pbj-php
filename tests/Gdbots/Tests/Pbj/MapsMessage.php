<?php

namespace Gdbots\Tests\Pbj;

use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Type as T;

final class MapsMessage extends AbstractMessage
{
    /**
     * @return array
     */
    public static function getAllTypes()
    {
        $reflector = new \ReflectionClass('Gdbots\Pbj\Type\Type');
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(dirname($reflector->getFileName())));
        $types = [];
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $type = $file->getBasename('.php');
            if (!$file->isFile() || in_array($type, ['AbstractInt', 'AbstractType', 'Type', '..', '.'])) {
                continue;
            }
            $types[$type] = 'Gdbots\Pbj\Type\\' . $type;
        }

        return $types;
    }

    /**
     * @return Schema
     */
    protected static function defineSchema()
    {
        $fields = [];

        /** @var T\Type $class */
        foreach (self::getAllTypes() as $type => $class) {
            switch ($type) {
                case 'IntEnum':
                    $fields[] = Fb::create($type, $class::create())
                        ->asAMap()
                        ->usingClass('Gdbots\Tests\Pbj\Enum\IntEnum')
                        ->build();
                    break;

                case 'StringEnum':
                    $fields[] = Fb::create($type, $class::create())
                        ->asAMap()
                        ->usingClass('Gdbots\Tests\Pbj\Enum\StringEnum')
                        ->build();
                    break;

                default:
                    $fields[] = Fb::create($type, $class::create())->asAMap()->build();
            }
        }

        return Schema::create(__CLASS__, '1-0-0', $fields);
    }

    /**
     * @param string $name
     * @return array
     */
    public function getAMap($name)
    {
        return $this->get($name) ?: [];
    }

    /**
     * @param string $name
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function addToAMap($name, $key, $value)
    {
        return $this->addToMap($name, $key, $value);
    }

    /**
     * @param string $name
     * @param string $key
     * @return static
     */
    public function removeFromAMap($name, $key)
    {
        return $this->removeFromMap($name, $key);
    }
}