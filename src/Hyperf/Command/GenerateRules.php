<?php

declare(strict_types=1);

namespace Serendipity\Hyperf\Command;

use Hyperf\Command\Command as HyperfCommand;
use Override;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use Serendipity\Domain\Support\Reflective\Factory\Ruler;
use Symfony\Component\Console\Input\InputArgument;

use function Serendipity\Type\Cast\stringify;
use function Serendipity\Type\Util\extractArray;

class GenerateRules extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('dev:rules {entity}');
    }

    #[Override]
    public function configure()
    {
        parent::configure();
        $this->setDescription('Export the rules to validate an entity');
    }

    /**
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(): void
    {
        $this->getOutput()?->title('Exporting rules');
        $entity = stringify($this->input->getArgument('entity'));
        $this->line(sprintf("Generating rules for '%s'. Please wait...", $entity));
        $this->newLine();

        $output = match (true) {
            class_exists($entity) => $this->generateRules($entity),
            file_exists($entity) => $this->generateRulesFromFile($entity),
            default => null,
        };
        if (! $output) {
            $this->error('It was not possible to generate rules for the entity');
            return;
        }
        $this->info('Rules generated successfully');
        $this->line($output);
        $this->newLine();
        $this->line('Copy and paste the rules above into your input file');
        $this->line('--');
    }

    protected function getArguments()
    {
        return [
            [
                'entity',
                InputArgument::REQUIRED,
                'The entity to generate rules for. It can be a full qualified name or a file path.'
            ],
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     * @throws NotFoundExceptionInterface
     */
    private function generateRules(string $entity): string
    {
        $ruler = $this->container->get(Ruler::class);
        $ruleset = $ruler->ruleset($entity);
        $rules = $ruleset->all();
        return array_export($rules);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     * @throws NotFoundExceptionInterface
     */
    private function generateRulesFromFile(string $filePath): ?string
    {
        $projectRoot = dirname(__DIR__, 3);
        if (defined('BASE_PATH')) {
            $projectRoot = BASE_PATH;
        }
        $composerJsonPath = sprintf('%s/composer.json', $projectRoot);

        $composer = json_decode(file_get_contents($composerJsonPath), true);
        $autoload = extractArray($composer, 'autoload');
        $psr4Mappings = extractArray($autoload, 'psr-4');

        $realFilePath = realpath($filePath);

        foreach ($psr4Mappings as $namespace => $directory) {
            $basePath = realpath(sprintf('%s/%s', $projectRoot, $directory));
            if (! str_starts_with($realFilePath, $basePath)) {
                continue;
            }
            $relativePath = substr($realFilePath, strlen($basePath) + 1);
            $class = $namespace . str_replace(['/', '.php'], ['\\', ''], $relativePath);

            if (class_exists($class)) {
                return $this->generateRules($class);
            }
        }
        return null;
    }
}
