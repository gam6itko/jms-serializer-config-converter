<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Model;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
abstract class AbstractPropertyConfig implements \Serializable
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string|null
     */
    public $serializedName;

    /**
     * @var string|null
     */
    public $sinceVersion;

    /**
     * @var string|null
     */
    public $untilVersion;

    /**
     * @var array|null
     */
    public $groups;

    /**
     * @var bool|null
     */
    public $xmlAttribute;

    /**
     * @var bool|null
     */
    public $xmlValue;

    public function serialize()
    {
        return serialize([
            $this->name,
            $this->type,
            $this->serializedName,
            $this->xmlAttribute,
            $this->xmlValue,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->name,
            $this->type,
            $this->serializedName,
            $this->xmlAttribute,
            $this->xmlValue,
        ] = unserialize($serialized);
    }
}
