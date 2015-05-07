<?php

namespace Gdbots\Tests\Pbj\Marshaler\DynamoDb;

use Gdbots\Pbj\Marshaler\DynamoDb\ItemMarshaler;
use Gdbots\Tests\Pbj\FixtureLoader;

class ItemMarshalerTest extends \PHPUnit_Framework_TestCase
{
    use FixtureLoader;

    /** @var ItemMarshaler */
    protected $marshaler;

    public function setup()
    {
        $this->marshaler = new ItemMarshaler();
    }

    public function testMarshal()
    {
        $message = $this->createEmailMessage();
        $expected = '{"_schema":{"S":"pbj:gdbots:tests.pbj:fixtures:email-message:1-0-0"},"id":{"S":"0dcee564-aa71-11e4-a811-3c15c2c60168"},"from_name":{"S":"homer"},"from_email":{"S":"homer@thesimpsons.com"},"subject":{"S":"donuts,mmmm,chicken test"},"priority":{"N":"2"},"sent":{"BOOL":false},"date_sent":{"S":"2014-12-25T12:12:00.123456+00:00"},"microtime_sent":{"N":"1422122017734617"},"provider":{"S":"gmail"},"labels":{"SS":["donuts","mmmm","chicken"]},"nested":{"M":{"_schema":{"S":"pbj:gdbots:tests.pbj:fixtures:nested-message:1-0-0"},"test1":{"S":"val1"},"test2":{"NS":["1","2"]},"location":{"M":{"type":{"S":"Point"},"coordinates":{"L":[{"N":"102"},{"N":"0.5"}]}}},"refs":{"L":[{"M":{"curie":{"S":"gdbots:tests.pbj:fixtures:email-message"},"id":{"S":"0dcee564-aa71-11e4-a811-3c15c2c60168"},"tag":{"S":"parent"}}}]}}},"enum_in_set":{"SS":["aol","gmail"]},"enum_in_list":{"L":[{"S":"aol"},{"S":"aol"},{"S":"gmail"},{"S":"gmail"}]},"any_of_message":{"L":[{"M":{"_schema":{"S":"pbj:gdbots:tests.pbj:fixtures:maps-message:1-0-0"},"String":{"M":{"test:field:name":{"S":"value1"}}}}},{"M":{"_schema":{"S":"pbj:gdbots:tests.pbj:fixtures:nested-message:1-0-0"},"test1":{"S":"value1"}}}]}}';
        $actual = $this->marshaler->marshal($message);
        $this->assertSame($expected, json_encode($actual));
    }

    public function testUnmarshal()
    {
        $expected = $this->createEmailMessage();
        $item = unserialize('a:15:{s:7:"subject";a:1:{s:1:"S";s:24:"donuts,mmmm,chicken test";}s:9:"date_sent";a:1:{s:1:"S";s:32:"2014-12-25T12:12:00.123456+00:00";}s:14:"any_of_message";a:1:{s:1:"L";a:2:{i:0;a:1:{s:1:"M";a:2:{s:7:"_schema";a:1:{s:1:"S";s:48:"pbj:gdbots:tests.pbj:fixtures:maps-message:1-0-0";}s:6:"String";a:1:{s:1:"M";a:1:{s:15:"test:field:name";a:1:{s:1:"S";s:6:"value1";}}}}}i:1;a:1:{s:1:"M";a:2:{s:5:"test1";a:1:{s:1:"S";s:6:"value1";}s:7:"_schema";a:1:{s:1:"S";s:50:"pbj:gdbots:tests.pbj:fixtures:nested-message:1-0-0";}}}}}s:6:"nested";a:1:{s:1:"M";a:5:{s:5:"test1";a:1:{s:1:"S";s:4:"val1";}s:7:"_schema";a:1:{s:1:"S";s:50:"pbj:gdbots:tests.pbj:fixtures:nested-message:1-0-0";}s:4:"refs";a:1:{s:1:"L";a:1:{i:0;a:1:{s:1:"M";a:3:{s:2:"id";a:1:{s:1:"S";s:36:"0dcee564-aa71-11e4-a811-3c15c2c60168";}s:3:"tag";a:1:{s:1:"S";s:6:"parent";}s:5:"curie";a:1:{s:1:"S";s:39:"gdbots:tests.pbj:fixtures:email-message";}}}}}s:8:"location";a:1:{s:1:"M";a:2:{s:4:"type";a:1:{s:1:"S";s:5:"Point";}s:11:"coordinates";a:1:{s:1:"L";a:2:{i:0;a:1:{s:1:"N";s:3:"102";}i:1;a:1:{s:1:"N";s:3:"0.5";}}}}}s:5:"test2";a:1:{s:2:"NS";a:2:{i:0;s:1:"1";i:1;s:1:"2";}}}}s:6:"labels";a:1:{s:2:"SS";a:3:{i:0;s:6:"donuts";i:1;s:4:"mmmm";i:2;s:7:"chicken";}}s:11:"enum_in_set";a:1:{s:2:"SS";a:2:{i:0;s:3:"aol";i:1;s:5:"gmail";}}s:12:"enum_in_list";a:1:{s:1:"L";a:4:{i:0;a:1:{s:1:"S";s:3:"aol";}i:1;a:1:{s:1:"S";s:3:"aol";}i:2;a:1:{s:1:"S";s:5:"gmail";}i:3;a:1:{s:1:"S";s:5:"gmail";}}}s:8:"priority";a:1:{s:1:"N";s:1:"2";}s:2:"id";a:1:{s:1:"S";s:36:"0dcee564-aa71-11e4-a811-3c15c2c60168";}s:8:"provider";a:1:{s:1:"S";s:5:"gmail";}s:7:"_schema";a:1:{s:1:"S";s:49:"pbj:gdbots:tests.pbj:fixtures:email-message:1-0-0";}s:4:"sent";a:1:{s:4:"BOOL";b:0;}s:10:"from_email";a:1:{s:1:"S";s:21:"homer@thesimpsons.com";}s:14:"microtime_sent";a:1:{s:1:"N";s:16:"1422122017734617";}s:9:"from_name";a:1:{s:1:"S";s:5:"homer";}}');
        $actual = $this->marshaler->unmarshal($item);
        $this->assertTrue($expected->equals($actual));
    }
}
