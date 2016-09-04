<?php namespace Vaccuum\Config;

use Vaccuum\Contracts\Config\ConfigException;
use Vaccuum\Contracts\Config\IDirectoryReader;

class DirectoryReader implements IDirectoryReader
{
    /** @inheritdoc */
    public function read($path)
    {
        $files = [];
        $pattern = "$path/*.conf.php";

        foreach (glob($pattern) as $file)
        {
            $name = basename($file, '.conf.php');
            $files[$name] = $this->load($file);
        }

        return $files;
    }

    /**
     * Load configuration file.
     *
     * @param string $path
     *
     * @throws ConfigException
     * @return array
     */
    protected function load($path)
    {
        /** @noinspection PhpIncludeInspection */
        $file = require($path);

        if (!is_array($file))
        {
            $message = "{$path} configuration file is not readable.";
            throw new ConfigException($message);
        }

        return $file;
    }
}