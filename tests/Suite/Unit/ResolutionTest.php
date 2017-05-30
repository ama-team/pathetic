<?php

namespace AmaTeam\Pathetic\Test\Suite\Unit;

use AmaTeam\Pathetic\Path;
use Codeception\Test\Unit;

/**
 * @author Etki <etki@etki.me>
 */
class ResolutionTest extends Unit
{
    public function resolutionDataProvider()
    {
        $u = Path::PLATFORM_UNIX;
        $w = Path::PLATFORM_WINDOWS;
        return [
            [$u, 'node', 'leaf', 'node/leaf',],
            [$u, '/node', 'leaf', '/node/leaf',],
            [$u, '/node', '/leaf', '/leaf',],
            [$u, 'node', '/leaf', '/leaf',],
            
            [$w, 'node', 'leaf', 'node/leaf',],
            [$w, 'c:/node', 'leaf', 'c:/node/leaf',],
            [$w, 'c:/node', 'c:/leaf', 'c:/leaf',],
            [$w, 'node', 'c:/leaf', 'c:/leaf',],
        ];
    }

    public function relativizationDataProvider()
    {
        $u = Path::PLATFORM_UNIX;
        $w = Path::PLATFORM_WINDOWS;
        return [
            [$u, 'node', 'node/leaf', 'leaf',],
            [$u, '/node', 'node/leaf', 'node/leaf'],
            [$u, 'node', '/node/leaf', '/node/leaf',],
            [$u, '/node', '/node/leaf', 'leaf',],
            [$u, 'node/leaf', 'node/node/leaf', '../node/leaf',],
            [$u, 'node/node/leaf', 'node/leaf', '../../leaf',],
            [$u, 'node/node/leaf', 'node/node', '..',],

            [$w, 'node', 'node/leaf', 'leaf',],
            [$w, 'c:/node', 'node/leaf', 'node/leaf'],
            [$w, 'node', 'c:/node/leaf', 'c:/node/leaf',],
            [$w, 'c:/node', 'c:/node/leaf', 'leaf',],
            [$w, 'node/leaf', 'node/node/leaf', '../node/leaf',],
            [$w, 'node/node/leaf', 'node/leaf', '../../leaf',],
            [$w, 'node/node/leaf', 'node/node', '..',],
        ];
    }

    /**
     * @test
     *
     * @dataProvider resolutionDataProvider
     *
     * @param string $platform
     * @param string $path
     * @param string $other
     * @param string $expectation
     */
    public function shouldResolvePath($platform, $path, $other, $expectation)
    {
        $path = Path::parse($path, $platform);
        $other = Path::parse($other, $platform);
        $this->assertEquals($expectation, (string) $path->resolve($other));
    }

    /**
     * @test
     *
     * @dataProvider relativizationDataProvider
     *
     * @param string $platform
     * @param string $path
     * @param string $other
     * @param string $expectation
     */
    public function shouldRelativizePath($platform, $path, $other, $expectation)
    {
        $path = Path::parse($path, $platform);
        $other = Path::parse($other, $platform);
        $this->assertEquals($expectation, (string) $path->relativize($other));
    }
}
