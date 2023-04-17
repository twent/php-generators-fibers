<?php

declare(strict_types=1);

namespace Twent\GeneratorsFibers\BanksGenerators;

final class BankGateway
{
    public function __construct(
        public readonly string $name,
    ) {
    }

    public function request(int $amount): \Generator
    {
        $errno = $errstr = null;

        $socket = stream_socket_client(
            "tcp://localhost:80",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_ASYNC_CONNECT
        );

        stream_set_blocking($socket, false);

        // Ждём, пока в сокет можно будет начать писать
        do {
            yield null;
            $socketStatus = stream_get_meta_data($socket);
        } while ($socketStatus['blocked'] === true);

        // Отправляем тело запроса
        $request = "GET /otus/api.php HTTP/1.0\r\nHost: localhost\r\nConnection: Close\r\n\r\n";
        fwrite($socket, $request);

        // Ждём, когда можно будет начать читать из сокета
        $response = '';
        $isStopped = false;

        while (!feof($socket)) {
            $action = yield null;

            if ($action === 'stop') {
                $isStopped = true;
                break;
            }

            $response .= fread($socket, 8192);

            echo '.';
        }

        fclose($socket);

        if ($isStopped === true) {
            yield "{$this->name}: Остановка по таймауту";
        } else {
            $responseBody = '';

            if (preg_match('/\r\n\r\n(.*)/s', $response, $matches)) {
                $responseBody = $matches[1];
            }

            yield "{$this->name}: {$responseBody}";
        }
    }
}
