<?php

namespace Mpietrucha\Finder\Executable;

use SplFileInfo;
use Mpietrucha\Finder\Concerns\WithStaticInput;
use Mpietrucha\Support\File;
use Mpietrucha\Finder\Factory\ExecutableFinderFactory;
use Mpietrucha\Finder\Contracts\ExecutableFinderInterface;
use Mpietrucha\Finder\Concerns\ResolveWith;

class ExtensionHandler extends ExecutableFinderFactory implements ExecutableFinderInterface
{
    use ResolveWith;

    use WithStaticInput;

    protected const EXTENSIONS = [
        'js' => 'node',
        'jsx' => 'node',
        'ts' => 'node',
        'sh' => 'sh',
        'py' => 'python'
    ];

    public function __construct(protected ?string $extension = null, protected ?string $executable = null)
    {
    }

    public function shouldRegister(): bool
    {
        return ! $this->extension || ! $this->executable;
    }

    public function register(): void
    {
        self::createAsHandler('php', 'php')->resolveWithSymfonyPhpExecutableFinder();

        collect(self::EXTENSIONS)->map(fn (string $executable, string $extension) => self::createAsHandler($extension, $executable));
    }

    public function handling(mixed $input): void
    {
        if (! $input instanceof SplFileInfo) {
            self::withStaticInput(null);

            return;
        }

        $extension = collect([
            File::extension($input), File::guessExtension($input)
        ])->filter()->first();

        self::withStaticInput($extension);
    }

    public function result(): ?string
    {
        if (! self::input()) {
            return null;
        }

        if ($this->extension !== self::input()) {
            return null;
        }

        return $this->executable;
    }
}
