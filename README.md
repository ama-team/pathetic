# Pathetic

[![Packagist](https://img.shields.io/packagist/v/ama-team/pathetic.svg?style=flat-square)](https://packagist.org/packages/ama-team/pathetic)
[![AppVeyor/Master](https://img.shields.io/appveyor/ci/etki/pathetic/master.svg?style=flat-square)](https://ci.appveyor.com/project/etki/pathetic)
[![CircleCI/Master](https://img.shields.io/circleci/project/github/ama-team/pathetic/master.svg?style=flat-square)](https://circleci.com/gh/ama-team/pathetic/tree/master)
[![Scrutinizer/Master](https://img.shields.io/scrutinizer/g/ama-team/pathetic/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/ama-team/pathetic)
[![Code Climate](https://img.shields.io/codeclimate/github/ama-team/pathetic.svg?style=flat-square)](https://codeclimate.com/github/ama-team/pathetic)
[![Coveralls/Master](https://img.shields.io/coveralls/ama-team/pathetic/master.svg?style=flat-square)](https://coveralls.io/github/ama-team/pathetic)


This is simple PHP library consisting just of couple of classes. It is
aimed to help with platform-independent path work, so you can run the 
same code, fnmatch checks and comparisons regardless of specific 
machine your code is running on.

And yes, it is influenced by `java.nio.Path`.

## Installation

```bash
composer require ama-team/pathetic
```

## Usage

Everything whirls around `Path` class that is usually created via 
`Path::parse()` call:

```php
use AmaTeam\Pathetic\Path;

$path = Path::parse('beverages/soda');
$path = Path::parse('file://beverages/soda');
$path = Path::parse('c:\\beverages\\soda', Path::PLATFORM_WINDOWS);
$path = Path::parse('c:/beverages/soda', Path::PLATFORM_WINDOWS);
$path = Path::parse('custom-scheme://c:/beverages/soda', Path::PLATFORM_WINDOWS);
```

Second argument should be used only when you operate with paths for 
specific platform - by default, it is calculated automatically.

After you've obtained path instance, you can simply convert it to
string to get consistent-delimiter representation:

```php
$path = Path::parse('file://beverages\\soda');
echo (string) $path; // file://beverages/soda
```

This will save you from awkward moments when you append 
`directory/file` to windows path and then try to compare it with path
received from OS (which will contain `directory\file` instead), also,
it makes it pretty easy to use `fnmatch` glob patterns 
platform-independently.

If you ever to need platform-consistent representation, you may use
`toPlatformString()` method:

```php
echo $path->toPlatformString(); // file://beverages\\soda
```

Except for those basic operations, Pathetic allows basic path normalization,
path concatenation, path relativization and path comparing.

```php
$path = Path::parse('/node/directory//./../leaf');
echo (string) $path; # /node/directory//./../leaf
echo $path->normalize(); # /node/leaf
$path->isAbsolute(); # true

$node = Path::parse('/node');
$leaf = Path::parse('leaf');
$other = $node->resolve($leaf); # /node/leaf
$path->equals($other); # true
$path->isChildOf($node); # true
echo (string) $path->getParent(); # /node
echo (string) $node->getChild('leaf') # /node/leaf
echo (string) $node->relativize($path); # leaf
$path->isSiblingOf($other); # true
foreach ($path->iterator() as $entry) {
    echo (string) $entry;
    # /
    # /node
    # /node/leaf
}
```

There are some edge cases, of course. When path is rendered down, it
may be completely empty.

At last, there are some helper methods you may want to use:

```php
$path = Path::parse('file://c:/node/directory', Path::PLATFORM_WINDOWS);
$path = $path->withoutScheme();
echo $path->getRoot(); # empty string
echo $path->getSeparator(); # \
```

### Dev branch shield cellar


[![Packagist](https://img.shields.io/packagist/v/ama-team/pathetic.svg?style=flat-square)](https://packagist.org/packages/ama-team/pathetic)
[![AppVeyor/Dev](https://img.shields.io/appveyor/ci/etki/pathetic/dev.svg?style=flat-square)](https://ci.appveyor.com/project/etki/pathetic)
[![CircleCI/Dev](https://img.shields.io/circleci/project/github/ama-team/pathetic/master.svg?style=flat-square)](https://circleci.com/gh/ama-team/pathetic/tree/dev)
[![Scrutinizer/Master](https://img.shields.io/scrutinizer/g/ama-team/pathetic/dev.svg?style=flat-square)](https://scrutinizer-ci.com/g/ama-team/pathetic)
[![Coveralls/Master](https://img.shields.io/coveralls/ama-team/pathetic/master.svg?style=flat-square)](https://coveralls.io/github/ama-team/pathetic?branch=dev)