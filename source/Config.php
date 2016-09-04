<?php namespace Vaccuum\Config;

use Vaccuum\Contracts\Config\IConfig;
use Vaccuum\Contracts\Config\IConfigLoader;
use Vaccuum\Contracts\Config\IDirectoryReader;

class Config implements IConfig, IConfigLoader
{
    /** @var array */
    protected $configs = [];

    /** @var IDirectoryReader */
    protected $reader;

    /**
     * Config constructor.
     *
     * @param IDirectoryReader|null $reader
     */
    public function __construct(IDirectoryReader $reader = null)
    {
        $this->reader = ($reader) ? $reader : new DirectoryReader();
    }

    /** @inheritdoc */
    public function loadBundles()
    {
        foreach ($this->get('bundles') as $name => $bundle)
        {
            $directory = DIR_ROOT . '/app/' . $name;

            if (isset($bundle['path']))
            {
                $directory = DIR_ROOT . "{$bundle['path']}/{$name}";
            }

            $this->loadDirectory($directory);
        }
    }

    /** @inheritdoc */
    public function get($name)
    {
        $parts = explode('.', $name);
        $result = $this->configs[$parts[0]];

        for ($i = 1; $i < count($parts); $i++)
        {
            if (isset($result[$parts[$i]]))
            {
                $result = $result[$parts[$i]];
            }
        }

        return $result;
    }

    /** @inheritdoc */
    public function loadDirectory($path)
    {
        foreach ($this->reader->read(DIR_ROOT . $path) as $name => $config)
        {
            if (!isset($this->configs[$name]))
            {
                $this->configs[$name] = [];
            }

            $this->configs[$name] =
                array_merge_recursive($this->configs[$name], $config);
        }
    }

    /** @inheritdoc */
    public function set($name, $value)
    {
        $parts = explode('.', $name);
        $target = $this->configs[$parts[0]];

        for ($i = 1; $i < count($parts); $i++)
        {
            if (!isset($target[$parts[$i]]))
            {
                $target[$parts[$i]] = [];
            }

            $target = $target[$parts[$i]];
        }

        $target = $value;
    }
}