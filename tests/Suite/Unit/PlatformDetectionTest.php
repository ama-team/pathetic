<?php

namespace AmaTeam\Pathetic\Test\Suite\Unit;

use AmaTeam\Pathetic\Path;
use Codeception\Test\Unit;

/**
 * Oh yeah i like 100% coverage.
 *
 * @author Etki <etki@etki.me>
 */
class PlatformDetectionTest extends Unit
{
    /**
     * @test
     */
    public function shouldCorrectlyDetectPlatform()
    {
        $this->assertEquals(
            Path::PLATFORM_UNIX,
            Path::getPlatformBySeparator('/')
        );
        $this->assertEquals(
            Path::PLATFORM_WINDOWS,
            Path::getPlatformBySeparator('\\')
        );
        $expected = DIRECTORY_SEPARATOR === '/' ? Path::PLATFORM_UNIX : Path::PLATFORM_WINDOWS;
        $this->assertEquals($expected, Path::detectPlatform());
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function shouldThrowOnUnknownSeparator()
    {
        Path::getPlatformBySeparator(' ');
    }
}
