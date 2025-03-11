<?php

namespace Monolog\App\Helpers\Storage;

/**
 * Class Storage
 *
 * This class is responsible for handling image file operations such as:
 * - Creating directories for storing images
 * - Moving uploaded image files to the storage directory
 * - Renaming existing image files
 * - Locating files based on a search pattern
 * - Sanitizing file names to prevent invalid characters
 *
 * The class is designed to be used in a fluent/chainable structure for better usability.
 */
class Storage 
{
    protected string $basePath; // Base directory for image storage
    protected string $fileName; // The name of the image file
    protected string $filePath; // The full path of the image file

    /**
     * Storage constructor.
     * Initializes the base storage directory for images.
     */
    public function __construct()
    {
        // Set the base path where images will be stored
        $this->basePath = __DIR__ . '/../../../view/public/images/';
    }

    /**
     * Set the filename for the image.
     *
     * @param string $name The name of the image file.
     * @return self
     */
    public function setFileName(string $name): self
    {
        $this->fileName = $this->sanitizeFileName($name);
        return $this;
    }

    /**
     * Set the full file path based on the current filename.
     *
     * @return self
     */
    public function setFilePath(): self
    {
        $this->filePath = $this->basePath . $this->fileName;
        return $this;
    }

    /**
     * Create a directory if it doesn't exist.
     *
     * @param string $path Subdirectory inside the base storage path.
     * @return self
     */
    public function makeDirectory(string $path = ''): self
    {
        $dir = $this->basePath . trim($path, '/');
        
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $this;
    }

    /**
     * Move an uploaded image to the storage directory.
     *
     * @param string $tempPath The temporary file path from the upload.
     * @return bool Returns true if the file is successfully moved, otherwise false.
     */
    public function moveFile(string $tempPath): bool
    {
        $this->setFilePath();

        if (move_uploaded_file($tempPath, $this->filePath)) {
            return true;
        }

        return false;
    }

    /**
     * Rename an existing file.
     *
     * @param string $newName The new name for the file.
     * @return bool Returns true if the file is successfully renamed, otherwise false.
     */
    public function renameFile(string $newName): bool
    {
        $newPath = $this->basePath . $this->sanitizeFileName($newName);

        if (file_exists($this->filePath)) {
            if (rename($this->filePath, $newPath)) {
                $this->filePath = $newPath;
                return true;
            }
        }

        return false;
    }

    /**
     * Locate files in the storage directory.
     *
     * @param string $pattern The search pattern (e.g., '*.jpg' to find all JPG files).
     * @return array Returns an array of file paths matching the pattern.
     */
    public function findFiles(string $pattern = '*'): array
    {
        return glob($this->basePath . $pattern);
    }

    /**
     * Sanitize a file name to remove unwanted characters.
     *
     * @param string $name The original file name.
     * @return string Returns a sanitized file name.
     */
    private function sanitizeFileName(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $name);
    }
}
