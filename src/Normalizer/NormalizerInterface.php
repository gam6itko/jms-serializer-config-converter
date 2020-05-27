<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Normalizer;

use Gam6itko\JSCC\Model\ClassConfig;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
interface NormalizerInterface
{
    public function normalize(\ReflectionClass $class): ?ClassConfig;
}
