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

namespace cdigruttola\Module\Electronicinvoicefields\Controller\Admin;

use cdigruttola\Module\Electronicinvoicefields\Core\Search\Filters\AddressCustomerTypeFilters;
use cdigruttola\Module\Electronicinvoicefields\Entity\EinvoiceCustomerType;
use cdigruttola\Module\Electronicinvoicefields\Repository\EinvoiceCustomerTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandler;
use PrestaShop\PrestaShop\Core\Grid\GridFactory;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminAddressCustomerTypeController extends PrestaShopAdminController
{
    const INDEX_ROUTE = 'admin_address_customer_type';

    /**
     * @param AddressCustomerTypeFilters $filters
     *
     * #[AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))", message="Access denied.")
     * @param GridFactory $addressCustomerTypeGridFactory
     * @return Response
     */
    public function indexAction(
        AddressCustomerTypeFilters $filters,
        #[Autowire(service: 'cdigruttola.module.electronicinvoicefields.core.grid.factory.address_customer_type')]
        GridFactory $addressCustomerTypeGridFactory,
    ): Response {
        $addressCustomerTypeGrid = $addressCustomerTypeGridFactory->getGrid($filters);

        return $this->render('@Modules/electronicinvoicefields/views/templates/admin/index.html.twig', [
            'addressCustomerTypeGrid' => $this->presentGrid($addressCustomerTypeGrid),
            'help_link' => false,
        ]);
    }

    /**
     * Show address_customer_type create form & handle processing of it.
     *
     * #[AdminSecurity("is_granted(['create'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param FormBuilderInterface $formDataHandler
     * @param FormHandler $formHandler
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createAction(
        Request $request,
        #[Autowire(service: 'cdigruttola.module.electronicinvoicefields.core.form.identifiable_object.builder.address_customer_type_form_builder')]
        FormBuilderInterface $formDataHandler,
        #[Autowire(service: 'cdigruttola.module.electronicinvoicefields.core.form.identifiable_object.handler.address_customer_type_form_handler')]
        FormHandler $formHandler,
    ): Response {
        $addressCustomerTypeForm = $formDataHandler->getForm();
        $addressCustomerTypeForm->handleRequest($request);

        try {
            $result = $formHandler->handle($addressCustomerTypeForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute(self::INDEX_ROUTE);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e));
        }

        return $this->render('@Modules/electronicinvoicefields/views/templates/admin/create.html.twig', [
            'addressCustomerTypeForm' => $addressCustomerTypeForm->createView(),
            'help_link' => false,
            'contextLangId' => $this->container->get(LanguageContext::class)->getId(),
            'templatesPreviewUrl' => _MAIL_DIR_,
            'languages' => array_map(
                function (array $language) {
                    return [
                        'id' => $language['iso_code'],
                        'value' => sprintf('%s - %s', $language['iso_code'], $language['name']), ];
                },
                $this->container->get(LegacyContext::class)->getLanguages()
            ),
        ]);
    }

    /**
     * #[AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param int $addressCustomerTypeId
     * @param Request $request
     * @param FormBuilderInterface $formBuilder
     * @param FormHandler $formHandler
     * @return Response
     */
    public function editAction(
        int $addressCustomerTypeId,
        Request $request,
        #[Autowire(service: 'cdigruttola.module.electronicinvoicefields.core.form.identifiable_object.builder.address_customer_type_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'cdigruttola.module.electronicinvoicefields.core.form.identifiable_object.handler.address_customer_type_form_handler')]
        FormHandler $formHandler,
    ): Response {
        $addressCustomerTypeForm = $formBuilder->getFormFor($addressCustomerTypeId);
        $addressCustomerTypeForm->handleRequest($request);

        try {
            $result = $formHandler->handleFor($addressCustomerTypeId, $addressCustomerTypeForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful edition.', [], 'Admin.Notifications.Success')
                );

                return $this->redirectToRoute(self::INDEX_ROUTE);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e));
        }

        return $this->render('@Modules/electronicinvoicefields/views/templates/admin/edit.html.twig', [
            'addressCustomerTypeForm' => $addressCustomerTypeForm->createView(),
            'help_link' => false,
            'title' => $this->trans('Address Customer Type edit', [], 'Modules.Recipesproducts.Admin'),
        ]);
    }

    /**
     * #[AdminSecurity("is_granted(['delete'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param int $addressCustomerTypeId
     *
     * @return RedirectResponse
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function deleteAction($addressCustomerTypeId)
    {
        $entityManager = $this->container->get(EntityManagerInterface::class);
        /** @var EinvoiceCustomerTypeRepository $repository */
        $repository = $entityManager->getRepository(EinvoiceCustomerType::class);

        /** @var EinvoiceCustomerType|null $entity */
        $entity = $repository->find($addressCustomerTypeId);

        if (!empty($entity)) {
            if (\Addresscustomertype::checkAssociatedAddressToAddressCustomerType($addressCustomerTypeId)) {
                $this->addFlash(
                    'error',
                    $this->trans('Could not delete %i%, there is at least one address associated', ['%i%' => $addressCustomerTypeId], 'Modules.Electronicinvoicefields.Einvoice'));
            } else {
                $entityManager->remove($entity);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    $this->trans('Successful deletion.', [], 'Admin.Notifications.Success')
                );
            }

            return $this->redirectToRoute(self::INDEX_ROUTE);
        }

        $this->addFlash(
            'error',
            $this->trans('Cannot find entity %d', ['%d' => $addressCustomerTypeId], 'Modules.Electronicinvoicefields.Einvoice')
        );

        return $this->redirectToRoute(self::INDEX_ROUTE);
    }

    /**
     * #[AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param int $addressCustomerTypeId
     *
     * @return RedirectResponse
     */
    public function toggleStatusAction(int $addressCustomerTypeId): RedirectResponse
    {
        $entityManager = $this->container->get(EntityManagerInterface::class);
        /** @var EinvoiceCustomerType|null $entity */
        $entity = $entityManager->getRepository(EinvoiceCustomerType::class)->find(['id' => $addressCustomerTypeId]);

        if ($entity == null) {
            $errors = [$this->trans('Entity %d doesn\'t exist', [$addressCustomerTypeId], 'Modules.Electronicinvoicefields.Einvoice')];
            $this->addFlashErrors($errors);

            return $this->redirectToRoute(self::INDEX_ROUTE);
        }

        try {
            $entity->setActive(!$entity->isActive());
            $entityManager->flush();

            $this->addFlash('success', $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'));
        } catch (\Exception $e) {
            $errors = [$this->trans('There was an error while updating the status of active %d: %s', [$addressCustomerTypeId, $e->getMessage()], 'Modules.Electronicinvoicefields.Einvoice')];
            $this->addFlashErrors($errors);
        }

        return $this->redirectToRoute(self::INDEX_ROUTE);    }

    /**
     * #[AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param int $addressCustomerTypeId
     *
     * @return RedirectResponse
     */
    public function toggleNeedInvoiceAction(int $addressCustomerTypeId): RedirectResponse
    {
        $entityManager = $this->container->get(EntityManagerInterface::class);
        /** @var EinvoiceCustomerType|null $entity */
        $entity = $entityManager->getRepository(EinvoiceCustomerType::class)->find(['id' => $addressCustomerTypeId]);

        if ($entity == null) {
            $errors = [$this->trans('Entity %d doesn\'t exist', [$addressCustomerTypeId], 'Modules.Electronicinvoicefields.Einvoice')];
            $this->addFlashErrors($errors);

            return $this->redirectToRoute(self::INDEX_ROUTE);
        }

        try {
            $entity->setNeedInvoice(!$entity->isNeedInvoice());
            $entityManager->flush();

            $this->addFlash('success', $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'));
        } catch (\Exception $e) {
            $errors = [$this->trans('There was an error while updating the status of need invoice %d: %s', [$addressCustomerTypeId, $e->getMessage()], 'Modules.Electronicinvoicefields.Einvoice')];
            $this->addFlashErrors($errors);
        }

        return $this->redirectToRoute(self::INDEX_ROUTE);
    }

    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
                LegacyContext::class => LegacyContext::class,
                EntityManagerInterface::class => EntityManagerInterface::class,
            ];
    }
}
