<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Denormalizer;

use Gam6itko\JSCC\Model\ClassConfig;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
interface DenormalizerInterface
{
    public function denormalize(ClassConfig $config);
}
