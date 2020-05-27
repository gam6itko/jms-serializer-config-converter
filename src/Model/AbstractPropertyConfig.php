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

    public function serialize()
    {
        return serialize([
            $this->name,
            $this->type,
            $this->serializedName,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->name,
            $this->type,
            $this->serializedName,
        ] = unserialize($serialized);
    }
}
