<?php

declare(strict_types=1);

/**
 * Tests run in parallel processes, but they share the same `workbench/app/Models` directory.
 * Tests that generate or scan model files must not overlap, otherwise one process can see a model
 * whose translation class has not been written yet (or was just deleted by another process).
 */
function lockModelsDirectory(): void
{
    $GLOBALS['models_directory_lock'] = fopen(sys_get_temp_dir() . '/laravel-lang-models-tests.lock', 'c');

    flock($GLOBALS['models_directory_lock'], LOCK_EX);
}

function unlockModelsDirectory(): void
{
    if (isset($GLOBALS['models_directory_lock'])) {
        flock($GLOBALS['models_directory_lock'], LOCK_UN);
        fclose($GLOBALS['models_directory_lock']);

        unset($GLOBALS['models_directory_lock']);
    }
}
