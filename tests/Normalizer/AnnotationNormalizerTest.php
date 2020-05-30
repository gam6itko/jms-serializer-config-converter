<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Tests\Normalizer;

use Gam6itko\JSCC\Model\AccessorConfig;
use Gam6itko\JSCC\Model\ClassConfig;
use Gam6itko\JSCC\Model\DiscriminatorConfig;
use Gam6itko\JSCC\Model\PropertyConfig;
use Gam6itko\JSCC\Model\VirtualPropertyConfig;
use Gam6itko\JSCC\Model\XmlElementConfig;
use Gam6itko\JSCC\Model\XmlListConfig;
use Gam6itko\JSCC\Model\XmlMapConfig;
use Gam6itko\JSCC\Normalizer\AnnotationNormalizer;
use Gam6itko\JSCC\Tests\Fixtures\Discriminator\Vehicle;
use JMS\Serializer\Metadata\PropertyMetadata;
use PHPUnit\Framework\TestCase;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
class AnnotationNormalizerTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testLoad(string $fqcn, ClassConfig $expected): void
    {
        $normalizer = new AnnotationNormalizer();
        $reflectionClass = new \ReflectionClass($fqcn);
        $config = $normalizer->normalize($reflectionClass);
        self::assertNotEmpty($config);
        self::assertInstanceOf(ClassConfig::class, $config);
        self::assertEquals($expected, $config);
        self::assertEquals($expected->serialize(), $config->serialize());
    }

    public function dataProvider()
    {
        //<editor-fold desc="case">
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
        $vp->method = 'getSomeProperty';
        $vp->name = 'optional-prop-name';
        $vp->serializedName = 'foo';
        $vp->type = 'integer';
        $config->virtualProperties['getSomeProperty'] = $vp;
        $prop = new PropertyConfig();
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
        yield [
            'Gam6itko\JSCC\Tests\Fixtures\All',
            $config,
        ];
        //</editor-fold>

        //<editor-fold desc="case">
        $config = new ClassConfig('Gam6itko\JSCC\Tests\Fixtures\AuthorExpressionAccess');
        $vp = new VirtualPropertyConfig();
        $vp->name = 'firstName';
        $vp->exp = 'object.getFirstName()';
        $vp->serializedName = 'my_first_name';
        $config->virtualProperties['firstName'] = $vp;
        $vp = new VirtualPropertyConfig();
        $vp->name = 'lastName';
        $vp->method = 'getLastName';
        $config->virtualProperties['getLastName'] = $vp;
        $prop = new PropertyConfig();
        $prop->name = 'id';
        $config->properties['id'] = $prop;
        $prop = new PropertyConfig();
        $prop->name = 'firstName';
        $prop->exclude = true;
        $config->properties['firstName'] = $prop;
        $prop = new PropertyConfig();
        $prop->name = 'lastName';
        $prop->exclude = true;
        $config->properties['lastName'] = $prop;
        yield [
            'Gam6itko\JSCC\Tests\Fixtures\AuthorExpressionAccess',
            $config,
        ];
        //</editor-fold>
    }

    public function testAbstractClass()
    {
        $normalizer = new AnnotationNormalizer();
        $reflectionClass = new \ReflectionClass(Vehicle::class);
        $config = $normalizer->normalize($reflectionClass);
        self::assertNull($config);
    }
}
