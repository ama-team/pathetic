<?php

namespace AmaTeam\Pathetic\Test\Suite\Unit;

use AmaTeam\Pathetic\Path;
use AmaTeam\Pathetic\Test\Support\Test;

/**
 * @author Etki <etki@etki.me>
 */
class AttributesTest extends Test
{
    public function dataProvider()
    {
        $u = Path::PLATFORM_UNIX;
        $w = Path::PLATFORM_WINDOWS;
        // path | platform | intmask: root, absolute
        return [
            ['/', $u, [1, 1,],],
            ['/node', $u, [0, 1,],],
            ['node', $u, [0, 0,],],
            ['/', $w, [1, 1,],],
            ['\\', $w, [1, 1,],],
            ['c:/', $w, [1, 1,],],
            ['/node', $w, [0, 1,],],
            ['c:/node', $w, [0, 1,],],
            ['node', $w, [0, 0,],],
        ];
    }

    /**
     * @test
     *
     * @dataProvider dataProvider
     *
     * @param string $input
     * @param string $platform
     * @param int[] $mask
     */
    public function shouldHaveExpectedAttributes($input, $platform, $mask)
    {
        $path = Path::parse($input, $platform);
        $this->assertEquals((bool) $mask[0], $path->isRoot());
        $this->assertEquals((bool) $mask[1], $path->isAbsolute());
        $this->assertEquals(!((bool) $mask[1]), $path->isRelative());
    }
}
