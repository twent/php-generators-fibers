<?php

declare(strict_types=1);

namespace Twent\GeneratorsFibers\BanksFibers;

use Fiber;
use Throwable;

final class BankGateway
{
    public function __construct(
        public readonly string $name,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function request(int $amount): string
    {
        Fiber::suspend("{$this->name}: Отправлен запрос в API");

        $isTerminated = false;
        $attempts = random_int(10, 50);

        // Имитируем задержку ответа со стороны API
        for ($i = 0; $i < $attempts; ++$i) {
            // Если банк "ещё не ответил", возвращаем null
            $action = Fiber::suspend();
            // Нас могут попросить больше не ждать ответа от банка
            if ($action === 'stop') {
                $isTerminated = true;
                break;
            }
        }

        if ($isTerminated) {
            $result = 'Остановка по таймауту';
        } else {
            $result = random_int(0, 1) ? 'Отказ' : 'Согласие';
        }
        return "{$this->name}: $result";
    }
}
