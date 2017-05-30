<?php

namespace AmaTeam\Pathetic;

use ArrayIterator;
use Iterator;
use RuntimeException;

/**
 * This class represents abstract filesystem path - regardless of underlying
 * operating system. It is represented just as (sometimes) scheme, (sometimes)
 * root point, and a list of segments of which path consists. It provides lots
 * of auxiliary methods (like hierarchy comparison or paths resolution) that
 * help to deal with paths in an OS-independent way, which, i hope, will result
 * in easier pathwork in any library.
 *
 * @author Etki <etki@etki.me>
 */
class Path
{
    const PLATFORM_UNIX = 'unix';
    const PLATFORM_WINDOWS = 'windows';

    /**
     * Separator that is used to render platform-independent representations.
     */
    const SEPARATOR = '/';

    /**
     * Path scheme, if any was used.
     *
     * @var string|null
     */
    private $scheme;

    /**
     * Path root. May be null (relative path), empty string (unix root or
     * windows path \like\that) or drive name (windows-only).
     *
     * @var string|null
     */
    private $root;

    /**
     * List of path segments.
     *
     * @var string[]
     */
    private $segments;

    /**
     * Separator that will be used to assemble path back.
     *
     * @var string
     */
    private $separator;

    /**
     * @param string|null $scheme
     * @param string|null $root
     * @param string[] $segments
     * @param string $separator
     */
    public function __construct($scheme, $root, array $segments, $separator)
    {
        $this->scheme = $scheme;
        $this->root = $root;
        $this->segments = $segments;
        $this->separator = $separator;
    }

    public static function parse($input, $platform = null)
    {
        $platform = $platform ?: static::detectPlatform();
        $separator = $platform === static::PLATFORM_UNIX ? '/' : '\\';
        $scheme = static::extractScheme($input);
        $scheme = empty($scheme) ? null : $scheme;
        $path = static::stripScheme($input);
        if ($platform === static::PLATFORM_WINDOWS) {
            $path = str_replace('\\', '/', $path);
        }
        $segments = $path === '' ? [] : explode('/', $path);
        $root = null;
        if (static::containsRoot($segments, $platform)) {
            $root = array_shift($segments);
            $segments = $segments === [''] ? [] : $segments;
        }
        return new static($scheme, $root, $segments, $separator);
    }

    protected static function extractScheme($input)
    {
        $position = strpos($input, '://');
        if ($position === false) {
            return null;
        }
        return substr($input, 0, $position);
    }

    protected static function stripScheme($input)
    {
        $position = strpos($input, '://');
        if ($position === false) {
            return $input;
        }
        return substr($input, $position + 3);
    }

    protected static function containsRoot(array $segments, $platform)
    {
        if (empty($segments)) {
            return false;
        }
        if ($segments[0] === '') {
            return true;
        }
        if ($platform === static::PLATFORM_UNIX) {
            return false;
        }
        return strpos($segments[0], ':') > 0;
    }

    /**
     * Returns platform matching the separator. Exists for 100% coverage :P
     *
     * @param string $separator
     * @return string
     */
    public static function getPlatformBySeparator($separator)
    {
        switch ($separator) {
            case '/':
                return static::PLATFORM_UNIX;
            case '\\':
                return static::PLATFORM_WINDOWS;
            default:
                $format = 'Unknown directory separator %s';
                throw new RuntimeException(sprintf($format, $separator));
        }
    }

    /**
     * Detects current platform.
     *
     * @return string
     */
    public static function detectPlatform()
    {
        return static::getPlatformBySeparator(DIRECTORY_SEPARATOR);
    }

    /**
     * Converts input into path. Throws runtime exception if that's not
     * possible.
     *
     * @param Path|string|object $input
     * @return Path|null
     */
    protected function tryAdapt($input)
    {
        if ($input instanceof Path) {
            return $input;
        }
        if (is_object($input)) {
            if (method_exists($input, '__toString')) {
                $input = $input->__toString();
            }
        }
        return is_string($input) ? static::parse($input) : null;
    }

    /**
     * Converts input into path. Throws runtime exception if that's not
     * possible.
     *
     * @param Path|string|object $input
     * @return Path
     */
    protected function adapt($input)
    {
        $input = $this->tryAdapt($input);
        if ($input === null) {
            throw new RuntimeException('Invalid input provided');
        }
        return $input;
    }

    /**
     * Creates new path, destroying as much empty ('') and dot-entries
     * ('.', '..') as possible, so path 'node/./../leaf' will be truncated
     * just to leaf, but 'node/../../leaf' would result in '../leaf'.
     *
     * @return Path
     */
    public function normalize()
    {
        $reducer = function ($carrier, $segment) {
            if ($segment === '' || $segment === '.') {
                return $carrier;
            }
            if ($segment === '..' && !empty($carrier) && end($carrier) !== '..') {
                array_shift($carrier);
                return $carrier;
            }
            $carrier[] = $segment;
            return $carrier;
        };
        $copy = clone $this;
        $copy->segments = array_reduce($this->segments, $reducer, []);
        return $copy;
    }

    /**
     * Returns hierarchy branch from the topmost node (root, if present) down
     * to current node, so `/a/b/c` will result in `/a`, `/a/b` and `/a/b/c`,
     * and `a/b` in `a` and `a/b`.
     *
     * @return Path[]
     */
    public function enumerate()
    {
        $accumulator = [];
        $results = [];
        $normalized = $this->normalize();
        foreach ($normalized->segments as $segment) {
            $copy = clone $normalized;
            $copy->segments = $accumulator;
            $results[] = $copy;
            $accumulator[] = $segment;
        }
        $results[] = $this;
        return $results;
    }

    /**
     * Returns iterator that will iterate hierarchy branch according to
     * {@link #enumerate} rules.
     *
     * @return Iterator<Path>
     */
    public function iterator()
    {
        return new ArrayIterator($this->enumerate());
    }

    /**
     * Returns list of path parents, starting from the topmost one and going
     * down one by one, similar to {@link #enumerate}.
     *
     * @return Path[]
     */
    public function getParents()
    {
        $enumeration = $this->enumerate();
        array_pop($enumeration);
        return $enumeration;
    }

    /**
     * @return bool
     */
    public function isAbsolute()
    {
        return $this->root !== null;
    }

    /**
     * @return bool
     */
    public function isRelative()
    {
        return $this->root === null;
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return $this->root !== null && empty($this->segments);
    }

    /**
     * Returns true if $other is located on the same hierarchy branch higher
     * that current path.
     *
     * If path schemes differ, or not both paths are relative/absolute, this
     * method will instantly return false.
     *
     * @param Path|string|object $other
     * @return bool
     */
    public function isDescendantOf($other)
    {
        $other = $this->adapt($other)->normalize();
        $current = $this->normalize();
        if ($current->scheme !== $other->scheme) {
            return false;
        }
        if ($current->root !== $other->root) {
            return false;
        }
        if (sizeof($current->segments) <= sizeof($other->segments)) {
            return false;
        }
        for ($i = 0; $i < sizeof($other->segments); $i++) {
            if ($other->segments[$i] !== $current->segments[$i]) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns true if $other is located on the same hierarchy branch deeper
     * that current path.
     *
     * If path schemes differ, or not both paths are relative/absolute, this
     * method will instantly return false.
     *
     * @param Path|string|object $other
     * @return bool
     */
    public function isAncestorOf($other)
    {
        return $this->adapt($other)->isDescendantOf($this);
    }

    /**
     * Returns true if $other is direct parent of current path.
     *
     * If path schemes differ, or not both paths are relative/absolute, this
     * method will instantly return false.
     *
     * @param $other
     * @return bool
     */
    public function isChildOf($other)
    {
        $other = $this->adapt($other)->normalize();
        $current = $this->normalize();
        if (sizeof($other->segments) !== sizeof($current->segments) - 1) {
            return false;
        }
        return $current->isDescendantOf($other);
    }

    /**
     * Returns true if $other is direct child of current path.
     *
     * If path schemes differ, or not both paths are relative/absolute, this
     * method will instantly return false.
     *
     * @param $other
     * @return bool
     */
    public function isParentOf($other)
    {
        return $this->adapt($other)->isChildOf($this);
    }

    /**
     * Returns true if $other is located on the smae hierarchy level.
     *
     * If path schemes differ, or not both paths are relative/absolute, this
     * method will instantly return false.
     *
     * @param $other
     * @return bool
     */
    public function isSiblingOf($other)
    {
        $other = $this->adapt($other);
        if ($other->isRoot()) {
            return $this->isRoot() && $this->root === $other->root;
        }
        return $other->getParent()->isParentOf($this);
    }

    /**
     * Returns direct parent of current path. In case of call on root node
     * exception will be thrown.
     *
     * @return Path
     */
    public function getParent()
    {
        if ($this->isRoot()) {
            throw new RuntimeException('Root cannot have parent node');
        }
        $copy = clone $this;
        $copy->segments[] = '..';
        return $copy->normalize();
    }

    /**
     * Resolves other path against current one (or, in other words, prepends
     * current path to provided one). If provided path is absolute, it is
     * returned as-is, otherwise it is appended to current one:
     *
     * /a/b resolve /c/d => /b/c
     * /a/b resolve c/d => /a/b/c/d
     * a/b resolve c/d => a/b/c/d
     *
     * @param Path|string|object $other
     *
     * @return Path
     */
    public function resolve($other)
    {
        if ($other->isAbsolute()) {
            return $other;
        }
        $copy = clone $this;
        $copy->segments = array_merge($copy->segments, $other->segments);
        return $copy;
    }

    /**
     * Construct a relative path, trying to subtract current path from provided
     * one (thus providing a path required to traverse from current one to
     * provided one). If called with an absolute path against relative or vice
     * versa, will return other path as is.
     *
     * @param Path|string|object $other
     *
     * @return Path
     */
    public function relativize($other)
    {
        if ($other->root !== $this->root) {
            return $other;
        }
        $current = $this->normalize();
        $other = $other->normalize();
        $counter = 0;
        for ($i = 0; $i < sizeof($current->segments); $i++) {
            if (!isset($other->segments[$i])) {
                break;
            }
            if ($current->segments[$i] !== $other->segments[$i]) {
                break;
            }
            $counter++;
        }
        $traversal = array_fill(0, sizeof($current->segments) - $counter, '..');
        $slice = array_slice($other->segments, $counter);
        $copy = clone $this;
        $copy->root = null;
        $copy->segments = array_merge($traversal, $slice);
        return $copy;
    }

    /**
     * Lexicographically compares one path to another.
     *
     * @param Path|string|object $other
     * @return int
     */
    public function compareTo($other)
    {
        $other = $this->tryAdapt($other);
        if ($other === null) {
            return 1;
        }
        $other = $other->normalize();
        $current = $this->normalize();
        $schemes = $this->compareStrings($current->scheme, $other->scheme);
        if ($schemes !== 0) {
            return $schemes;
        }
        $roots = $this->compareStrings($current->root, $other->root);
        if ($roots !== 0) {
            return $roots;
        }
        $limit = max(sizeof($current->segments), sizeof($other->segments));
        for ($i = 0; $i < $limit; $i++) {
            if (!isset($current->segments[$i])) {
                return -1;
            } else if (!isset($other->segments[$i])) {
                return 1;
            } else if ($current->segments[$i] === $other->segments[$i]) {
                continue;
            }
            return strcmp($current->segments[$i], $other->segments[$i]);
        }
        return 0;
    }

    /**
     * Helper method for {@link #compareTo()}
     *
     * @param string|null $left
     * @param string|null $right
     * @return int
     */
    protected function compareStrings($left, $right)
    {
        if ($left === null) {
            return $right === null ? 0 : -1;
        }
        if ($right === null) {
            return 1;
        }
        return strcmp($left, $right);
    }

    /**
     * Compares current path to provided one and tells if they are equal.
     *
     * @param Path|string|object|null $other
     * @return bool
     */
    public function equals($other)
    {
        return $this->compareTo($other) === 0;
    }

    /**
     * @return string|null
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return string|null
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return string[]
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * @param string|null $scheme
     * @return Path
     */
    public function withScheme($scheme)
    {
        $copy = clone ($this);
        $copy->scheme = $scheme;
        return $copy;
    }

    /**
     * @return Path
     */
    public function withoutScheme()
    {
        return $this->withScheme(null);
    }

    /**
     * Assembles string representation using provided separator.
     *
     * @param string $separator
     * @return string
     */
    protected function assemble($separator)
    {
        $builder = '';
        if (!empty($this->scheme)) {
            $builder .= $this->scheme . '://';
        }
        if ($this->root !== null) {
            $builder .= $this->root . $separator;
        }
        return $builder . implode($separator, $this->segments);
    }

    /**
     * Returns platform-independent representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->assemble(static::SEPARATOR);
    }

    /**
     * Returns representation for target platform.
     *
     * @return string
     */
    public function toPlatformString()
    {
        return $this->assemble($this->separator);
    }
}
