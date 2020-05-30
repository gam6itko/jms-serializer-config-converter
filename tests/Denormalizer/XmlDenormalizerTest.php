<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Tests\Denormalizer;

use Gam6itko\JSCC\Denormalizer\XmlDenormalizer;
use Gam6itko\JSCC\Model\ClassConfig;
use Gam6itko\JSCC\Tests\Fixtures\All;
use Gam6itko\JSCC\Tests\ModelRepository;

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
            ModelRepository::getAll(),
            realpath(__DIR__.'/../Resources/Denormalizer/xml/All.xml'),
        ];
    }

    /**
     * @depends      testToString
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
            ModelRepository::getAll(),
            realpath(__DIR__.'/../Resources/Denormalizer/xml/All.xml'),
            __DIR__.'/../sink/All.xml',
        ];
    }

    public function testNoNamespaces()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('You must define at least one namespace folder');

        $denormalizer = new XmlDenormalizer([]);
        $denormalizer->denormalize(new ClassConfig(''));
    }

    public function testConfigNoName()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Config class name not defined');

        $denormalizer = new XmlDenormalizer(self::NAMESPACE_FOLDER);
        $denormalizer->denormalize(new ClassConfig(''));
    }

    public function testNoOverwrite()
    {
        $this->expectException(\RuntimeException::class);

        $denormalizer = new XmlDenormalizer([
            'Gam6itko\JSCC\Tests\Fixtures' => __DIR__.'/../Resources/Normalizer/xml',
        ], false);
        $denormalizer->denormalize(new ClassConfig(All::class));
    }

    /**
     * @dataProvider dataDenormalize
     */
    public function testOverwrite(ClassConfig $config, string $compareWithFile, string $createsFile)
    {
        $oldFile = "$createsFile~";
        self::assertFileDoesNotExist($oldFile);

        try {
            touch($createsFile);

            $denormalizer = new XmlDenormalizer(self::NAMESPACE_FOLDER, true);
            $denormalizer->denormalize($config);
            self::assertFileExists($oldFile);
            self::assertFileExists($createsFile);
            self::assertXmlFileEqualsXmlFile($compareWithFile, $createsFile);
        } finally {
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
    }
}
