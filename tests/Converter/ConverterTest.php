<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Tests\Converter;

use Gam6itko\JSCC\Converter\Converter;
use Gam6itko\JSCC\Denormalizer\DenormalizerInterface;
use Gam6itko\JSCC\Model\ClassConfig;
use Gam6itko\JSCC\Normalizer\NormalizerInterface;
use Gam6itko\JSCC\Tests\Fixtures\All;
use PHPUnit\Framework\TestCase;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
class ConverterTest extends TestCase
{
    public function testConvert(): void
    {
        $config = new ClassConfig(All::class);

        $xmlNormalizer = $this->createMock(NormalizerInterface::class);
        $xmlNormalizer
            ->expects(self::once())
            ->method('normalize')
            ->willReturn($config);

        $yamlDenormalizer = $this->createMock(DenormalizerInterface::class);
        $yamlDenormalizer
            ->expects(self::once())
            ->method('denormalize');

        $converter = new Converter(
            [
                'xml' => $xmlNormalizer,
            ],
            [
                'yaml' => $yamlDenormalizer,
            ]
        );

        $refClass = new \ReflectionClass(All::class);
        $converter->convert($refClass, 'xml', 'yaml');
    }

    /**
     * @dataProvider dataNotFound
     */
    public function testNotFound(string $message, array $normalizers, array $denormalizers): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage($message);

        $converter = new Converter($normalizers, $denormalizers);
        $refClass = new \ReflectionClass(All::class);
        $converter->convert($refClass, 'xml', 'yaml');
    }

    public function dataNotFound()
    {
        yield [
            'Normalizer for type `xml` not found',
            [],
            [],
        ];

        yield [
            'Denormalizer for type `xml` not found',
            ['xml' => $this->createMock(NormalizerInterface::class)],
            [],
        ];
    }
}
