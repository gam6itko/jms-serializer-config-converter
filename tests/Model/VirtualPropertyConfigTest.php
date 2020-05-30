<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Tests\Model;

use Gam6itko\JSCC\Model\VirtualPropertyConfig;
use Gam6itko\JSCC\Model\XmlMapConfig;
use PHPUnit\Framework\TestCase;

class VirtualPropertyConfigTest extends TestCase
{
    public function testSerialize()
    {
        $vp = new VirtualPropertyConfig();
        $vp->method = 'getName';
        $vp->exp = 'object.getName()';
        $vp->name = 'name';
        $vp->type = 'string';
        $vp->serializedName = 'serializedName';
        $vp->sinceVersion = '1';
        $vp->untilVersion = '2';
        $vp->skipWhenEmpty = true;
        $vp->groups = ['foo', 'bar'];
        $vp->xmlAttribute = true;
        $vp->xmlValue = false;
        $vp->xmlMap = new XmlMapConfig();
        $vp->xmlMap->entryName = 'map';
        $vp->xmlList = new XmlMapConfig();
        $vp->xmlList->entryName = 'list';

        $vp2 = new VirtualPropertyConfig();
        $vp2->unserialize($vp->serialize());
        self::assertEquals($vp, $vp2);
        self::assertSame('getName', $vp2->method);
        self::assertSame('object.getName()', $vp2->exp);
        self::assertSame('name', $vp2->name);
        self::assertSame('string', $vp2->type);
        self::assertSame('serializedName', $vp2->serializedName);
        self::assertSame('1', $vp2->sinceVersion);
        self::assertSame('2', $vp2->untilVersion);
        self::assertTrue($vp2->skipWhenEmpty);
        self::assertSame(['foo', 'bar'], $vp2->groups);
        self::assertTrue($vp2->xmlAttribute);
        self::assertFalse($vp2->xmlValue);
        self::assertSame('list', $vp2->xmlList->entryName);
        self::assertSame('map', $vp2->xmlMap->entryName);
    }
}
