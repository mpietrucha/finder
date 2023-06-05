<?php

namespace Mpietrucha\Finder\Executable;

use Mpietrucha\Support\File;
use Illuminate\Support\Stringable;
use Mpietrucha\Finder\Contracts\Executable\Inputable;
use Mpietrucha\Finder\Contracts\Executable\Registerable;

class ContentsHandler extends Handler implements Registerable, Inputable
{
    public function __construct(protected string $contains, protected ClosureHandler $handler)
    {
    }

    public static function register(): void
    {
        self::createAsHandler('<?php', fn () => 'php');

        self::createAsHandler('@echo off', fn () => '');

        self::createAsHandler('#!/bin/', function (Stringable $contents, string $contains) {
            return $contents->after($contains)->toNewLineCollection()->first();
        });
    }

    public function handling(string $input): void
    {
        self::withStaticInput(
            File::exists($input) ? File::toStringable($input) : str($input)
        );
    }

    public function result(): ?string
    {
        if (! self::input()?->contains($this->contains)) {
            return null;
        }

        return $this->handler->result(self::input(), $this->contains);
    }
}
