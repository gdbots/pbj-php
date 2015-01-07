<?php

namespace Gdbots\Tests\Messages;

use Gdbots\Messages\AbstractMessage;
use Gdbots\Messages\Field;
use Gdbots\Messages\FieldBuilder as Fb;
use Gdbots\Messages\Type as T;

class MapsMessage extends AbstractMessage
{
    /**
     * @return array
     */
    public static function getAllTypes()
    {
        $reflector = new \ReflectionClass('Gdbots\Messages\Type\Type');
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(dirname($reflector->getFileName())));
        $types = [];
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $type = $file->getBasename('Type.php');
            if (!$file->isFile() || in_array($type, ['AbstractInt', 'Abstract', 'Type.php', '..', '.'])) {
                continue;
            }
            $types[$type] = 'Gdbots\Messages\Type\\' . $type . 'Type';
        }

        return $types;
    }

    /**
     * @return Field[]
     */
    protected static function getFields()
    {
        $fields = [];

        /** @var T\Type $class */
        foreach (self::getAllTypes() as $type => $class) {
            switch ($type) {
                case 'IntEnum':
                    $fields[] = Fb::create($type, $class::create())
                        ->asAMap()
                        ->usingClass('Gdbots\Tests\Messages\Enum\IntEnum')
                        ->build();
                    break;

                case 'StringEnum':
                    $fields[] = Fb::create($type, $class::create())
                        ->asAMap()
                        ->usingClass('Gdbots\Tests\Messages\Enum\StringEnum')
                        ->build();
                    break;

                default:
                    $fields[] = Fb::create($type, $class::create())->asAMap()->build();
            }
        }

        return $fields;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getAMap($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function addToAMap($name, $key, $value)
    {
        return $this->addValueToMap($name, $key, $value);
    }

    /**
     * @param string $name
     * @param string $key
     * @return static
     */
    public function removeFromAMap($name, $key)
    {
        return $this->removeValueFromMap($name, $key);
    }
}