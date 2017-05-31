<?php

namespace AmaTeam\Pathetic\Test\Suite\Unit;

use AmaTeam\Pathetic\Path;
use AmaTeam\Pathetic\Test\Support\Test;

/**
 * @author Etki <etki@etki.me>
 */
class ComparisonTest extends Test
{
    public function dataProvider()
    {
        return [
            ['node', 'node', 0,],
            ['node/leaf', 'node', 1,],
            ['node', 'node/leaf', -1,],
            ['node/b', 'node/a', 1,],
            ['node/a', 'node/b', -1,],
            ['/node', 'node', 1,],
            ['/node', '/node', 0,],
            ['file://node', 'node', 1,],
            ['file://node', 'file://node', 0,],
        ];
    }

    /**
     * @test
     *
     * @dataProvider dataProvider
     *
     * @param string $left
     * @param string $right
     * @param int $result
     */
    public function shouldConformToCommonComparisonRules($left, $right, $result)
    {
        $left = Path::parse($left);
        $right = Path::parse($right);
        $this->assertEquals($result, $left->compareTo($right));
        $this->assertEquals($result === 0, $left->equals($right));
    }

    /**
     * @test
     */
    public function shouldCorrectlyCompareToNull()
    {
        $path = Path::parse('node');
        $this->assertGreaterThan(0, $path->compareTo(null));
        $this->assertFalse($path->equals(null));
    }
}
