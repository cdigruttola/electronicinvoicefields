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

namespace cdigruttola\Module\Electronicinvoicefields\Form\DataHandler;

use cdigruttola\Module\Electronicinvoicefields\Entity\EinvoiceCustomerType;
use cdigruttola\Module\Electronicinvoicefields\Entity\EinvoiceCustomerTypeLang;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Saves or updates order return state data submitted in form
 */
final class AddressCustomerTypeFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * @var LangRepository
     */
    private $langRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var array
     */
    private $languages;

    public function __construct(
        EntityRepository $entityRepository,
        LangRepository $langRepository,
        EntityManagerInterface $entityManager,
        array $languages,
    ) {
        $this->entityRepository = $entityRepository;
        $this->langRepository = $langRepository;
        $this->entityManager = $entityManager;
        $this->languages = $languages;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $entity = new EinvoiceCustomerType();

        $entity->setActive((bool) $data['active']);
        $entity->setNeedInvoice((bool) $data['need_invoice']);
        $entity->setRemovable(true);
        $entity->setDateAdd(new \DateTime());
        $entity->setDateUpd(new \DateTime());

        foreach ($this->languages as $language) {
            $langId = (int) $language['id_lang'];
            $lang = $this->langRepository->findOneBy(['id' => $langId]);
            $faqLang = new EinvoiceCustomerTypeLang();

            $faqLang
                ->setLang($lang)
                ->setName($data['name'][$langId] ?? '');

            $entity->addNameLang($faqLang);
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        /** @var EinvoiceCustomerType $entity */
        $entity = $this->entityRepository->find($id);

        $entity->setDateUpd(new \DateTime());
        $entity->setActive((bool) $data['active']);
        $entity->setNeedInvoice((bool) $data['need_invoice']);

        foreach ($this->languages as $language) {
            $langId = (int) $language['id_lang'];
            $nameLangByLangId = $entity->getNameLangByLangId($langId);

            $newEntity = false;
            if (null === $nameLangByLangId) {
                $nameLangByLangId = new EinvoiceCustomerTypeLang();
                $lang = $this->langRepository->find($langId);
                $nameLangByLangId->setLang($lang);
                $newEntity = true;
            }

            $nameLangByLangId->setName($data['name'][$langId] ?? '');

            if ($newEntity) {
                $entity->addNameLang($nameLangByLangId);
            }
        }

        $this->entityManager->flush();

        return $entity->getId();
    }
}
