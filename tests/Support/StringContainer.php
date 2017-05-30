<?php

namespace AmaTeam\Pathetic\Test\Support;

/**
 * @author Etki <etki@etki.me>
 */
class StringContainer
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    function __toString()
    {
        return $this->value;
    }
}
