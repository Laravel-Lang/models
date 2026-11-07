<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem as Files;
use LaravelLang\Models\Helpers\Filesystem;

beforeEach(function () {
    $this->root = sys_get_temp_dir() . '/laravel-lang-models-' . uniqid();

    mkdir($this->root);
});

afterEach(fn () => (new Files)->deleteDirectory($this->root));

test('store creates missing directories and returns the real path', function () {
    $path = $this->root . '/a/b/file.txt';

    expect(Filesystem::store($path, 'foo'))->toBe(realpath($path));
    expect(file_get_contents($path))->toBe('foo');
});

test('store overwrites existing file', function () {
    $path = $this->root . '/file.txt';

    Filesystem::store($path, 'foo');
    Filesystem::store($path, 'bar');

    expect(file_get_contents($path))->toBe('bar');
});

test('ensure directory is idempotent', function () {
    $path = $this->root . '/a/b';

    Filesystem::ensureDirectory($path);
    Filesystem::ensureDirectory($path);

    expect($path)->toBeDirectory();
});

test('ensure delete file ignores missing files', function () {
    $one = $this->root . '/one.txt';
    $two = $this->root . '/two.txt';

    touch($one);

    Filesystem::ensureDeleteFile([$one, $two]);

    expect($one)->not->toBeFile();
});

test('ensure delete directory removes nested content and ignores missing paths', function () {
    Filesystem::store($this->root . '/dir/nested/file.txt', 'foo');

    Filesystem::ensureDeleteDirectory([$this->root . '/dir', $this->root . '/missing']);

    expect($this->root . '/dir')->not->toBeDirectory();
    expect($this->root)->toBeDirectory();
});

test('ensure delete directory does not follow symlinks', function () {
    Filesystem::store($this->root . '/target/file.txt', 'foo');
    Filesystem::store($this->root . '/dir/keep.txt', 'bar');

    symlink($this->root . '/target', $this->root . '/dir/link');

    Filesystem::ensureDeleteDirectory($this->root . '/dir');

    expect($this->root . '/dir')->not->toBeDirectory();
    expect($this->root . '/target/file.txt')->toBeFile();
});
