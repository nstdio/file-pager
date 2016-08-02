# file-pager

This is a simple extension is written in PHP intended to divide a file into pages.

# Installation
```
$ composer require nstdio/file-pager: "dev-master"
```
or add

```
"nstdio/file-pager": "dev-master"
```

to the `require` section of your `composer.json` file.

# Usage
```php
<?php
use nstdio\FilePager;

$fileName = "path/to/file";
$pageSize = 25; // lines count on page.

$pager = new FilePager($fileName, $pageSize);
$pager->setLineSeparator(LineSeparator::HTML); // All control characters will be trimmed out.

$pager->prependLine('#{line}') // prepend string to line. Available tokens {line}, {pageLine}, {path}, {file}, {dir}, {page}.
      ->append("{page}.");

echp $pager->getPage(1);
```
