<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Tests\Denormalizer;

use Gam6itko\JSCC\Model\AccessorConfig;
use Gam6itko\JSCC\Model\ClassConfig;
use Gam6itko\JSCC\Model\DiscriminatorConfig;
use Gam6itko\JSCC\Model\PropertyConfig;
use Gam6itko\JSCC\Model\VirtualPropertyConfig;
use Gam6itko\JSCC\Model\XmlElementConfig;
use Gam6itko\JSCC\Model\XmlListConfig;
use Gam6itko\JSCC\Model\XmlMapConfig;
use JMS\Serializer\Metadata\PropertyMetadata;
use PHPUnit\Framework\TestCase;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
abstract class AbstractFileDenormalizer extends TestCase
{
    const NAMESPACE_FOLDER = [
        'Gam6itko\JSCC\Tests\Fixtures' => __DIR__.'/../sink',
    ];

    protected function setUp(): void
    {
        foreach (self::NAMESPACE_FOLDER as $ns => $folder) {
            if (!file_exists($folder)) {
                mkdir($folder);
                continue;
            }

            array_map('unlink', array_filter((array) glob("$folder/*")));
        }
    }

    protected function buildConfigAll()
    {
        $config = new ClassConfig('Gam6itko\JSCC\Tests\Fixtures\All');
        $config->exclusionPolicy = 'ALL';
        $config->xmlRootName = 'foobar';
        $config->xmlRootNamespace = 'http://your.default.namespace';
        $config->xmlRootPrefix = 'foo';
        $config->excludeIf = 'expr';
        $config->readOnly = false;
        $config->accessType = 'public_method';
        $config->accessorOrder = 'custom';
        $config->customAccessorOrder = ['propertyName1', 'propertyName2', 'propertyNameN'];
        $config->xmlNamespaces = [
            ''     => 'http://your.default.namespace',
            'atom' => 'http://www.w3.org/2005/Atom',
        ];
        $config->callbackMethods = [
            'preSerialize'    => ['methodOne', 'methodTwo'],
            'postSerialize'   => ['methodOne', 'methodTwo'],
            'postDeserialize' => ['methodOne', 'methodTwo'],
        ];
        $discriminator = new DiscriminatorConfig();
        $discriminator->fieldName = 'type';
        $discriminator->map = [
            'some-value' => 'ClassName',
        ];
        $discriminator->groups = ['foo', 'bar'];
        $discriminator->xmlAttribute = true;
        $xmlElement = new XmlElementConfig();
        $xmlElement->cdata = false;
        $xmlElement->namespace = 'http://www.w3.org/2005/Atom';
        $discriminator->xmlElement = $xmlElement;
        $config->discriminator = $discriminator;
        $vp = new VirtualPropertyConfig();
        $vp->name = 'expression_prop';
        $vp->exp = 'object.getName()';
        $vp->serializedName = 'class-foo';
        $vp->type = 'integer';
        $config->virtualProperties['expression_prop'] = $vp;
        $vp = new VirtualPropertyConfig();
        $vp->name = 'optional-prop-name';
        $vp->method = 'getSomeProperty';
        $vp->serializedName = 'foo';
        $vp->type = 'integer';
        $config->virtualProperties['getSomeProperty'] = $vp;
        $prop = new PropertyConfig();
        $prop->exclude = false;
        $prop->expose = false;
        $prop->name = 'property';
        $prop->excludeIf = 'expr';
        $prop->exposeIf = 'expr';
        $prop->skipWhenEmpty = true;
        $prop->accessType = PropertyMetadata::ACCESS_TYPE_PROPERTY;
        $prop->type = 'string';
        $prop->serializedName = 'property-foo';
        $prop->sinceVersion = '1';
        $prop->untilVersion = '2';
        $prop->groups = ['foo', 'bar'];
        $prop->inline = true;
        $prop->readOnly = true;
        $prop->maxDepth = 2;
        $config->properties['property'] = $prop;
        $prop = new PropertyConfig();
        $prop->name = 'property2';
        $accessor = new AccessorConfig();
        $accessor->getter = 'getProperty2';
        $accessor->setter = 'setProperty2';
        $prop->accessor = $accessor;
        $config->properties['property2'] = $prop;
        $prop = new PropertyConfig();
        $prop->name = 'xmlAttribute';
        $prop->xmlAttribute = true;
        $config->properties['xmlAttribute'] = $prop;
        $prop = new PropertyConfig();
        $prop->name = 'xmlValue';
        $prop->xmlValue = true;
        $config->properties['xmlValue'] = $prop;
        $prop = new PropertyConfig();
        $prop->name = 'xmlKvp';
        $prop->xmlKeyValuePairs = true;
        $config->properties['xmlKvp'] = $prop;
        $prop = new PropertyConfig();
        $prop->name = 'xmlList';
        $xmlList = new XmlListConfig();
        $xmlList->inline = true;
        $xmlList->entryName = 'string';
        $xmlList->namespace = 'http://www.w3.org/2005/Atom';
        $xmlList->skipWhenEmpty = true;
        $prop->xmlList = $xmlList;
        $config->properties['xmlList'] = $prop;
        $prop = new PropertyConfig();
        $prop->name = 'xmlMap';
        $xmlMap = new XmlMapConfig();
        $xmlMap->inline = false;
        $xmlMap->entryName = 'item';
        $xmlMap->keyAttributeName = 'id';
        $xmlMap->namespace = 'http://example.com/namespace2';
        $prop->xmlMap = $xmlMap;
        $config->properties['xmlMap'] = $prop;
        $prop = new PropertyConfig();
        $prop->name = 'xmlAttributeMap';
        $prop->xmlAttributeMap = true;
        $config->properties['xmlAttributeMap'] = $prop;
        $prop = new PropertyConfig();
        $prop->name = 'xmlElement';
        $xmlElement = new XmlElementConfig();
        $xmlElement->cdata = false;
        $xmlElement->namespace = 'http://example.com/namespace5';
        $prop->xmlElement = $xmlElement;
        $config->properties['xmlElement'] = $prop;

        return $config;
    }
}
