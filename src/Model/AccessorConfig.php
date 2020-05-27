<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Model;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
final class AccessorConfig implements \Serializable
{
    /**
     * @var string
     */
    public $getter;

    /**
     * @var string
     */
    public $setter;

    public function serialize()
    {
        return serialize([
            $this->getter,
            $this->setter,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->getter,
            $this->setter,
        ] = unserialize($serialized);
    }
}
