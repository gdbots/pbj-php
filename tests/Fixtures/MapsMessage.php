<?php

namespace Gdbots\Tests\Pbj\Fixtures;

use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Type as T;
use Gdbots\Pbj\WellKnown\TimeUuidIdentifier;
use Gdbots\Tests\Pbj\Fixtures\Enum\IntEnum;
use Gdbots\Tests\Pbj\Fixtures\Enum\StringEnum;

final class MapsMessage extends AbstractMessage
{
    public static function getAllTypes(): array
    {
        $reflector = new \ReflectionClass(T\Type::class);
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

    protected static function defineSchema(): Schema
    {
        $fields = [];

        /** @var T\Type $class */
        foreach (self::getAllTypes() as $type => $class) {
            switch ($type) {
                case 'Identifier':
                    $fields[] = Fb::create($type, $class::create())
                        ->asAMap()
                        ->className(TimeUuidIdentifier::class)
                        ->build();
                    break;

                case 'IntEnum':
                    $fields[] = Fb::create($type, $class::create())
                        ->asAMap()
                        ->className(IntEnum::class)
                        ->build();
                    break;

                case 'StringEnum':
                    $fields[] = Fb::create($type, $class::create())
                        ->asAMap()
                        ->className(StringEnum::class)
                        ->build();
                    break;

                case 'Message':
                    $fields[] = Fb::create($type, $class::create())
                        ->asAMap()
                        ->anyOfClassNames([
                            NestedMessage::class,
                        ])
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
