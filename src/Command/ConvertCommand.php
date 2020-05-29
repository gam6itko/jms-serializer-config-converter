<?php declare(strict_types=1);

namespace Gam6itko\JSCC\Command;

use Composer\Autoload\ClassLoader;
use Gam6itko\JSCC\Converter\ConverterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertCommand extends Command
{
    protected static $defaultName = 'jms-serializer:config-convert';

    /**
     * @var ConverterInterface
     */
    private $converter;

    public function __construct(ConverterInterface $converter)
    {
        parent::__construct();
        $this->converter = $converter;
    }

    protected function configure()
    {
        $this
            ->addArgument('namespace', InputArgument::REQUIRED)
            ->addArgument('from-format', InputArgument::REQUIRED, 'From format')
            ->addArgument('to-format', InputArgument::REQUIRED, 'To format')
            ->setDescription('Convert configuration from one format to another');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $namespace = trim($input->getArgument('namespace'), '\\');
        $folders = $this->collectFolders($namespace);
        if (!$folders) {
            return 1;
        }

        $classes = [];
        foreach ($folders as $folder) {
            $classes = array_merge($classes, $this->collectClasses($namespace, $folder));
        }

        foreach ($classes as $fqcn) {
            $this->converter->convert(new \ReflectionClass($fqcn), $input->getArgument('from-format'), $input->getArgument('to-format'));
        }

        return 0;
    }

    private function collectFolders(string $targetNamespace): array
    {
        $classLoader = $this->getClassLoader();
        $targetNsArr = explode('\\', trim($targetNamespace, '\\'));

        $result = [];

        foreach ($classLoader->getPrefixesPsr4() as $ns => $dirs) {
            if (false === strpos($ns, $targetNsArr[0])) {
                continue;
            }

            $nsArr = explode('\\', trim($ns, '\\'));
            foreach ($nsArr as $str) {
                if (empty($targetNsArr)) {
                    $result[] = $ns;
                    continue 2;
                }
                if ($str === $targetNsArr[0]) {
                    array_shift($targetNsArr);
                    continue;
                }
            }

            if (empty($targetNsArr)) {
                $result[] = $ns;
                continue;
            }

            $add = implode(\DIRECTORY_SEPARATOR, $targetNsArr);
            foreach ($dirs as $dir) {
                $folder = rtrim($dir, \DIRECTORY_SEPARATOR).\DIRECTORY_SEPARATOR.$add;
                if (file_exists($folder)) {
                    $result[] = realpath($folder);
                }
            }
        }

        return $result;
    }

    private function getClassLoader(): ClassLoader
    {
        if (null === $pathToComposer = $this->findComposerJson()) {
            throw new \RuntimeException('Failed to find project composer.json');
        }

        $composerConfig = json_decode(file_get_contents($pathToComposer), true);
        $vendorDir = \dirname($pathToComposer).'/vendor';
        if (isset($composerConfig['config']['vendor-dir'])) {
            if ($absPath = realpath($composerConfig['config']['vendor-dir'])) {
                $vendorDir = $absPath;
            } else {
                $vendorDir = \dirname($pathToComposer).\DIRECTORY_SEPARATOR.$composerConfig['config']['vendor-dir'];
            }
        }

        return include $vendorDir.'/autoload.php';
    }

    /**
     * Go up to until find composer.json file.
     */
    private function findComposerJson(string $folder = __DIR__): ?string
    {
        if (!\in_array('composer.json', scandir($folder))) {
            if ('/' === $folder) {
                return null;
            }

            return $this->findComposerJson(\dirname($folder)); //up
        }

        return "$folder/composer.json";
    }

    /**
     * Searches psr4 classes in folders.
     */
    private function collectClasses(string $namespace, string $folder): array
    {
        $result = [];
        foreach (scandir($folder) as $item) {
            if (\in_array($item, ['.', '..'])) {
                continue;
            }

            if (is_dir($item)) {
                $this->collectClasses($namespace.'\\'.$item, $item);
            } else {
                if (is_file($folder.\DIRECTORY_SEPARATOR.$item)) {
                    $fqcn = $namespace.'\\'.str_replace('.php', '', $item);
                    if (class_exists($fqcn)) {
                        $result[] = $fqcn;
                    }
                }
            }
        }

        return $result;
    }
}
