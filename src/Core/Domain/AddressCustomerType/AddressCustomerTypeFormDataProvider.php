<?php
/**
 * Copyright since 2007 Carmine Di Gruttola
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    cdigruttola <c.digruttola@hotmail.it>
 * @copyright Copyright since 2007 Carmine Di Gruttola
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

declare(strict_types=1);

namespace cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType;

use cdigruttola\Module\Electronicinvoicefields\Entity\EinvoiceCustomerType;
use cdigruttola\Module\Electronicinvoicefields\Entity\EinvoiceCustomerTypeLang;
use Doctrine\ORM\EntityRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Provides data for  AddressCustomerTypeDataProvider form.
 */
final class AddressCustomerTypeFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * Constructor.
     *
     * @param EntityRepository $repository
     */
    public function __construct(
        EntityRepository $repository,
    ) {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        /** @var EinvoiceCustomerType $entity */
        $entity = $this->repository->find($id);

        $entityData = [];
        $entityData['active'] = $entity->isActive();
        $entityData['need_invoice'] = $entity->isNeedInvoice();

        /** @var EinvoiceCustomerTypeLang $nameLang */
        foreach ($entity->getNameLangs() as $nameLang) {
            $entityData['name'][$nameLang->getLang()->getId()] = $nameLang->getName();
        }

        return $entityData;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $data = [
            'is_enabled' => true,
        ];

        return $data;
    }
}
