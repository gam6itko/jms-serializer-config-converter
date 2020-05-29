<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Model;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
final class XmlMapConfig extends AbstractXmlCollectionConfig
{
    /**
     * @var string|null
     */
    public $keyAttributeName;

    public function serialize()
    {
        return serialize([
            $this->keyAttributeName,
            parent::serialize(),
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->keyAttributeName,
            $parentSerialized,
        ] = unserialize($serialized);

        parent::unserialize($parentSerialized);
    }
}
