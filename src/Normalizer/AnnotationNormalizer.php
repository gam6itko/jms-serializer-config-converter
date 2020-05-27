<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Normalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Gam6itko\JSCC\Model\AccessorConfig;
use Gam6itko\JSCC\Model\ClassConfig;
use Gam6itko\JSCC\Model\DiscriminatorConfig;
use Gam6itko\JSCC\Model\PropertyConfig;
use Gam6itko\JSCC\Model\VirtualPropertyConfig;
use Gam6itko\JSCC\Model\XmlElementConfig;
use Gam6itko\JSCC\Model\XmlListConfig;
use Gam6itko\JSCC\Model\XmlMapConfig;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\Discriminator;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Inline;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\PostDeserialize;
use JMS\Serializer\Annotation\PostSerialize;
use JMS\Serializer\Annotation\PreSerialize;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\SkipWhenEmpty;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlAttributeMap;
use JMS\Serializer\Annotation\XmlDiscriminator;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlKeyValuePairs;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlMap;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
class AnnotationNormalizer implements NormalizerInterface
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(?Reader $annotationReader = null)
    {
        $this->reader = $annotationReader ?? new AnnotationReader();
    }

    public function normalize(\ReflectionClass $class): ?ClassConfig
    {
        $config = new ClassConfig($class->name);

        foreach ($this->reader->getClassAnnotations($class) as $annot) {
            if ($annot instanceof ExclusionPolicy) {
                $config->exclusionPolicy = $annot->policy;
            } elseif ($annot instanceof XmlRoot) {
                $config->xmlRootName = $annot->name;
                $config->xmlRootNamespace = $annot->namespace;
                $config->xmlRootPrefix = $annot->prefix;
            } elseif ($annot instanceof XmlNamespace) {
                $config->xmlNamespaces[$annot->prefix] = $annot->uri;
            } elseif ($annot instanceof Exclude) {
                if (null !== $annot->if) {
                    $config->excludeIf = $annot->if;
                } else {
                    $config->exclude = true;
                }
            } elseif ($annot instanceof AccessType) {
                $config->accessType = $annot->type;
            } elseif ($annot instanceof ReadOnly) {
                $config->readOnly = $annot->readOnly;
            } elseif ($annot instanceof AccessorOrder) {
                $config->accessorOrder = $annot->order;
                $config->customAccessorOrder = $annot->custom;
            } elseif ($annot instanceof Discriminator) {
                $disc = $config->discriminator ?? new DiscriminatorConfig();
                if ($annot->disabled) {
                    $disc->disabled = true;
                } else {
                    $disc->fieldName = $annot->field;
                    $disc->map = $annot->map;
                    $disc->groups = $annot->groups;
                }
                $config->discriminator = $disc;
                unset($disc);
            } elseif ($annot instanceof XmlDiscriminator) {
                $disc = $config->discriminator ?? new DiscriminatorConfig();
                $disc->xmlAttribute = (bool) $annot->attribute;
                $disc->xmlElement = new XmlElementConfig();
                $disc->xmlElement->cdata = (bool) $annot->cdata;
                $disc->xmlElement->namespace = $annot->namespace ? (string) $annot->namespace : null;
                $config->discriminator = $disc;
                unset($disc);
            } elseif ($annot instanceof VirtualProperty) {
                $vp = new VirtualPropertyConfig();
                $vp->name = $annot->name;
                $vp->exp = $annot->exp;
                $this->fillProperty($vp, $annot->options);
                $config->virtualProperties[$annot->name] = $vp;
            }
        }

        $this->parseMethods($class, $config);
        $this->parseProperties($class, $config);

        return $config;
    }

    private function parseMethods(\ReflectionClass $class, ClassConfig $config): void
    {
        $callbacks = [
            PreSerialize::class    => 'preSerialize',
            PostSerialize::class   => 'postSerialize',
            PostDeserialize::class => 'postDeserialize',
        ];

        foreach ($class->getMethods() as $method) {
            $annotations = $this->reader->getMethodAnnotations($method);

            foreach ($annotations as $i => $annot) {
                foreach ($callbacks as $cbClass => $key) {
                    if ($annot instanceof $cbClass) {
                        $config->callbackMethods[$key][] = $method->name;
                        unset($annotations[$i]);
                        continue;
                    }
                }
            }

            if (!$annotations) {
                continue;
            }

            $vp = null;
            foreach ($annotations as $i => $annot) {
                if ($annot instanceof VirtualProperty) {
                    $vp = new VirtualPropertyConfig();
                    $vp->name = $annot->name;
                    $vp->exp = $annot->exp;
                    $this->fillProperty($vp, $annot->options);
                    unset($annotations[$i]);
                    break;
                }
            }

            if ($vp) {
                $this->fillProperty($vp, $annotations);
                if (!$vp->name) {
                    $vp->name = $method->getName();
                }
                $config->virtualProperties[$method->getName()] = $vp;
            }
        }
    }

    private function parseProperties(\ReflectionClass $class, ClassConfig $config)
    {
        foreach ($class->getProperties() as $property) {
            $propConf = new PropertyConfig();
            $propConf->name = $property->getName();
            $this->fillProperty($propConf, $this->reader->getPropertyAnnotations($property));
            $config->properties[$property->getName()] = $propConf;
        }
    }

    /**
     * @param PropertyConfig|VirtualPropertyConfig $propConf
     */
    private function fillProperty($propConf, array $annotations = [])
    {
        if (!$annotations) {
            return;
        }

        foreach ($annotations as $annot) {
            if ($annot instanceof Accessor) {
                $propConf->accessor = new AccessorConfig();
                $propConf->accessor->getter = $annot->getter;
                $propConf->accessor->setter = $annot->setter;
            } elseif ($annot instanceof AccessType) {
                $propConf->accessType = $annot->type;
            } elseif ($annot instanceof SerializedName) {
                $propConf->serializedName = $annot->name;
            } elseif ($annot instanceof Since) {
                $propConf->sinceVersion = $annot->version;
            } elseif ($annot instanceof Until) {
                $propConf->untilVersion = $annot->version;
            } elseif ($annot instanceof Groups) {
                $propConf->groups = $annot->groups;
            } elseif ($annot instanceof Exclude) {
                if (null !== $annot->if) {
                    $propConf->excludeIf = $annot->if;
                } else {
                    $propConf->exclude = true;
                }
            } elseif ($annot instanceof Expose) {
                if (null !== $annot->if) {
                    $propConf->exposeIf = $annot->if;
                } else {
                    $propConf->expose = true;
                }
            } elseif ($annot instanceof MaxDepth) {
                $propConf->maxDepth = $annot->depth;
            } elseif ($annot instanceof Type) {
                $propConf->type = $annot->name;
            } elseif ($annot instanceof XmlElement) {
                $xmlElement = new XmlElementConfig();
                $xmlElement->cdata = $annot->cdata;
                $xmlElement->namespace = $annot->namespace;
                $propConf->xmlElement = $xmlElement;
            } elseif ($annot instanceof XmlList) {
                $xmlList = new XmlListConfig();
                $xmlList->namespace = $annot->namespace;
                $xmlList->entryName = $annot->entry;
                $xmlList->skipWhenEmpty = $annot->skipWhenEmpty;
                $xmlList->inline = $annot->inline;
                $propConf->xmlList = $xmlList;
            } elseif ($annot instanceof XmlMap) {
                $xmlMap = new XmlMapConfig();
                $xmlMap->inline = $annot->inline;
                $xmlMap->keyAttributeName = $annot->keyAttribute;
                $xmlMap->entryName = $annot->entry;
                $xmlMap->namespace = $annot->namespace;
                $propConf->xmlMap = $xmlMap;
            } elseif ($annot instanceof XmlAttributeMap) {
                $propConf->xmlAttributeMap = true;
            } elseif ($annot instanceof XmlKeyValuePairs) {
                $propConf->xmlKeyValuePairs = true;
            } elseif ($annot instanceof XmlAttribute) {
                $propConf->xmlAttribute = true;
            // $annot->namespace;
            } elseif ($annot instanceof SkipWhenEmpty) {
                $propConf->skipWhenEmpty = true;
            } elseif ($annot instanceof Inline) {
                $propConf->inline = true;
            } elseif ($annot instanceof XmlValue) {
                $propConf->xmlValue = true;
            } elseif ($annot instanceof ReadOnly) {
                $propConf->readOnly = true;
            }
        }
    }
}
