<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Tests\Fixtures\Discriminator;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\Discriminator(field = "type", map = {
 *    "post": "Gam6itko\JSCC\Tests\Fixtures\Discriminator\Post",
 *    "image_post": "Gam6itko\JSCC\Tests\Fixtures\Discriminator\ImagePost",
 * })
 */
class Post
{
    /** @Serializer\Type("string") */
    public $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }
}
