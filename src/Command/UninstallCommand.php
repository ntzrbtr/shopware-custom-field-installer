<?php

declare(strict_types=1);

namespace Netzarbeiter\Shopware\CustomFieldInstaller\Command;

use Netzarbeiter\Shopware\CustomFieldInstaller\Service\CustomFieldService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Uninstall custom fields
 */
class UninstallCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * @inerhitDoc
     */
    protected static $defaultName = 'netzarbeiter:custom-fields:uninstall';

    /**
     * @inerhitDoc
     */
    protected static $defaultDescription = 'Uninstall custom fields';

    /**
     * Context
     *
     * @var Context
     */
    protected Context $context;

    /**
     * Style for input/output
     *
     * @var SymfonyStyle
     */
    protected SymfonyStyle $io;

    /**
     * InstallCommand constructor.
     *
     * @param CustomFieldService $customFieldService
     */
    public function __construct(protected CustomFieldService $customFieldService)
    {
        parent::__construct();

        // Create context.
        $this->context = Context::createDefaultContext();
        $this->context->addState(EntityIndexerRegistry::DISABLE_INDEXING);
    }

    /**
     * @inerhitDoc
     */
    protected function configure(): void
    {
        $this
            ->addArgument('manifest', InputArgument::REQUIRED, 'Manifest file (XML)');
    }

    /**
     * @inerhitDoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @inerhitDoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        // Print plugin title.
        $this->io->title(sprintf('%s (%s)', $this->getDescription(), $this->getName()));

        // Check the manifest file.
        $manifestFile = $input->getArgument('manifest');
        if (!file_exists($manifestFile)) {
            $this->io->error(sprintf('Manifest file "%s" not found', $manifestFile));
            return self::FAILURE;
        }

        // Call the service to do the work.
        $this->customFieldService->uninstall($manifestFile, $this->context);
        $this->io->success('Custom fields uninstalled');

        return self::SUCCESS;
    }
}
