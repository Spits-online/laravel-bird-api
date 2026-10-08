<?php

declare(strict_types=1);

arch()->preset()->php();
arch()->preset()->security();

arch('every file declares strict types')
    ->expect('SpitsOnline\Bird')
    ->toUseStrictTypes();

arch('data objects are final and readonly')
    ->expect('SpitsOnline\Bird\Data')
    ->classes()
    ->toBeFinal()
    ->toBeReadonly();

arch('exceptions extend the package base exception')
    ->expect('SpitsOnline\Bird\Exceptions')
    ->classes()
    ->toExtend('SpitsOnline\Bird\Exceptions\BirdException')
    ->ignoring('SpitsOnline\Bird\Exceptions\BirdException');

arch('enums are enums')->expect('SpitsOnline\Bird\Enums')->toBeEnums();
