# Pathetic

[![Packagist](https://img.shields.io/packagist/v/ama-team/pathetic.svg?style=flat-square)](https://packagist.org/packages/ama-team/pathetic)
[![AppVeyor/Master](https://img.shields.io/appveyor/ci/etki/pathetic/master.svg?style=flat-square)](https://ci.appveyor.com/project/etki/pathetic)
[![CircleCI/Master](https://img.shields.io/circleci/project/github/ama-team/pathetic/master.svg?style=flat-square)](https://circleci.com/gh/ama-team/pathetic/tree/master)
[![Scrutinizer/Master](https://img.shields.io/scrutinizer/g/ama-team/pathetic/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/ama-team/pathetic?branch=dev)
[![Code Climate](https://img.shields.io/codeclimate/github/ama-team/pathetic.svg?style=flat-square)](https://codeclimate.com/github/ama-team/pathetic)
[![Coveralls/Master](https://img.shields.io/coveralls/ama-team/pathetic/master.svg?style=flat-square)](https://coveralls.io/github/ama-team/pathetic?branch=master)


Pathetic is a simple PHP library consisting just of couple of classes. 
It is aimed to help with platform-independent path work, so you can 
run the same code, fnmatch checks and comparisons regardless of 
specific machine your project is running on.

And yes, it is influenced by `java.nio.Path`.

## Installation

```bash
composer require ama-team/pathetic
```

## Usage

You start with classic string and `Path::parse` method:

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

Except for those basic operations, Pathetic allows basic path 
normalization, path concatenation (resolution), path relativization 
and path comparison.

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

At last, there are some helper methods you may want to use:

```php
$path = Path::parse('file://c:/node/directory', Path::PLATFORM_WINDOWS);
$path = $path->withoutScheme()->withRoot('d:');
echo $path->getRoot(); # d:
echo $path->getScheme(); # empty string
echo $path->getSeparator(); # \
```

### Major notes

All path operations are non-destructive, and all path instances are
immutable - whenever `#normalize()`, `#relativize()` or `#withRoot()`
are called, new object is created instead of modifying old one.

There is edge case with current directory - while one may expect
that normalized relative path of current directory will render down
to dot (`'.'`), this won't happen - it will be rendered to empty string
(`''`). However, while you don't call for normalization, your path will 
stay as-is.

Windows has two types of absolute paths - with and without drive 
letter, (`\Users` and `C:\Users`, for example). Both types are 
treated as absolute by Pathetic - it's up to end user to determine if
he or she has to specify drive letter to not to inherit it from current
working directory. This is, of course, a drawback, but unless that 
absolute path is inherited from user input - which should be 
intentional thing - that shouldn't happen.

### Dev branch shield cellar

[![AppVeyor/Dev](https://img.shields.io/appveyor/ci/etki/pathetic/dev.svg?style=flat-square)](https://ci.appveyor.com/project/etki/pathetic)
[![CircleCI/Dev](https://img.shields.io/circleci/project/github/ama-team/pathetic/dev.svg?style=flat-square)](https://circleci.com/gh/ama-team/pathetic/tree/dev)
[![Scrutinizer/Dev](https://img.shields.io/scrutinizer/g/ama-team/pathetic/dev.svg?style=flat-square)](https://scrutinizer-ci.com/g/ama-team/pathetic/?branch=dev)
[![Coveralls/Dev](https://img.shields.io/coveralls/ama-team/pathetic/dev.svg?style=flat-square)](https://coveralls.io/github/ama-team/pathetic?branch=dev)
