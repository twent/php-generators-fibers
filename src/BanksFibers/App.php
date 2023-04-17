<?php

declare(strict_types=1);

namespace Twent\GeneratorsFibers\BanksFibers;

use Fiber;
use Throwable;

final class App
{
    /**
     * @throws Throwable
     */
    public function run(): void
    {
        $banks = [
            new BankGateway("Зеленый банк"),
            new BankGateway("Желтый банк"),
            new BankGateway("Красный банк"),
            new BankGateway("Фиолетовый банк"),
            new BankGateway("Оранжевый банк"),
        ];

        // Отправляем заявки и ставим обработчики в очередь
        $queue = [];

        foreach ($banks as $id => $bank) {
            $queue[$id] = new Fiber(static fn() => $bank->request(10_000));
            $queue[$id]->start();
            echo "{$bank->name}: Отправлен запрос в API\r\n";
        }

        // Делаем по 10 проверок готовности в секунду * 3 секунды
        for ($i = 0; $i < 30; $i++) {
            time_nanosleep(0, 100_000_000);
            foreach ($queue as $id => $item) {
                // Спрашиваем у обработчика: "Ну как?"
                $item->resume('check');
                // Если всё готово — выводим ответ банка и удаляем обработчик из очереди
                if ($item->isTerminated()) {
                    echo $item->getReturn().PHP_EOL;
                    unset($queue[$id]);
                }
            }
        }

        // 3 секунды прошло, больше не ждём, останавливаем "подвисшие" заявки по таймауту
        foreach ($queue as $id => $item) {
            $item->resume('stop');
            echo $item->getReturn().PHP_EOL;
        }
    }
}
