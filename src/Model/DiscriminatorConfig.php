<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Model;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
final class DiscriminatorConfig implements \Serializable
{
    /**
     * @var bool
     */
    public $disabled = false;

    /**
     * @var string
     */
    public $fieldName;

    /**
     * @var array|null
     */
    public $map;

    /**
     * @var array|null
     */
    public $groups;

    /**
     * @var bool
     */
    public $xmlAttribute = true;

    /**
     * @var XmlElementConfig|null
     */
    public $xmlElement;

    public function serialize()
    {
        return serialize([
            $this->fieldName,
            $this->disabled,
            $this->map,
            $this->groups,
            $this->xmlAttribute,
            $this->xmlElement,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->fieldName,
            $this->disabled,
            $this->map,
            $this->groups,
            $this->xmlAttribute,
            $this->xmlElement,
        ] = unserialize($serialized);
    }
}
