<?php

namespace Gdbots\Tests\Pbj\Fixtures;

use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\MessageRef;
use Gdbots\Pbj\MessageResolver;
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
            $type = $file->getBasename('Type.php');
            if (!$file->isFile()
                || in_array(
                    $type,
                    ['AbstractInt', 'AbstractBinary', 'AbstractString', 'Abstract', 'Type.php', '..', '.']
                )
            ) {
                continue;
            }
            $types[$type] = 'Gdbots\Pbj\Type\\' . $type . 'Type';
        }
        return $types;
    }

    /**
     * {@inheritdoc}
     */
    public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), null, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function getUriTemplateVars()
    {
        return [];
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
                case 'Identifier':
                    $fields[] = Fb::create($type, $class::create())
                        ->asAMap()
                        ->className('Gdbots\Pbj\WellKnown\TimeUuidIdentifier')
                        ->build();
                    break;

                case 'IntEnum':
                    $fields[] = Fb::create($type, $class::create())
                        ->asAMap()
                        ->className('Gdbots\Tests\Pbj\Fixtures\Enum\IntEnum')
                        ->build();
                    break;

                case 'StringEnum':
                    $fields[] = Fb::create($type, $class::create())
                        ->asAMap()
                        ->className('Gdbots\Tests\Pbj\Fixtures\Enum\StringEnum')
                        ->build();
                    break;

                case 'Message':
                    $fields[] = Fb::create($type, $class::create())
                        ->asAMap()
                        ->className('Gdbots\Tests\Pbj\Fixtures\NestedMessage')
                        ->build();
                    break;

                default:
                    $fields[] = Fb::create($type, $class::create())
                        ->asAMap()
                        ->build();
            }
        }

        $schema = new Schema('pbj:gdbots:tests.pbj:fixtures:maps-message:1-0-0', __CLASS__, $fields);
        MessageResolver::registerSchema($schema);
        return $schema;
    }
}
