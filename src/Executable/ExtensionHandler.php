<?php

namespace Mpietrucha\Finder\Executable;

use Mpietrucha\Support\File;
use Illuminate\Support\Collection;
use Mpietrucha\Finder\Contracts\Executable\Inputable;
use Mpietrucha\Finder\Contracts\Executable\Registerable;

class ExtensionHandler extends Handler implements Registerable, Inputable
{
    protected const EXTENSIONS = [
        'php' => 'php',
        'js' => 'node',
        'jsx' => 'node',
        'ts' => 'node',
        'sh' => 'sh',
        'py' => 'python'
    ];

    public function __construct(protected string $extension, protected string $executable)
    {
    }

    public static function register(): void
    {
        collect(self::EXTENSIONS)->each(fn (string $executable, string $extension) => self::createAsHandler($extension, $executable));
    }

    public function handling(string $input): void
    {
        $extension = collect([
            File::extension($input)
        ])->when(File::exists($input), fn (Collection $extensions) => $extensions->push(
            File::guessExtension($input)
        ))->filter()->first();

        self::withStaticInput($extension);
    }

    public function result(): ?string
    {
        if ($this->extension !== self::input()) {
            return null;
        }

        return $this->executable;
    }
}
