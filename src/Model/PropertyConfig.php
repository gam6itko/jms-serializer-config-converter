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
            $this->accessType,
            $this->accessor,
            $this->inline,
            $this->readOnly,
            $this->maxDepth,
            $this->xmlAttributeMap,
            $this->xmlKeyValuePairs,
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
            $this->accessType,
            $this->accessor,
            $this->inline,
            $this->readOnly,
            $this->maxDepth,
            $this->xmlAttributeMap,
            $this->xmlKeyValuePairs,
            $this->xmlElement,
            $parentSerialized,
        ] = unserialize($serialized);

        parent::unserialize($parentSerialized);
    }
}
