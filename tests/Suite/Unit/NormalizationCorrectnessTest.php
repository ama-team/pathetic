<?php

namespace AmaTeam\Pathetic\Test\Suite\Unit;

use AmaTeam\Pathetic\Path;
use Codeception\Test\Unit;

/**
 * @author Etki <etki@etki.me>
 */
class NormalizationCorrectnessTest extends Unit
{
    public function dataProvider()
    {
        return [
            ['node/leaf', 'node/leaf',],
            ['node/./leaf', 'node/leaf',],
            ['node/leaf/.', 'node/leaf',],
            ['./node/leaf', 'node/leaf',],
            ['./node/../leaf', 'leaf',],
            ['node/../../leaf', '../leaf',],
            ['node/////./////leaf', 'node/leaf',],
        ];
    }

    /**
     * @test
     *
     * @dataProvider dataProvider
     *
     * @param string $input
     * @param string $expectation
     */
    public function shouldNormalizeAsExpected($input, $expectation)
    {
        $path = Path::parse($input)->normalize();
        $this->assertEquals($expectation, (string) $path);
    }
}
