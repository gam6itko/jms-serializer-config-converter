<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Normalizer;

use Gam6itko\JSCC\Model\AccessorConfig;
use Gam6itko\JSCC\Model\ClassConfig;
use Gam6itko\JSCC\Model\DiscriminatorConfig;
use Gam6itko\JSCC\Model\PropertyConfig;
use Gam6itko\JSCC\Model\VirtualPropertyConfig;
use Gam6itko\JSCC\Model\XmlElementConfig;
use Gam6itko\JSCC\Model\XmlListConfig;
use Gam6itko\JSCC\Model\XmlMapConfig;
use Jawira\CaseConverter\Convert;
use JMS\Serializer\Exception\InvalidMetadataException;
use JMS\Serializer\Exception\XmlErrorException;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
class XmlNormalizer extends AbstractFileNormalizer
{
    /**
     * @throws InvalidMetadataException
     */
    protected function loadFromFile(\ReflectionClass $class, string $path): ?ClassConfig
    {
        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $elem = simplexml_load_file($path);
        libxml_use_internal_errors($previous);

        if (false === $elem) {
            throw new InvalidMetadataException('Invalid XML content for config', 0, new XmlErrorException(libxml_get_last_error()));
        }

        $config = new ClassConfig($name = $class->name);
        if (!$elems = $elem->xpath(sprintf('./class[@name="%s"]', $config->name))) {
            throw new InvalidMetadataException(sprintf('Could not find class %s inside XML element.', $name));
        }
        $elem = reset($elems);

        $config->exclusionPolicy = strtoupper((string) $elem->attributes()->{'exclusion-policy'}) ?: 'NONE';
        $config->exclude = filter_var($elem->attributes()->exclude, FILTER_VALIDATE_BOOLEAN);
        if (null !== $excludeIf = $elem->attributes()->{'exclude-if'}) {
            $config->excludeIf = (string) $excludeIf;
        }

        $config->accessType = (string) ($elem->attributes()->{'access-type'} ?: PropertyMetadata::ACCESS_TYPE_PROPERTY);

        if (null !== $accessorOrder = $elem->attributes()->{'accessor-order'}) {
            $config->accessorOrder = (string) $accessorOrder;
            $config->customAccessorOrder = preg_split('/\s*,\s*/', (string) $elem->attributes()->{'custom-accessor-order'});
        }
        if (null !== $xmlRootName = $elem->attributes()->{'xml-root-name'}) {
            $config->xmlRootName = (string) $xmlRootName;
        }
        if (null !== $xmlRootNamespace = $elem->attributes()->{'xml-root-namespace'}) {
            $config->xmlRootNamespace = (string) $xmlRootNamespace;
        }
        if (null !== $xmlRootPrefix = $elem->attributes()->{'xml-root-prefix'}) {
            $config->xmlRootPrefix = (string) $xmlRootPrefix;
        }

        $config->readOnly = filter_var($elem->attributes()->{'read-only'}, FILTER_VALIDATE_BOOLEAN);
        $config->discriminator = $this->parseDiscriminator($elem);

        foreach ($elem->xpath('./xml-namespace') as $xmlNamespace) {
            if (!isset($xmlNamespace->attributes()->uri)) {
                throw new InvalidMetadataException('The prefix attribute must be set for all xml-namespace elements.');
            }

            if (isset($xmlNamespace->attributes()->prefix)) {
                $prefix = (string) $xmlNamespace->attributes()->prefix;
            } else {
                $prefix = null;
            }

            $config->xmlNamespaces[$prefix] = (string) $xmlNamespace->attributes()->uri;
        }

        foreach ($elem->xpath('./virtual-property') as $vpElem) {
            $vp = new VirtualPropertyConfig();
            if (null !== $vpName = $vpElem->attributes()->name) {
                $vp->name = (string) $vpName;
            }
            if (null !== $vpMethod = $vpElem->attributes()->method) {
                $vp->method = (string) $vpMethod;
            }
            if (null !== $expr = $vpElem->attributes()->expression) {
                $vp->exp = (string) $expr;
            }
            if (null !== $serializerName = $vpElem->attributes()->{'serialized-name'}) {
                $vp->serializedName = (string) $serializerName;
            }
            if (null !== $type = $vpElem->attributes()->type) {
                $vp->type = (string) $type;
            }
            $config->virtualProperties[$vp->method ?: $vp->name] = $vp;
        }

        foreach ($elem->xpath('./property') as $pElem) {
            $propConf = new PropertyConfig();
            $propConf->name = (string) $pElem->attributes()->name;
            if (null !== $exclude = $pElem->attributes()->exclude) {
                $propConf->exclude = filter_var($exclude, FILTER_VALIDATE_BOOLEAN);
            }
            if (null !== $pElem->attributes()->{'exclude-if'}) {
                $propConf->excludeIf = (string) $excludeIf;
            }

            if (null !== $expose = $pElem->attributes()->expose) {
                $propConf->expose = filter_var($expose, FILTER_VALIDATE_BOOLEAN);
            }
            if (null !== $exposeIf = $pElem->attributes()->{'expose-if'}) {
                $propConf->exposeIf = (string) $exposeIf;
            }

            if (null !== $skip = $pElem->attributes()->{'skip-when-empty'}) {
                $propConf->skipWhenEmpty = filter_var($skip, FILTER_VALIDATE_BOOLEAN);
            }

            if (null !== $version = $pElem->attributes()->{'since-version'}) {
                $propConf->sinceVersion = (string) $version;
            }

            if (null !== $version = $pElem->attributes()->{'until-version'}) {
                $propConf->untilVersion = (string) $version;
            }

            if (null !== $serializedName = $pElem->attributes()->{'serialized-name'}) {
                $propConf->serializedName = (string) $serializedName;
            }

            if (null !== $type = $pElem->attributes()->type) {
                $propConf->type = (string) $type;
            } elseif (isset($pElem->type)) {
                $propConf->type = (string) $pElem->type;
            }

            if (null !== $groups = $pElem->attributes()->groups) {
                $propConf->groups = preg_split('/\s*,\s*/', trim((string) $groups));
            } elseif (isset($pElem->groups)) {
                $propConf->groups = (array) $pElem->groups->value;
            }

            if (isset($pElem->{'xml-list'})) {
                $xmlList = new XmlListConfig();

                $colConfig = $pElem->{'xml-list'};
                if (isset($colConfig->attributes()->inline)) {
                    $xmlList->inline = filter_var($colConfig->attributes()->inline, FILTER_VALIDATE_BOOLEAN);
                }

                if (isset($colConfig->attributes()->{'entry-name'})) {
                    $xmlList->entryName = (string) $colConfig->attributes()->{'entry-name'};
                }

                if (isset($colConfig->attributes()->{'skip-when-empty'})) {
                    $xmlList->skipWhenEmpty = filter_var($colConfig->attributes()->{'skip-when-empty'}, FILTER_VALIDATE_BOOLEAN);
                }

                if (isset($colConfig->attributes()->namespace)) {
                    $xmlList->namespace = (string) $colConfig->attributes()->namespace;
                }

                $propConf->xmlList = $xmlList;
            }

            if (isset($pElem->{'xml-map'})) {
                $xmlMap = new XmlMapConfig();

                $colConfig = $pElem->{'xml-map'};
                if (isset($colConfig->attributes()->inline)) {
                    $xmlMap->inline = filter_var($colConfig->attributes()->inline, FILTER_VALIDATE_BOOLEAN);
                }

                if (isset($colConfig->attributes()->{'entry-name'})) {
                    $xmlMap->entryName = (string) $colConfig->attributes()->{'entry-name'};
                }

                if (isset($colConfig->attributes()->namespace)) {
                    $xmlMap->namespace = (string) $colConfig->attributes()->namespace;
                }

                if (isset($colConfig->attributes()->{'key-attribute-name'})) {
                    $xmlMap->keyAttributeName = (string) $colConfig->attributes()->{'key-attribute-name'};
                }
                $propConf->xmlMap = $xmlMap;
            }

            if (isset($pElem->{'xml-element'})) {
                $xmlElement = new XmlElementConfig();

                $colConfig = $pElem->{'xml-element'};
                if (isset($colConfig->attributes()->cdata)) {
                    $xmlElement->cdata = filter_var($colConfig->attributes()->cdata, FILTER_VALIDATE_BOOLEAN);
                }

                if (isset($colConfig->attributes()->namespace)) {
                    $xmlElement->namespace = (string) $colConfig->attributes()->namespace;
                }

                $propConf->xmlElement = $xmlElement;
            }

            if (isset($pElem->attributes()->{'xml-attribute'})) {
                $propConf->xmlAttribute = 'true' === (string) $pElem->attributes()->{'xml-attribute'};
            }

            if (isset($pElem->attributes()->{'xml-attribute-map'})) {
                $propConf->xmlAttributeMap = 'true' === (string) $pElem->attributes()->{'xml-attribute-map'};
            }

            if (isset($pElem->attributes()->{'xml-value'})) {
                $propConf->xmlValue = 'true' === (string) $pElem->attributes()->{'xml-value'};
            }

            if (isset($pElem->attributes()->{'xml-key-value-pairs'})) {
                $propConf->xmlKeyValuePairs = 'true' === (string) $pElem->attributes()->{'xml-key-value-pairs'};
            }

            if (isset($pElem->attributes()->{'max-depth'})) {
                $propConf->maxDepth = (int) $pElem->attributes()->{'max-depth'};
            }

            //we need read-only before setter and getter set, because that method depends on flag being set
            if (null !== $readOnly = $pElem->attributes()->{'read-only'}) {
                $propConf->readOnly = filter_var($readOnly, FILTER_VALIDATE_BOOLEAN);
            }

            if (null !== $accessType = $pElem->attributes()->{'access-type'}) {
                $propConf->accessType = (string) $accessType;
            }

            if (null !== $inline = $pElem->attributes()->inline) {
                $propConf->inline = 'true' === strtolower((string) $inline);
            }

            if (isset($pElem->attributes()->{'accessor-getter'}) || isset($pElem->attributes()->{'accessor-setter'})) {
                $propConf->accessor = new AccessorConfig();
                $propConf->accessor->getter = (string) $pElem->attributes()->{'accessor-getter'};
                $propConf->accessor->setter = (string) $pElem->attributes()->{'accessor-setter'};
            }

            $config->properties[$propConf->name] = $propConf;
        }

        foreach ($elem->xpath('./callback-method') as $vpElem) {
            if (!isset($vpElem->attributes()->type)) {
                throw new InvalidMetadataException('The type attribute must be set for all callback-method elements.');
            }
            if (!isset($vpElem->attributes()->name)) {
                throw new InvalidMetadataException('The name attribute must be set for all callback-method elements.');
            }

            $key = (new Convert((string) $vpElem->attributes()->type))->toCamel();
            $config->callbackMethods[$key][] = (string) $vpElem->attributes()->name;
        }

        return $config;
    }

    protected function getExtensions(): array
    {
        return ['xml'];
    }

    private function parseDiscriminator(\SimpleXMLElement $elem)
    {
        $discriminator = new DiscriminatorConfig();
        $discriminator->fieldName = (string) $elem->attributes()->{'discriminator-field-name'};
        $discriminator->disabled = filter_var($elem->attributes()->{'discriminator-disabled'}, FILTER_VALIDATE_BOOLEAN);
        foreach ($elem->xpath('./discriminator-class') as $entry) {
            if (!isset($entry->attributes()->value)) {
                throw new InvalidMetadataException('Each discriminator-class element must have a "value" attribute.');
            }

            $discriminator->map[(string) $entry->attributes()->value] = (string) $entry;
        }

        foreach ($elem->xpath('./discriminator-groups/group') as $entry) {
            $discriminator->groups[] = (string) $entry;
        }

        foreach ($elem->xpath('./xml-discriminator') as $xmlDiscriminator) {
            if (isset($xmlDiscriminator->attributes()->attribute)) {
                $discriminator->xmlAttribute = filter_var($xmlDiscriminator->attributes()->attribute, FILTER_VALIDATE_BOOLEAN);
            }
            if (isset($xmlDiscriminator->attributes()->cdata) || isset($xmlDiscriminator->attributes()->namespace)) {
                $xmlElement = new XmlElementConfig();
                $xmlElement->cdata = filter_var($xmlDiscriminator->attributes()->cdata, FILTER_VALIDATE_BOOLEAN);
                $xmlElement->namespace = (string) $xmlDiscriminator->attributes()->namespace;
                $discriminator->xmlElement = $xmlElement;
            }
        }

        return $discriminator;
    }
}
