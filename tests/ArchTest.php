<?php

arch('no debug statements')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'print_r'])
    ->not->toBeUsed();

arch('no die or exit')
    ->expect(['die', 'exit'])
    ->not->toBeUsed();
