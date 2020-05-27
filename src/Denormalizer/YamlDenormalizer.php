<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Denormalizer;

use Gam6itko\JSCC\ConfigPropertiesTrait;
use Gam6itko\JSCC\Model\ClassConfig;
use Jawira\CaseConverter\Convert;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
class YamlDenormalizer extends AbstractFileDenormalizer
{
    use ConfigPropertiesTrait;

    const SIMPLE_TYPES = ['string', 'bool', 'boolean', 'int', 'integer', 'float', 'array'];

    protected function getExtension(): string
    {
        return 'yaml';
    }

    public function toString(ClassConfig $config): string
    {
        $array = $this->toArray($config);
        unset($array['name']);

        return Yaml::dump([
            $config->name => $array,
        ], 10);
    }

    /**
     * Creates array from Config object.
     */
    protected function toArray($configObject)
    {
        $reflection = new \ReflectionClass($configObject);

        $result = [];
        $properties = $this->extractClassPropertyTypes($reflection);
        foreach ($properties as $propertyName => $type) {
            if (null === $value = $configObject->{$propertyName}) {
                continue;
            }

            $key = (new Convert($propertyName))->toSnake();
            if (\in_array($type, self::SIMPLE_TYPES)) {
                $result[$key] = $value;
                continue;
            }

            if ($this->endsWith($type, 'Array')) {
                $result[$key] = $this->listOf($value);
                continue;
            }

            $result[$key] = $this->toArray($value);
        }

        $result = $this->postCreate($result, $configObject);

        return $result;
    }

    private function listOf($list)
    {
        if (!\is_array($list)) {
            return [];
        }

        $result = [];
        foreach ($list as $key => $item) {
            $toArray = $this->toArray($item);
            // delete name property if in duplicates array key
            if (\is_array($toArray) && isset($toArray['name']) && $toArray['name'] === $key) {
                unset($toArray['name']);
            }

            $result[$key] = $toArray;
        }

        return $result;
    }

    private function endsWith($haystack, $needle)
    {
        $length = \strlen($needle);
        if (0 == $length) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }

    private function postCreate(array $array, $classObject)
    {
        if ($classObject instanceof ClassConfig) {
            $callbacks = $array['callback_methods'];
            $array['callback_methods'] = [];
            foreach ($callbacks as $key => $val) {
                $key = (new Convert($key))->toSnake();
                $array['callback_methods'][$key] = $val;
            }
        }

        return $array;
    }
}
