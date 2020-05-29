<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Tests\Denormalizer;

use PHPUnit\Framework\TestCase;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
abstract class AbstractFileDenormalizer extends TestCase
{
    const NAMESPACE_FOLDER = [
        'Somewhere\Sometimes'          => __DIR__.'/../../do_not_touch_me',
        'Gam6itko\JSCC\Tests\Fixtures' => __DIR__.'/../sink',
    ];

    protected function setUp(): void
    {
        foreach (self::NAMESPACE_FOLDER as $ns => $folder) {
            if (!file_exists($folder)) {
                continue;
            }

            array_map('unlink', array_filter((array) glob("$folder/*")));
        }
    }
}
