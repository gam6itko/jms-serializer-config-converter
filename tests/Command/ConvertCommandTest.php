<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Tests\Command;

use Gam6itko\JSCC\Command\ConvertCommand;
use Gam6itko\JSCC\Converter\ConverterInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertCommandTest extends TestCase
{
    public function test()
    {
        $shouldConvertClasses = [
            'Gam6itko\\JSCC\\Tests\\Fixtures\\All',
            'Gam6itko\\JSCC\\Tests\\Fixtures\\Attribute',
            'Gam6itko\\JSCC\\Tests\\Fixtures\\Author',
            'Gam6itko\\JSCC\\Tests\\Fixtures\\AuthorExpressionAccess',
            'Gam6itko\\JSCC\\Tests\\Fixtures\\Discriminator\\ImagePost',
            'Gam6itko\\JSCC\\Tests\\Fixtures\\Discriminator\\Moped',
            'Gam6itko\\JSCC\\Tests\\Fixtures\\Discriminator\\Post',
            'Gam6itko\\JSCC\\Tests\\Fixtures\\Discriminator\\Vehicle',
            'Gam6itko\\JSCC\\Tests\\Fixtures\\ObjectWithVirtualPropertiesAndDuplicatePropName',
        ];
        $converter = $this->createMock(ConverterInterface::class);
        $converter
            ->expects(self::atLeastOnce())
            ->method('convert')
            ->willReturnCallback(static function (\ReflectionClass $class, string $fromFormat, string $toFormat) use (&$shouldConvertClasses) {
                self::assertNotFalse($key = array_search($class->name, $shouldConvertClasses));
                unset($shouldConvertClasses[$key]);
            });

        $input = $this->createMock(InputInterface::class);
        $input
            ->method('getArgument')
            ->willReturnMap([
                ['namespace', 'Gam6itko\\JSCC\\Tests\\Fixtures\\'],
                ['from-format', 'annotation'],
                ['to-format', 'yaml'],
            ]);

        $output = $this->createMock(OutputInterface::class);
        $command = new ConvertCommand($converter);
        $command->run($input, $output);
        self::assertEmpty($shouldConvertClasses);
    }
}
