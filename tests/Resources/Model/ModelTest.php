<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Tests\Resources\Model;

use Gam6itko\JSCC\Model\ClassConfig;
use Gam6itko\JSCC\Tests\ModelRepository;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function testSerializable()
    {
        $config = ModelRepository::getAll();
        $serialized = $config->serialize();
        $newConfig = new ClassConfig('');
        $newConfig->unserialize($serialized);
        self::assertEquals($config, $newConfig);
    }
}
