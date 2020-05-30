<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class Attribute
{
    /**
     * @Serializer\VirtualProperty()
     * @Serializer\XmlAttribute()
     */
    public function getAttribute()
    {
        return 'attribute';
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\XmlValue()
     */
    public function getValue()
    {
        return 'value';
    }
}