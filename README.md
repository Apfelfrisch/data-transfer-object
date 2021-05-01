# Simple Dto

![Unit Test](https://github.com/Apfelfrisch/data-transfer-object/actions/workflows/phpunit.yml/badge.svg)
![Static Analysis](https://github.com/Apfelfrisch/data-transfer-object/actions/workflows/psalm.yml/badge.svg)

This package is heavily inspired by [spatie/data-transfer-object](https://github.com/spatie/data-transfer-object). The main difference is that the DataTransferObject doesn't come with a constructor. That make the initialization less magical which is a little nicer for static analysis.
## Usage

Here's what a DTO looks like:

```php
use Apfelfrisch\DataTransferObject;

class MyDTO extends DataTransferObject
{
    public function __construct(
        public int $a,

        public float $b,

        public OtherDTO $otherDTO,
        
        #[DateTimeCast]
        public DateTime $date,
    ) { }
}
```
You could construct this DTO with Attribute casting like so:

```php
$dto = MyDTO::fromArrayWithCast([
    'a' => 1,
    'b' => 2.2,
    'otherDTO' => ['id' => 3],
    'date' => '2021-05-01'
);
```
