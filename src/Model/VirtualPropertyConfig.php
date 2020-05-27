<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Model;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
final class VirtualPropertyConfig extends AbstractPropertyConfig
{
    /**
     * @var string|null Expression
     */
    public $exp;

    /**
     * @var string|null
     */
    public $method;

    public function serialize()
    {
        return serialize([
            $this->exp,
            $this->method,
            parent::serialize(),
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->exp,
            $this->method,
            $parentSerialized,
        ] = unserialize($serialized);

        parent::unserialize($parentSerialized);
    }
}
