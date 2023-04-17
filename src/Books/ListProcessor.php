<?php

declare(strict_types=1);

namespace Twent\GeneratorsFibers\Books;

final class ListProcessor
{
    public static function map(callable $fn, iterable $data): iterable
    {
        foreach ($data as $rowData) {
            yield $fn($rowData);
        }
    }

    public static function filter(callable $fn, iterable $data): iterable
    {
        foreach ($data as $rowData) {
            if ($fn($rowData)) {
                yield $rowData;
            }
        }
    }
}
