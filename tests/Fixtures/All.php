<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("ALL")
 * @Serializer\XmlRoot(name="foobar", namespace="http://your.default.namespace", prefix="foo")
 * @Serializer\Exclude(if="expr")
 * @Serializer\ReadOnly(readOnly=false)
 * @Serializer\AccessType(type="public_method")
 * @Serializer\AccessorOrder(order="custom", custom={"propertyName1", "propertyName2", "propertyNameN"})
 * @Serializer\XmlNamespace(uri="http://your.default.namespace")
 * @Serializer\XmlNamespace(uri="http://www.w3.org/2005/Atom", prefix="atom")
 * @Serializer\Discriminator(
 *     field="type",
 *     map={"some-value":"ClassName"},
 *     groups={"foo", "bar"}
 * )
 * @Serializer\XmlDiscriminator(attribute=true, cdata=false, namespace="http://www.w3.org/2005/Atom")
 * @Serializer\VirtualProperty(name="expression_prop", exp="object.getName()", options={@Serializer\SerializedName("class-foo"), @Serializer\Type("integer")})
 */
class All
{
    /**
     * @Serializer\Exclude(if="expr")
     * @Serializer\Expose(if="expr")
     * @Serializer\SkipWhenEmpty()
     * @Serializer\AccessType(type=JMS\Serializer\Metadata\PropertyMetadata::ACCESS_TYPE_PROPERTY)
     * @Serializer\Type("string")
     * @Serializer\SerializedName("property-foo")
     * @Serializer\Since("1")
     * @Serializer\Until("2")
     * @Serializer\Groups({"foo", "bar"})
     * @Serializer\Inline()
     * @Serializer\ReadOnly()
     * @Serializer\MaxDepth(2)
     */
    public $property;

    /**
     * @Serializer\Accessor(getter="getProperty2", setter="setProperty2")
     */
    private $property2;

    /**
     * @Serializer\XmlAttribute()
     */
    public $xmlAttribute;

    /**
     * @Serializer\XmlValue()
     */
    public $xmlValue;

    /**
     * @Serializer\XmlKeyValuePairs()
     */
    public $xmlKvp = [
        'key' => 'val',
    ];

    /**
     * @Serializer\XmlList(inline=true, entry="string", namespace="http://www.w3.org/2005/Atom")
     */
    public $xmlList;

    /**
     * @Serializer\XmlMap(inline=false, entry="item", keyAttribute="id", namespace="http://example.com/namespace2")
     */
    public $xmlMap;

    /**
     * @Serializer\XmlAttributeMap()
     */
    public $xmlAttributeMap;

    /**
     * @Serializer\XmlElement(cdata=false, namespace="http://example.com/namespace5")
     */
    public $xmlElement;

    public function getProperty2()
    {
        return $this->property2;
    }

    public function setProperty2($val)
    {
        $this->property2 = $val;
    }

    /**
     * @Serializer\VirtualProperty(name="optional-prop-name")
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("foo")
     */
    public function getSomeProperty()
    {
        return 'some_value';
    }

    /**
     * @Serializer\PreSerialize()
     * @Serializer\PostSerialize()
     * @Serializer\PostDeserialize()
     */
    public function methodOne()
    {
    }

    /**
     * @Serializer\PreSerialize()
     * @Serializer\PostSerialize()
     * @Serializer\PostDeserialize()
     */
    public function methodTwo()
    {
    }
}
