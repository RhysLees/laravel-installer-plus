<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait FileManipulation
{
    /**
     * Replace the given string in the given file.
     *
     * @param  string  $file
     * @param  string  $search
     * @param  string  $replace
     * @return void
     */
    public function replaceInFile(string $file, string $search, string $replace)
    {
        file_put_contents(
            $file,
            str_replace($search, $replace, file_get_contents($file))
        );
    }

    /**
     * Prepend the given string to the given file.
     *
     * @param string $file
     * @param string $content
     * @return void
     */
    public function prependInFile(string $file, string $content)
    {
        Storage::prepend($file, $content);
    }

    /**
     * Append the given string to the given file.
     *
     * @param string $file
     * @param string $content
     * @return void
     */
    public function appendInFile(string $file, string $content)
    {
        Storage::append($file, $content);
    }
}
