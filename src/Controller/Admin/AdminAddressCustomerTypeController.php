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

use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Command\ToggleNeedInvoiceAddressCustomerTypeCommand;
use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Command\ToggleStatusAddressCustomerTypeCommand;
use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Exception\AddressCustomerTypeConstraintException;
use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Exception\AddressCustomerTypeException;
use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Exception\AddressCustomerTypeNotFoundException;
use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Exception\DuplicateAddressCustomerTypeNameException;
use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Exception\MissingAddressCustomerTypeRequiredFieldsException;
use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Query\GetAddressCustomerTypeForEditing;
use cdigruttola\Module\Electronicinvoicefields\Core\Search\Filters\AddressCustomerTypeFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
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
     * @param Request $request
     * @param AddressCustomerTypeFilters $filters
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return Response
     */
    public function indexAction(Request $request, AddressCustomerTypeFilters $filters)
    {
        $legacyController = $request->attributes->get('_legacy_controller');
        $addressCustomerTypeGridFactory = $this->get('cdigruttola.module.electronicinvoicefields.core.grid.factory.address_customer_type');
        $addressCustomerTypeGrid = $addressCustomerTypeGridFactory->getGrid($filters);

        return $this->render('@Modules/electronicinvoicefields/views/templates/admin/index.html.twig', [
            'addressCustomerTypeGrid' => $this->presentGrid($addressCustomerTypeGrid),
            'help_link' => false,
        ]);
    }

    /**
     * Show address_customer_type create form & handle processing of it.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $addressCustomerTypeForm = $this->get('cdigruttola.module.electronicinvoicefields.core.form.identifiable_object.builder.address_customer_type_form_builder')->getForm();
        $addressCustomerTypeForm->handleRequest($request);

        $addressCustomerTypeFormHandler = $this->get('cdigruttola.module.electronicinvoicefields.core.form.identifiable_object.handler.address_customer_type_form_handler');

        try {
            $result = $addressCustomerTypeFormHandler->handle($addressCustomerTypeForm);

            if ($orderStateId = $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute(self::INDEX_ROUTE);
            }
        } catch (AddressCustomerTypeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@Modules/electronicinvoicefields/views/templates/admin/create.html.twig', [
            'addressCustomerTypeForm' => $addressCustomerTypeForm->createView(),
            'help_link' => false,
            'contextLangId' => $this->getContextLangId(),
            'templatesPreviewUrl' => _MAIL_DIR_,
            'languages' => array_map(
                function (array $language) {
                    return [
                        'id' => $language['iso_code'],
                        'value' => sprintf('%s - %s', $language['iso_code'], $language['name']), ];
                },
                $this->get('prestashop.adapter.legacy.context')->getLanguages()
            ),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function editAction(int $addressCustomerTypeId, Request $request)
    {
        $addressCustomerTypeForm = $this->get('cdigruttola.module.electronicinvoicefields.core.form.identifiable_object.builder.address_customer_type_form_builder')->getFormFor($addressCustomerTypeId);
        $addressCustomerTypeForm->handleRequest($request);

        $addressCustomerTypeFormHandler = $this->get('cdigruttola.module.electronicinvoicefields.core.form.identifiable_object.handler.address_customer_type_form_handler');

        try {
            $result = $addressCustomerTypeFormHandler->handleFor($addressCustomerTypeId, $addressCustomerTypeForm);

            if ($result->isSubmitted()) {
                if ($result->isValid()) {
                    $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
                } else {
                    $this->addFlashFormErrors($addressCustomerTypeForm);
                }

                return $this->redirectToRoute(self::INDEX_ROUTE);
            }
        } catch (AddressCustomerTypeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@Modules/electronicinvoicefields/views/templates/admin/edit.html.twig', [
            'addressCustomerTypeForm' => $addressCustomerTypeForm->createView(),
            'help_link' => false,
            'editableAddressCustomerType' => $this->getQueryBus()->handle(new GetAddressCustomerTypeForEditing((int) $addressCustomerTypeId)),
            'contextLangId' => $this->getContextLangId(),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param int $addressCustomerTypeId
     *
     * @return RedirectResponse
     */
    public function deleteAction($addressCustomerTypeId)
    {
        $addressCustomerType = new \Addresscustomertype($addressCustomerTypeId);
        $errors = [];
        if (\Addresscustomertype::checkAssociatedAddressToAddressCustomerType($addressCustomerTypeId)) {
            $errors[] = ['key' => 'Could not delete %i%, there is at least one address associated',
                'domain' => 'Modules.Electronicinvoicefields.Einvoice',
                'parameters' => ['%i%' => $addressCustomerTypeId], ];
        } elseif (!$addressCustomerType->removable) {
            $errors[] = ['key' => 'Could not delete %i%',
                'domain' => 'Modules.Electronicinvoicefields.Einvoice',
                'parameters' => ['%i%' => $addressCustomerTypeId], ];
        } elseif (!$addressCustomerType->delete()) {
            $errors[] = ['key' => 'Could not delete %i%',
                'domain' => 'Modules.Electronicinvoicefields.Einvoice',
                'parameters' => ['%i%' => $addressCustomerTypeId], ];
        }

        if (0 === count($errors)) {
            $this->addFlash('success', $this->trans('Successful deletion.', 'Admin.Notifications.Success'));
        } else {
            $this->flashErrors($errors);
        }
        unset($addressCustomerType);

        return $this->redirectToRoute(self::INDEX_ROUTE);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param int $addressCustomerTypeId
     *
     * @return RedirectResponse
     */
    public function toggleStatusAction(int $addressCustomerTypeId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new ToggleStatusAddressCustomerTypeCommand($addressCustomerTypeId));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (AddressCustomerTypeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute(self::INDEX_ROUTE);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param int $addressCustomerTypeId
     *
     * @return RedirectResponse
     */
    public function toggleNeedInvoiceAction(int $addressCustomerTypeId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new ToggleNeedInvoiceAddressCustomerTypeCommand($addressCustomerTypeId));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (AddressCustomerTypeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute(self::INDEX_ROUTE);
    }

    /**
     * Get errors that can be used to translate exceptions into user friendly messages
     *
     * @return array
     */
    private function getErrorMessages(\Exception $e)
    {
        return [
            AddressCustomerTypeNotFoundException::class => $this->trans(
                'This order status does not exist.',
                'Admin.Notifications.Error'
            ),
            DuplicateAddressCustomerTypeNameException::class => $this->trans(
                'An order status with the same name already exists: %s',
                'Admin.Shopparameters.Notification',
                [$e instanceof DuplicateAddressCustomerTypeNameException ? $e->getName()->getValue() : '']
            ),
            AddressCustomerTypeConstraintException::class => [
                AddressCustomerTypeConstraintException::INVALID_NAME => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Name', 'Admin.Global'))]
                ),
            ],
            MissingAddressCustomerTypeRequiredFieldsException::class => $this->trans(
                'The %s field is required.',
                'Admin.Notifications.Error',
                [
                    implode(
                        ',',
                        $e instanceof MissingAddressCustomerTypeRequiredFieldsException ? $e->getMissingRequiredFields() : []
                    ),
                ]
            ),
        ];
    }
}
