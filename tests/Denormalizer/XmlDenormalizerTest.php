<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Tests\Denormalizer;

use Gam6itko\JSCC\Denormalizer\XmlDenormalizer;
use Gam6itko\JSCC\Model\ClassConfig;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
class XmlDenormalizerTest extends AbstractFileDenormalizer
{
    /**
     * @dataProvider dataToString
     */
    public function testToString(ClassConfig $config, string $pathToXml): void
    {
        $denormalizer = new XmlDenormalizer(self::NAMESPACE_FOLDER);
        $xmlString = $denormalizer->toString($config);
        self::assertXmlStringEqualsXmlFile($pathToXml, $xmlString);
    }

    public function dataToString()
    {
        yield [
            $this->buildConfigAll(),
            realpath(__DIR__.'/../Resources/Denormalizer/xml/All.xml'),
        ];
    }

    /**
     * @depends testToString
     * @dataProvider dataDenormalize
     */
    public function testDenormalize(ClassConfig $config, string $compareWithFile, string $createsFile): void
    {
        $denormalizer = new XmlDenormalizer(self::NAMESPACE_FOLDER);
        $denormalizer->denormalize($config);
        self::assertFileExists($createsFile);
        self::assertXmlFileEqualsXmlFile($compareWithFile, $createsFile);
    }

    public function dataDenormalize()
    {
        yield [
            $this->buildConfigAll(),
            realpath(__DIR__.'/../Resources/Denormalizer/xml/All.xml'),
            __DIR__.'/../sink/All.xml',
        ];
    }
}
