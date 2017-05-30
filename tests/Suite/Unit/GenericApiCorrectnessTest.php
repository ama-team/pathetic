<?php

namespace AmaTeam\Pathetic\Test\Suite\Unit;

use AmaTeam\Pathetic\Path;
use AmaTeam\Pathetic\Test\Support\StringContainer;
use Codeception\Test\Unit;

/**
 * @author Etki <etki@etki.me>
 */
class GenericApiCorrectnessTest extends Unit
{
    /**
     * @test
     */
    public function shouldAllowSchemeChange()
    {
        $path = Path::parse('node');
        $this->assertNull($path->getScheme());
        $scheme = 'file';
        $path = $path->withScheme($scheme);
        $this->assertEquals($scheme, $path->getScheme());
        $path = $path->withoutScheme();
        $this->assertNull($path->getScheme());
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function shouldNotAllowRootParentRequest()
    {
        $path = Path::parse('/', Path::PLATFORM_UNIX);
        $path->getParent();
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function shouldThrowOnInvalidInput()
    {
        $path = Path::parse('/', Path::PLATFORM_UNIX);
        $path->isChildOf(null);
    }

    /**
     * @test
     */
    public function shouldConvertStringifiableObject()
    {
        $path = Path::parse('/', Path::PLATFORM_UNIX);
        // should not throw
        $this->assertTrue($path->isParentOf(new StringContainer('/node')));
    }
}
