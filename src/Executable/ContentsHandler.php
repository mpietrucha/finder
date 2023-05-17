<?php

namespace Mpietrucha\Finder\Executable;

use SplFileInfo;
use Mpietrucha\Support\Types;
use Mpietrucha\Support\File;
use Illuminate\Support\Stringable;
use Mpietrucha\Finder\Concerns\WithStaticInput;
use Mpietrucha\Finder\Contracts\ExecutableFinderInterface;
use Mpietrucha\Finder\Concerns\ResolveWith;

class ContentsHandler extends Executable implements ExecutableFinderInterface
{
    use ResolveWith;

    use WithStaticInput;

    public function __construct(protected ?string $contains = null, protected ?ClosureHandler $handler = null)
    {
    }

    public function shouldRegister(): bool
    {
        return ! $this->contains || ! $this->handler;
    }

    public function register(): void
    {
        self::createAsHandler('<?php', fn () => 'php')->resolveWithSymfonyPhpExecutableFinder();

        self::createAsHandler('@echo off', fn () => '');

        self::createAsHandler('#!/bin/', function (Stringable $contents, string $contains) {
            return $contents->after($contains)->toNewLineCollection()->first();
        });
    }

    public function handling(mixed $input): void
    {
        if (! $input instanceof SplFileInfo) {
            self::withStaticInput(Types::string($input) ? $input : null);

            return;
        }

        if (! $input->isReadable()) {
            self::withStaticInput(null);

            return;
        }

        self::withStaticInput(File::toStringable($input));
    }

    public function result(): ?string
    {
        if (! self::input()) {
            return null;
        }

        if (! self::input()->contains($this->contains)) {
            return null;
        }

        return $this->handler->result(self::input(), $this->contains);
    }
}
