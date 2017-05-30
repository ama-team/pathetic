<?php

namespace AmaTeam\Pathetic\Test\Suite\Unit;

use AmaTeam\Pathetic\Path;
use Codeception\Test\Unit;

/**
 * @author Etki <etki@etki.me>
 */
class RenderCorrectnessTest extends Unit
{
    public function dataProvider()
    {
        $u = Path::PLATFORM_UNIX;
        $w = Path::PLATFORM_WINDOWS;
        // input | platform | as string | as platform string
        return [
            ['', $u, '', '',],
            ['node', $u, 'node', 'node',],
            ['node/leaf', $u, 'node/leaf', 'node/leaf',],
            ['node/leaf\\leaf', $u, 'node/leaf\\leaf', 'node/leaf\\leaf',],
            ['/node/leaf', $u, '/node/leaf', '/node/leaf',],
            ['file://node/leaf', $u, 'file://node/leaf', 'file://node/leaf',],
            ['file:///node/leaf', $u, 'file:///node/leaf', 'file:///node/leaf',],

            ['', $w, '', '',],
            ['node', $w, 'node', 'node',],
            ['node\\leaf', $w, 'node/leaf', 'node\\leaf',],
            ['node/leaf', $w, 'node/leaf', 'node\\leaf',],
            ['c:\\node\\leaf', $w, 'c:/node/leaf', 'c:\\node\\leaf',],
            ['c:/node/leaf', $w, 'c:/node/leaf', 'c:\\node\\leaf',],
        ];
    }

    /**
     * @test
     *
     * @dataProvider dataProvider
     */
    public function shouldRenderExactlyAsExpected(
        $input,
        $platform,
        $asString,
        $asPlatformString
    ) {
        $path = Path::parse($input, $platform);
        $this->assertEquals($asString, (string) $path);
        $this->assertEquals($asPlatformString, $path->toPlatformString());
    }
}
