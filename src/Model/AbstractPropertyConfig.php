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
     * @var bool|null
     */
    public $skipWhenEmpty;

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

    /**
     * @var XmlListConfig|null
     */
    public $xmlList;

    /**
     * @var XmlMapConfig|null
     */
    public $xmlMap;

    public function serialize()
    {
        return serialize([
            $this->name,
            $this->type,
            $this->serializedName,
            $this->sinceVersion,
            $this->untilVersion,
            $this->skipWhenEmpty,
            $this->groups,
            $this->xmlAttribute,
            $this->xmlValue,
            $this->xmlList,
            $this->xmlMap,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->name,
            $this->type,
            $this->serializedName,
            $this->sinceVersion,
            $this->untilVersion,
            $this->skipWhenEmpty,
            $this->groups,
            $this->xmlAttribute,
            $this->xmlValue,
            $this->xmlList,
            $this->xmlMap,
        ] = unserialize($serialized);
    }
}
