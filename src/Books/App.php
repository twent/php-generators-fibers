<?php

declare(strict_types=1);

namespace Twent\GeneratorsFibers\Books;

use Exception;

final class App
{
    public function __construct(
        public readonly BookProvider $bookProvider = new BookProvider()
    ) {
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $books = $this->bookProvider->load();

        $books = ListProcessor::filter(
            static fn($book) => $book->price > 9_000,
            ListProcessor::map(json_decode(...), $books)
        );

        $this->print($books);
    }

    private function print(iterable $books): void
    {
        echo "Цена | Наименование\n";
        echo str_repeat('_', 100) . "\n";

        foreach ($books as $book) {
            echo "{$book->price} | {$book->title}\n";
        }
    }
}
