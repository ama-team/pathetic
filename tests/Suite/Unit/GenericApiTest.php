<?php

namespace AmaTeam\Pathetic\Test\Suite\Unit;

use AmaTeam\Pathetic\Path;
use AmaTeam\Pathetic\Test\Support\StringContainer;
use AmaTeam\Pathetic\Test\Support\Test;

/**
 * @author Etki <etki@etki.me>
 */
class GenericApiTest extends Test
{
    /**
     * @test
     */
    public function shouldAllowSchemeChanges()
    {
        /** @var Path $path */
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
     */
    public function shouldAllowRootChanges()
    {
        /** @var Path $path */
        $path = Path::parse('node', Path::PLATFORM_WINDOWS);
        $this->assertNull($path->getRoot());
        $root = 'c:';
        $path = $path->withRoot($root);
        $this->assertEquals($root, $path->getRoot());
        $path = $path->withoutRoot();
        $this->assertNull($path->getRoot());
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
