<?php

declare(strict_types=1);

namespace Netzarbeiter\Shopware\CustomFieldInstaller\Service;

use Shopware\Core\Framework\App\Manifest\Manifest;
use Shopware\Core\Framework\App\Manifest\Xml\CustomFieldSet;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\CustomField\Aggregate\CustomFieldSet\CustomFieldSetEntity;

/**
 * Custom field service
 */
class CustomFieldService
{
    /**
     * Custom field set repository
     *
     * @var EntityRepository
     */
    protected EntityRepository $customFieldSetRepository;

    /**
     * CustomFieldService constructor.
     *
     * @param EntityRepository $customFieldSetRepository
     */
    public function __construct(EntityRepository $customFieldSetRepository)
    {
        $this->customFieldSetRepository = $customFieldSetRepository;
    }

    /**
     * Install custom fields from manifest file.
     *
     * @param string $xmlPath
     * @param Context $context
     */
    public function install(string $xmlPath, Context $context): void
    {
        $manifest = Manifest::createFromXmlFile($xmlPath);

        if ($manifest->getCustomFields()) {
            foreach ($manifest->getCustomFields()->getCustomFieldSets() as $customFieldSetData) {
                $context->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($customFieldSetData): void {
                    $this->deleteCustomFieldSet($customFieldSetData, $context);
                    $this->createCustomFieldSet($customFieldSetData, $context);
                });
            }
        }
    }

    /**
     * Uninstall custom fields from manifest file.
     *
     * @param string $xmlPath
     * @param Context $context
     */
    public function uninstall(string $xmlPath, Context $context): void
    {
        $manifest = Manifest::createFromXmlFile($xmlPath);

        if ($manifest->getCustomFields()) {
            foreach ($manifest->getCustomFields()->getCustomFieldSets() as $customFieldSetData) {
                $this->deleteCustomFieldSet($customFieldSetData, $context);
            }
        }
    }

    /**
     * Create a custom field set.
     *
     * @param CustomFieldSet $customFieldSetData
     * @param Context $context
     */
    protected function createCustomFieldSet(CustomFieldSet $customFieldSetData, Context $context): void
    {
        $data = $customFieldSetData->toEntityArray('my-dummy-app-id');
        unset($data['appId']); // Remove appId from data array as we're not handling an app.

        $this->customFieldSetRepository->upsert([$data], $context);
    }

    /**
     * Delete a custom field set.
     *
     * @param CustomFieldSet $customFieldSetData
     * @param Context $context
     */
    protected function deleteCustomFieldSet(CustomFieldSet $customFieldSetData, Context $context): void
    {
        $customFieldSetEntity = $this->getCustomFieldSet($customFieldSetData->getName(), $context);
        if (!$customFieldSetEntity) {
            return;
        }

        $this->customFieldSetRepository->delete([['id' => $customFieldSetEntity->getId()]], $context);
    }

    /**
     * Fetch custom field set by its name.
     *
     * @param string $customFieldSetName
     * @param Context $context
     * @return CustomFieldSetEntity|null
     */
    protected function getCustomFieldSet(string $customFieldSetName, Context $context): ?CustomFieldSetEntity
    {
        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('name', $customFieldSetName))
            ->setLimit(1);

        return $this->customFieldSetRepository
            ->search($criteria, $context)
            ->getEntities()
            ->first();
    }
}
