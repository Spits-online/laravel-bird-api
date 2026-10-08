<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Exceptions;

use RuntimeException;

/**
 * Every exception this package throws extends this one, so a single
 * `catch (BirdException $e)` covers them all.
 */
class BirdException extends RuntimeException {}
