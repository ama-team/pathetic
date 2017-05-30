<?php

namespace AmaTeam\Pathetic\Test\Suite\Unit;

use AmaTeam\Pathetic\Path;
use Codeception\Test\Unit;

/**
 * @author Etki <etki@etki.me>
 */
class ParseCorrectnessTest extends Unit
{
    public function dataProvider()
    {
        $u = Path::PLATFORM_UNIX;
        $w = Path::PLATFORM_WINDOWS;
        // input | platform | scheme | root | segments
        return [
            ['', $u, null, null, [],],
            ['leaf', $u , null, null, ['leaf',],],
            ['node/leaf', $u, null, null, ['node', 'leaf',],],
            ['node//leaf', $u, null, null, ['node', '', 'leaf',],],
            ['file://node/leaf', $u, 'file', null, ['node', 'leaf',],],
            ['file:///leaf', $u, 'file', '', ['leaf',],],
            ['file://c:/node/leaf', $u, 'file', null, ['c:', 'node', 'leaf']],
            ['node\\leaf', $u, null, null, ['node\\leaf',]],

            ['', $w, null, null, [],],
            ['leaf', $w , null, null, ['leaf',],],
            ['node\\leaf', $w, null, null, ['node', 'leaf',],],
            ['node/leaf', $w, null, null, ['node', 'leaf',],],
            ['file:///leaf', $w, 'file', '', ['leaf',],],
            ['c:\\node\\leaf', $w, null, 'c:', ['node', 'leaf',],],
            ['file://c:\\node\\leaf', $w, 'file', 'c:', ['node', 'leaf',],],
        ];
    }

    /**
     * @test
     *
     * @dataProvider dataProvider
     *
     * @param $input
     * @param $platform
     * @param $scheme
     * @param $root
     * @param $segments
     */
    public function shouldParseAsExpected($input, $platform, $scheme, $root, $segments)
    {
        $path = Path::parse($input, $platform);
        $this->assertEquals($scheme, $path->getScheme());
        $this->assertEquals($root, $path->getRoot());
        $this->assertEquals($segments, $path->getSegments());
        $separator = $platform === Path::PLATFORM_UNIX ? '/' : '\\';
        $this->assertEquals($separator, $path->getSeparator());
    }

    public function rootPathsProvider()
    {
        return [
            [Path::parse('/', Path::PLATFORM_UNIX),],
            [Path::parse('c:\\', Path::PLATFORM_WINDOWS),],
            [Path::parse('\\', Path::PLATFORM_WINDOWS),],
        ];
    }

    /**
     * @test
     *
     * @dataProvider rootPathsProvider
     *
     * @param Path $path
     */
    public function shouldNotParseRootPathAsSingleEmptySegment(Path $path)
    {
        $this->assertEquals([], $path->getSegments());
    }
}
