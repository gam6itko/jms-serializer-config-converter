<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Normalizer;

use Gam6itko\JSCC\ConfigPropertiesTrait;
use Gam6itko\JSCC\Model\AccessorConfig;
use Gam6itko\JSCC\Model\ClassConfig;
use Gam6itko\JSCC\Model\DiscriminatorConfig;
use Gam6itko\JSCC\Model\PropertyConfig;
use Gam6itko\JSCC\Model\VirtualPropertyConfig;
use Gam6itko\JSCC\Model\XmlElementConfig;
use Gam6itko\JSCC\Model\XmlListConfig;
use Gam6itko\JSCC\Model\XmlMapConfig;
use Jawira\CaseConverter\Convert;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
class YamlNormalizer extends AbstractFileNormalizer
{
    use ConfigPropertiesTrait;

    /**
     * @throws \LogicException
     */
    protected function loadFromFile(\ReflectionClass $class, string $path): ?ClassConfig
    {
        $data = Yaml::parse(file_get_contents($path));
        if (!isset($data[$class->getName()])) {
            throw new \LogicException(sprintf('Yaml must contains `%s` key', $class->getName()));
        }

        $data = $data[$class->getName()];
        $data['name'] = $class->name;

        return $this->fromArray(ClassConfig::class, $data);
    }

    protected function getExtensions(): array
    {
        return ['yaml', 'yml'];
    }

    /**
     * Creates Config object from array.
     */
    protected function fromArray(string $class, $data)
    {
        $reflection = new \ReflectionClass($class);
        $result = $reflection->newInstanceWithoutConstructor();

        $properties = $this->extractClassPropertyTypes($reflection);
        foreach ($properties as $propertyName => $type) {
            if (isset($data[$propertyName])) {
                @trigger_error(sprintf('Yaml result attribute `%s` should be in snake_case', $propertyName));
            }

            $snakeCase = (new Convert($propertyName))->toSnake();
            if (!isset($data[$snakeCase])) {
                continue;
            }

            $result->{$propertyName} = $this->parse($type, $data[$snakeCase]);
        }

        $this->postCreate($result, $data);

        return $result;
    }

    private function listOf(string $class, $array)
    {
        if (!\is_array($array)) {
            return null;
        }

        $result = [];
        foreach ($array as $key => $item) {
            if (!\is_array($item)) {
                continue;
            }
            if (empty($item['name'])) {
                $item['name'] = $key;
            }
            $result[$key] = $this->fromArray($class, $item);
        }

        return $result;
    }

    protected function parse(string $type, $value)
    {
        $methodName = 'parse'.ucfirst($type);

        return \call_user_func([$this, $methodName], $value);
    }

    protected function parseAccessor($value)
    {
        if (!\is_array($value)) {
            return null;
        }

        return $this->fromArray(AccessorConfig::class, $value);
    }

    protected function parseDiscriminator($value)
    {
        if (!\is_array($value)) {
            return null;
        }

        return $this->fromArray(DiscriminatorConfig::class, $value);
    }

    protected function parsePropertyArray($array)
    {
        return $this->listOf(PropertyConfig::class, $array);
    }

    protected function parseVirtualPropertyArray($array)
    {
        return $this->listOf(VirtualPropertyConfig::class, $array);
    }

    protected function parseXmlElement($value)
    {
        if (!\is_array($value)) {
            return null;
        }

        return $this->fromArray(XmlElementConfig::class, $value);
    }

    protected function parseXmlList($value)
    {
        return $this->fromArray(XmlListConfig::class, $value);
    }

    protected function parseXmlMap($value)
    {
        return $this->fromArray(XmlMapConfig::class, $value);
    }

    private function postCreate($object, $data)
    {
        if ($object instanceof ClassConfig) {
            $callbacks = $object->callbackMethods;
            $object->callbackMethods = [];
            foreach ($callbacks as $key => $val) {
                $key = (new Convert($key))->toCamel();
                $object->callbackMethods[$key] = $val;
            }
        }

        return $object;
    }
}
