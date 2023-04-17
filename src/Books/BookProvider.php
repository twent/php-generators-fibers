<?php

declare(strict_types=1);

namespace Twent\GeneratorsFibers\Books;

use Exception;

final class BookProvider
{
    /**
     * @throws Exception
     */
    public function load(): iterable
    {
        $fh = fopen(__DIR__ . '/../../data/books.json', 'rb');

        while ($line = fgets($fh)) {
            // Имитация работы с внешним API
            time_nanosleep(0, random_int(20_000_000, 40_000_000));
            yield $line;
        }

        fclose($fh);
    }
}
