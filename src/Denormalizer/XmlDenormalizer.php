<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Denormalizer;

use Gam6itko\JSCC\ConfigPropertiesTrait;
use Gam6itko\JSCC\Model\ClassConfig;
use Gam6itko\JSCC\Model\XmlElementConfig;
use Gam6itko\JSCC\Model\XmlListConfig;
use Gam6itko\JSCC\Model\XmlMapConfig;
use Jawira\CaseConverter\Convert;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
class XmlDenormalizer extends AbstractFileDenormalizer
{
    use ConfigPropertiesTrait;

    const SIMPLE_TYPES = ['string', 'bool', 'boolean', 'int', 'integer', 'float', 'array'];

    protected function getExtension(): string
    {
        return 'xml';
    }

    public function toString(ClassConfig $config): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><serializer/>');
        $classEl = $xml->addChild('class');

        $properties = $this->extractClassPropertyTypes(new \ReflectionClass(ClassConfig::class));
        foreach ($properties as $propertyName => $type) {
            $value = $config->{$propertyName};
            if (null === $value) {
                continue;
            }

            if ('callbackMethods' === $propertyName) {
                foreach ($value as $callbackType => $arr) {
                    foreach ($arr as $methodName) {
                        $cm = $classEl->addChild('callback-method');
                        $cm->addAttribute('type', (new Convert($callbackType))->toKebab());
                        $cm->addAttribute('name', $methodName);
                    }
                }
            } elseif ('discriminator' === $propertyName) {
                if (null !== $value->disabled) {
                    $classEl->addAttribute('discriminator-disabled', $this->b2s($value->disabled));
                }
                if (null !== $value->fieldName) {
                    $classEl->addAttribute('discriminator-field-name', $value->fieldName);
                }
                if ($value->groups) {
                    $discGroup = $classEl->addChild('discriminator-groups');
                    foreach ($value->groups as $group) {
                        $discGroup->addChild('group', $group);
                    }
                }
                if ($value->map) {
                    foreach ($value->map as $prop => $class) {
                        $discClass = $classEl->addChild('discriminator-class', $class);
                        $discClass->addAttribute('value', $prop);
                    }
                }
                if ($value->xmlElement || $value->xmlAttribute) {
                    $discEl = $classEl->addChild('xml-discriminator');
                    if ($value->xmlAttribute) {
                        $discEl->addAttribute('attribute', $this->b2s($value->xmlAttribute));
                    }
                    $discEl->addAttribute('cdata', $this->b2s($value->xmlElement->cdata));
                    $discEl->addAttribute('namespace', $value->xmlElement->namespace);
                }
            } elseif ('xmlNamespaces' === $propertyName) {
                foreach ($value as $prefix => $uri) {
                    $xmlNsEl = $classEl->addChild('xml-namespace');
                    $xmlNsEl->addAttribute('prefix', $prefix);
                    $xmlNsEl->addAttribute('uri', $uri);
                }
            } elseif ('virtualProperties' === $propertyName) {
                foreach ($value as $name => $v) {
                    $vpEl = $this->addProperty($classEl, 'virtual-property', $v);
                    if (isset($vpEl['exp'])) {
                        $vpEl->addAttribute('expression', (string) $vpEl['exp']);
                        unset($vpEl['exp']);
                    }
                }
            } elseif ('properties' === $propertyName) {
                foreach ($value as $name => $v) {
                    $this->addProperty($classEl, 'property', $v);
                }
            } else {
                $this->setAttribute($classEl, $propertyName, $type, $value);
            }
        }

        return $xml->asXML();
    }

    private function setAttribute($classEl, $propertyName, $type, $value)
    {
        if (null === $value) {
            return;
        }

        $key = (new Convert($propertyName))->toKebab();
        if (\in_array($type, self::SIMPLE_TYPES)) {
            if (\is_array($value)) {
                // groups, customAccessOrder
                $value = implode(',', $value);
            }
            if (\is_bool($value)) {
                $value = $this->b2s($value);
            }
            $classEl->addAttribute($key, (string) $value);
        }
    }

    private function b2s(bool $value): string
    {
        return $value ? 'true' : 'false';
    }

    private function addProperty(\SimpleXMLElement $classEl, string $nodeName, $config)
    {
        $propEl = $classEl->addChild($nodeName);
        $properties = $this->extractClassPropertyTypes(new \ReflectionClass($config));
        foreach ($properties as $propertyName => $type) {
            $value = $config->{$propertyName};
            if (null === $value) {
                continue;
            }

            if ('accessor' === $propertyName) {
                $propEl->addAttribute('accessor-getter', $value->getter);
                $propEl->addAttribute('accessor-setter', $value->setter);
            } elseif ('xmlList' === $propertyName) {
                $el = $propEl->addChild('xml-list');
                $pArr = $this->extractClassPropertyTypes(new \ReflectionClass(XmlListConfig::class));
                foreach ($pArr as $p => $t) {
                    $v = $value->{$p};
                    $this->setAttribute($el, $p, $t, $v);
                }
            } elseif ('xmlMap' === $propertyName) {
                $el = $propEl->addChild('xml-map');
                $pArr = $this->extractClassPropertyTypes(new \ReflectionClass(XmlMapConfig::class));
                foreach ($pArr as $p => $t) {
                    $v = $value->{$p};
                    $this->setAttribute($el, $p, $t, $v);
                }
            } elseif ('xmlElement' === $propertyName) {
                $el = $propEl->addChild('xml-element');
                $pArr = $this->extractClassPropertyTypes(new \ReflectionClass(XmlElementConfig::class));
                foreach ($pArr as $p => $t) {
                    $v = $value->{$p};
                    $this->setAttribute($el, $p, $t, $v);
                }
            } else {
                $this->setAttribute($propEl, $propertyName, $type, $value);
            }
        }

        return $propEl;
    }
}
