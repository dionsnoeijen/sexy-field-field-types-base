<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tardigrades\FieldType\Uuid\Helper;

use Rhumsaa\Uuid\Uuid;

final class UuidV4
{
    public static function get(): string
    {
        return (string) Uuid::uuid4();
    }
}
