<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Model;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
final class XmlElementConfig implements \Serializable
{
    /**
     * @var bool
     */
    public $cdata = true;

    /**
     * @var string|null
     */
    public $namespace;

    public function serialize()
    {
        return serialize([
            $this->cdata,
            $this->namespace,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->cdata,
            $this->namespace,
        ] = unserialize($serialized);
    }
}
