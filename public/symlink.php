<?php
// Simple script to create storage symlink on shared hosting
$targetFolder = __DIR__.'/../storage/app/public';
$linkFolder = __DIR__.'/storage';

if (!file_exists($linkFolder)) {
    if (symlink($targetFolder, $linkFolder)) {
        echo 'Symlink process successfully completed';
    } else {
        echo 'Failed to create symlink. Your hosting provider might have disabled the symlink function.';
    }
} else {
    echo 'Symlink already exists.';
}
