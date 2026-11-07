<?php

declare(strict_types=1);

namespace LaravelLang\Models\Helpers;

use DirectoryIterator;

use function dirname;
use function file_put_contents;
use function is_dir;
use function is_file;
use function is_link;
use function mkdir;
use function realpath;
use function rmdir;
use function rtrim;
use function unlink;

/** The few filesystem operations the package needs, built on native PHP only. */
final class Filesystem
{
    /** @return string the real path of the stored file */
    public static function store(string $path, string $content, int $mode = 0o755): string
    {
        self::ensureDirectory(dirname($path), $mode);

        file_put_contents($path, $content);

        return realpath($path);
    }

    public static function ensureDirectory(string $path, int $mode = 0o755): void
    {
        if (! is_dir($path)) {
            mkdir($path, $mode, true);
        }
    }

    /** @param  array<string>|string  $paths */
    public static function ensureDeleteFile(array|string $paths): void
    {
        foreach ((array) $paths as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    /**
     * Recursively delete directories. Symlinks are unlinked, never followed.
     *
     * @param  array<string>|string  $paths
     */
    public static function ensureDeleteDirectory(array|string $paths): void
    {
        foreach ((array) $paths as $path) {
            // `is_link('link/')` is false, so a trailing separator would make us traverse the target.
            $link = rtrim($path, '\\/');

            if ($link !== '' && is_link($link)) {
                unlink($link);

                continue;
            }

            if (! is_dir($path)) {
                continue;
            }

            foreach (new DirectoryIterator($path) as $item) {
                if ($item->isDot()) {
                    continue;
                }

                $item->isDir() && ! $item->isLink()
                    ? self::ensureDeleteDirectory($item->getPathname())
                    : unlink($item->getPathname());
            }

            rmdir($path);
        }
    }
}
