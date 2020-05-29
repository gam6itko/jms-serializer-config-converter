<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Converter;

use Gam6itko\JSCC\Denormalizer\DenormalizerInterface;
use Gam6itko\JSCC\Normalizer\NormalizerInterface;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
class Converter implements ConverterInterface
{
    /**
     * @var array<string, NormalizerInterface> Key must be type [yaml, xml, annotation, annot, yaml]
     */
    private $normalizers;

    /**
     * @var array<string, DenormalizerInterface> Key must be type [yaml, xml, annotation, annot, yaml]
     */
    private $denormalizers;

    public function __construct(array $normalizers, array $denormalizers)
    {
        $this->normalizers = $normalizers;
        $this->denormalizers = $denormalizers;
    }

    public function convert(\ReflectionClass $class, string $from, string $to)
    {
        if (!$normalizer = $this->findLizer($from, $this->normalizers)) {
            throw new \RuntimeException("Normalizer for type `$from` not found");
        }
        if (!$denormalizer = $this->findLizer($to, $this->denormalizers)) {
            throw new \RuntimeException("Denormalizer for type `$from` not found");
        }

        $denormalizer->denormalize($normalizer->normalize($class));
    }

    /**
     * @return NormalizerInterface|DenormalizerInterface|null
     */
    private function findLizer(string $key, array $dictionary)
    {
        if (!\array_key_exists($key, $dictionary)) {
            return null;
        }

        return $dictionary[$key];
    }
}
