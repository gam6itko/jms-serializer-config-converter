<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Converter;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
interface ConverterInterface
{
    /**
     * @param string $from Converts from Format
     * @param string $to   Converts to Format
     *
     * @return mixed
     */
    public function convert(\ReflectionClass $class, string $from, string $to);
}
