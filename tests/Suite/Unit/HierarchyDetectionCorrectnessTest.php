<?php

namespace AmaTeam\Pathetic\Test\Suite\Unit;

use AmaTeam\Pathetic\Path;
use Codeception\Test\Unit;

/**
 * @author Etki <etki@etki.me>
 */
class HierarchyDetectionCorrectnessTest extends Unit
{
    public function dataProvider()
    {
        // path | other | intmask: ancestor, parent, sibling, child, descendant
        return [
            ['node', 'leaf', [0, 0, 1, 0, 0,],],
            ['node', 'node', [0, 0, 1, 0, 0,],],
            ['node/leaf', 'node', [0, 0, 0, 1, 1,],],
            ['node/node/leaf', 'node', [0, 0, 0, 0, 1,],],
            ['node/node/leaf', 'node/leaf', [0, 0, 0, 0, 0,],],
            ['node', 'node/leaf', [1, 1, 0, 0, 0,],],
            ['node', 'node/node/leaf', [1, 0, 0, 0, 0,],],
            ['node', 'node/node/..//./leaf', [1, 1, 0, 0, 0,],],
            ['/', '/', [0, 0, 1, 0, 0,],],
        ];
    }

    /**
     * @test
     *
     * @dataProvider dataProvider
     *
     * @param $path
     * @param $other
     * @param $mask
     */
    public function shouldMatchExpectedHierarchy($path, $other, $mask)
    {
        $subject = Path::parse($path);
        $other = Path::parse($other);
        $this->assertEquals((bool) $mask[0], $subject->isAncestorOf($other));
        $this->assertEquals((bool) $mask[1], $subject->isParentOf($other));
        $this->assertEquals((bool) $mask[2], $subject->isSiblingOf($other));
        $this->assertEquals((bool) $mask[3], $subject->isChildOf($other));
        $this->assertEquals((bool) $mask[4], $subject->isDescendantOf($other));
    }

    /**
     * @test
     */
    public function shouldNotEstablishHierarchyWithDifferentSchemes()
    {
        $path = Path::parse('file:///leaf');
        $other = Path::parse('local:///leaf');
        $this->assertFalse($path->isAncestorOf($other));
        $this->assertFalse($path->isParentOf($other));
        $this->assertFalse($path->isSiblingOf($other));
        $this->assertFalse($path->isChildOf($other));
        $this->assertFalse($path->isDescendantOf($other));
    }

    /**
     * @test
     */
    public function shouldNoEstablishHierarchyWithDifferentRoots()
    {
        $path = Path::parse('c:/leaf', Path::PLATFORM_WINDOWS);
        $other = Path::parse('d:/leaf', Path::PLATFORM_WINDOWS);
        $this->assertFalse($path->isAncestorOf($other));
        $this->assertFalse($path->isParentOf($other));
        $this->assertFalse($path->isSiblingOf($other));
        $this->assertFalse($path->isChildOf($other));
        $this->assertFalse($path->isDescendantOf($other));
    }
}
