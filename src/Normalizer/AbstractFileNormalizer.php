<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Normalizer;

use Gam6itko\JSCC\Model\ClassConfig;
use Metadata\Driver\FileLocatorInterface;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
abstract class AbstractFileNormalizer implements NormalizerInterface
{
    /**
     * @var FileLocatorInterface
     */
    private $locator;

    public function __construct(FileLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    public function normalize(\ReflectionClass $class): ?ClassConfig
    {
        $path = null;
        foreach ($this->getExtensions() as $extension) {
            $path = $this->locator->findFileForClass($class, $extension);
            if (null !== $path) {
                break;
            }
        }

        if (null === $path) {
            return null;
        }

        return $this->loadFromFile($class, $path);
    }

    /**
     * Parses the content of the file, and converts it to the desired config object.
     */
    abstract protected function loadFromFile(\ReflectionClass $class, string $path): ?ClassConfig;

    /**
     * @return string[]
     */
    abstract protected function getExtensions(): array;

    protected function parseString($value): string
    {
        return (string) $value;
    }

    protected function parseBool($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    protected function parseInt($value): int
    {
        return (int) $value;
    }

    protected function parseFloat($value): float
    {
        return (float) $value;
    }

    protected function parseArray($value): ?array
    {
        if (!\is_array($value)) {
            return null;
        }

        return $value;
    }
}
