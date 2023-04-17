<?php

declare(strict_types=1);

time_nanosleep(
    random_int(0, 4),
    random_int(0, 1_000_000_000)
);

$result = random_int(0, 1) ? 'Отказ' : 'Согласие';

echo $result;
