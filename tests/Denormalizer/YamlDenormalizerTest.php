<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Tests\Denormalizer;

use Gam6itko\JSCC\Denormalizer\YamlDenormalizer;
use Gam6itko\JSCC\Model\ClassConfig;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
class YamlDenormalizerTest extends AbstractFileDenormalizer
{
    /**
     * @dataProvider dataToString
     */
    public function testToString(ClassConfig $config, string $pathToYaml): void
    {
        $denormalizer = new YamlDenormalizer(self::NAMESPACE_FOLDER);
        $yamlString = $denormalizer->toString($config);

        self::assertEquals(Yaml::parse(file_get_contents($pathToYaml)), Yaml::parse($yamlString));
    }

    public function dataToString()
    {
        yield [
            $this->buildConfigAll(),
            realpath(__DIR__.'/../Resources/Denormalizer/yml/All.yaml'),
        ];
    }

    /**
     * @depends testToString
     * @dataProvider dataDenormalize
     */
    public function testDenormalize(ClassConfig $config, string $compareWithFile, string $createsFile): void
    {
        $denormalizer = new YamlDenormalizer(self::NAMESPACE_FOLDER);
        $denormalizer->denormalize($config);
        self::assertFileExists($createsFile);
        self::assertEquals(Yaml::parse(file_get_contents($compareWithFile)), Yaml::parse(file_get_contents($createsFile)));
    }

    public function dataDenormalize()
    {
        yield [
            $this->buildConfigAll(),
            realpath(__DIR__.'/../Resources/Denormalizer/yml/All.yaml'),
            __DIR__.'/../sink/All.yaml',
        ];
    }
}
