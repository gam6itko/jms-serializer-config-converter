<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Model;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
abstract class AbstractXmlCollectionConfig implements \Serializable
{
    /**
     * @var string
     */
    public $entryName = 'entry';

    /**
     * @var bool
     */
    public $inline = false;

    /**
     * @var string|null
     */
    public $namespace;

    /**
     * @var bool|null
     */
    public $skipWhenEmpty;

    public function serialize()
    {
        return serialize([
            $this->entryName,
            $this->inline,
            $this->namespace,
            $this->skipWhenEmpty,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->entryName,
            $this->inline,
            $this->namespace,
            $this->skipWhenEmpty,
        ] = unserialize($serialized);
    }
}
