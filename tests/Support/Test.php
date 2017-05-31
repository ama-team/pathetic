<?php

namespace AmaTeam\Pathetic\Test\Support;

use Codeception\Test\Unit;

/**
 * Helper class to allow allure-codeception to work.
 *
 * @author Etki <etki@etki.me>
 */
class Test extends Unit
{
    public function getTestClass()
    {
        return $this;
    }
}
