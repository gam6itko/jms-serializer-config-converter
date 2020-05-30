<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Model;

use JMS\Serializer\Annotation\XmlElement;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
final class PropertyConfig extends AbstractPropertyConfig
{
    /**
     * @var bool|null
     */
    public $exclude;

    /**
     * @var bool|null
     */
    public $expose;

    /**
     * @var string|null Expression
     */
    public $excludeIf;

    /**
     * @var string|null Expression
     */
    public $exposeIf;

    /**
     * @var bool|null
     */
    public $skipWhenEmpty;

    /**
     * @var string|null
     */
    public $accessType;

    /**
     * @var AccessorConfig
     */
    public $accessor;

    /**
     * @var bool|null
     */
    public $inline;

    /**
     * @var bool|null
     */
    public $readOnly;

    /**
     * @var int|null
     */
    public $maxDepth;

    /**
     * @var bool|null
     */
    public $xmlAttributeMap;

    /**
     * @var bool|null
     */
    public $xmlKeyValuePairs;

    /**
     * @var XmlListConfig|null
     */
    public $xmlList;

    /**
     * @var XmlMapConfig|null
     */
    public $xmlMap;

    /**
     * @var XmlElement|null
     */
    public $xmlElement;

    public function serialize()
    {
        return serialize([
            $this->exclude,
            $this->expose,
            $this->excludeIf,
            $this->exposeIf,
            $this->skipWhenEmpty,
            $this->accessType,
            $this->accessor,
            $this->sinceVersion,
            $this->untilVersion,
            $this->groups,
            $this->inline,
            $this->readOnly,
            $this->maxDepth,
            $this->xmlAttributeMap,
            $this->xmlKeyValuePairs,
            $this->xmlList,
            $this->xmlMap,
            $this->xmlElement,
            parent::serialize(),
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->exclude,
            $this->expose,
            $this->excludeIf,
            $this->exposeIf,
            $this->skipWhenEmpty,
            $this->accessType,
            $this->accessor,
            $this->sinceVersion,
            $this->untilVersion,
            $this->groups,
            $this->inline,
            $this->readOnly,
            $this->maxDepth,
            $this->xmlAttributeMap,
            $this->xmlKeyValuePairs,
            $this->xmlList,
            $this->xmlMap,
            $this->xmlElement,
            $parentSerialized,
        ] = unserialize($serialized);

        parent::unserialize($parentSerialized);
    }
}
