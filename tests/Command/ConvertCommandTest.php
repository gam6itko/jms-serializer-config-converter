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
        $converter = $this->createMock(ConverterInterface::class);
        $converter
            ->expects(self::atLeastOnce())
            ->method('convert');

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
        self::assertTrue(true);
    }
}
