<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Denormalizer;

use Gam6itko\JSCC\Model\ClassConfig;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
abstract class AbstractFileDenormalizer implements DenormalizerInterface
{
    /**
     * @var string[]
     */
    private $namespaceFolders;

    /**
     * @var bool
     */
    private $overwrite;

    /**
     * @param bool $overwrite if file already exists
     */
    public function __construct(array $namespaceFolders, bool $overwrite = false)
    {
        $this->namespaceFolders = $namespaceFolders;
        $this->overwrite = $overwrite;
    }

    abstract public function toString(ClassConfig $config): string;

    abstract protected function getExtension(): string;

    public function denormalize(ClassConfig $config)
    {
        if (!$this->namespaceFolders) {
            throw new \LogicException('You must define at least one namespace folder');
        }

        if (!$config->name) {
            throw new \LogicException('Config class name not defined');
        }

        $saveTo = $this->buildFilepath($config->name);
        if (file_exists($saveTo)) {
            if (!$this->overwrite) {
                throw new \RuntimeException("File `$saveTo` already exists");
            } else {
                // rename old file and save
                rename($saveTo, "~$saveTo");
            }
        }

        if (!file_exists($dirname = \dirname($saveTo))) {
            mkdir($dirname, 0777, true);
        }

        file_put_contents($saveTo, $this->toString($config));
    }

    private function buildFilepath(string $className): string
    {
        $refClass = new \ReflectionClass($className);
        foreach ($this->namespaceFolders as $prefix => $dir) {
            if ('' !== $prefix && 0 !== strpos($refClass->getNamespaceName(), $prefix)) {
                continue;
            }

            $len = '' === $prefix ? 0 : \strlen($prefix) + 1;

            return $dir.'/'.str_replace('\\', '.', substr($refClass->name, $len)).'.'.$this->getExtension();
        }

        throw new \RuntimeException('Failed to build savePath');
    }
}
