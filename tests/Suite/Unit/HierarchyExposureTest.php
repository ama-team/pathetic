<?php

namespace AmaTeam\Pathetic\Test\Suite\Unit;

use AmaTeam\Pathetic\Path;
use AmaTeam\Pathetic\Test\Support\Test;

/**
 * @author Etki <etki@etki.me>
 */
class HierarchyExposureTest extends Test
{
    public function dataProvider()
    {
        $u = Path::PLATFORM_UNIX;
        $w = Path::PLATFORM_WINDOWS;
        return [
            ['node/path', $u, ['', 'node', 'node/path',],],
            ['/node/path', $u, ['/', '/node', '/node/path',],],
            ['node', $u, ['', 'node',],],
            ['node/path', $w, ['', 'node', 'node/path',],],
            ['c:/node/path', $w, ['c:/', 'c:/node', 'c:/node/path',],],
        ];
    }

    /**
     * @test
     *
     * @dataProvider dataProvider
     *
     * @param string $path
     * @param string $platform
     * @param string[] $paths
     */
    public function shouldProvidedCorrectHierarchy($path, $platform, $paths)
    {
        $path = Path::parse($path, $platform);
        $paths = array_map(function ($input) use ($platform) {
            return Path::parse($input, $platform);
        }, $paths);
        $enumeration = iterator_to_array($path->iterator());
        $parents = $path->getParents();
        $this->assertEquals($paths, $enumeration);
        $this->assertEquals(end($parents), $path->getParent());
        $this->assertEquals(array_slice($paths, 0, sizeof($paths) - 1), $parents);
    }
}
