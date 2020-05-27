<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Model;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
final class ClassConfig implements \Serializable
{
    /**
     * @var string Class name
     */
    public $name;

    /**
     * @var string
     */
    public $exclusionPolicy = 'NONE';

    /**
     * @var bool
     */
    public $exclude = false;

    /**
     * @var string|null
     */
    public $excludeIf;

    /**
     * @var bool
     */
    public $readOnly = false;

    /**
     * @var string|null
     */
    public $accessType;

    /**
     * @var string
     */
    public $accessorOrder;

    /**
     * @var array|null
     */
    public $customAccessorOrder;

    /**
     * @var DiscriminatorConfig
     */
    public $discriminator;

    /**
     * @var PropertyConfig[]
     */
    public $properties = [];

    /**
     * @var VirtualPropertyConfig[]
     */
    public $virtualProperties = [];

    /**
     * @var array
     */
    public $callbackMethods = [
        'preSerialize'    => [],
        'postSerialize'   => [],
        'postDeserialize' => [],
    ];

    /**
     * @var string|null
     */
    public $xmlRootName;

    /**
     * @var string|null
     */
    public $xmlRootNamespace;

    /**
     * @var string|null
     */
    public $xmlRootPrefix;

    /**
     * @var array|null
     */
    public $xmlNamespaces;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function serialize()
    {
        return serialize([
            $this->name,
            $this->exclusionPolicy,
            $this->exclude,
            $this->excludeIf,
            $this->readOnly,
            $this->readOnly,
            $this->accessType,
            $this->accessorOrder,
            $this->customAccessorOrder,
            $this->discriminator,
            $this->properties,
            $this->virtualProperties,
            $this->callbackMethods,
            $this->xmlRootName,
            $this->xmlRootNamespace,
            $this->xmlRootPrefix,
            $this->xmlNamespaces,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->name,
            $this->exclusionPolicy,
            $this->exclude,
            $this->excludeIf,
            $this->readOnly,
            $this->readOnly,
            $this->accessType,
            $this->accessorOrder,
            $this->customAccessorOrder,
            $this->discriminator,
            $this->properties,
            $this->virtualProperties,
            $this->callbackMethods,
            $this->xmlRootName,
            $this->xmlRootNamespace,
            $this->xmlRootPrefix,
            $this->xmlNamespaces,
        ] = unserialize($serialized);
    }
}
