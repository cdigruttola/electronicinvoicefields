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

use cdigruttola\Module\Electronicinvoicefields\Entity\EinvoiceAddress;
use cdigruttola\Module\Electronicinvoicefields\Form\DataConfiguration\ConfigurationDataConfiguration;
use cdigruttola\Module\Electronicinvoicefields\Repository\EinvoiceAddressRepository;
use cdigruttola\Module\Electronicinvoicefields\Repository\EinvoiceCustomerTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;

if (!defined('_PS_VERSION_')) {
    exit;
}
require 'vendor/autoload.php';

class Electronicinvoicefields extends Module
{
    public function __construct()
    {
        $this->name = 'electronicinvoicefields';
        $this->tab = 'administration';
        $this->version = '3.0.0';
        $this->author = 'cdigruttola';
        $this->need_instance = 0;

        /*
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        $tabNames = [];
        foreach (Language::getLanguages() as $lang) {
            $tabNames[$lang['locale']] = $this->trans('Setting Address Customer Type', [], 'Modules.Electronicinvoicefields.Einvoice', $lang['locale']);
        }

        $this->tabs = [
            [
                'name' => $tabNames,
                'class_name' => 'AdminAddressCustomerType',
                'visible' => true,
                'route_name' => 'admin_address_customer_type',
                'parent_class_name' => 'ShopParameters',
                'wording' => 'Setting Address Customer Type',
                'wording_domain' => 'Modules.Electronicinvoicefields.Einvoice',
            ],
        ];

        parent::__construct();

        $this->displayName = $this->trans('Electronic Invoice - fields', [], 'Modules.Electronicinvoicefields.Einvoice');
        $this->description = $this->trans('This module adds the new fields for E-Invoice in Customer Address', [], 'Modules.Electronicinvoicefields.Einvoice');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Electronicinvoicefields.Einvoice');

        $this->ps_versions_compliancy = ['min' => '9.0.0', 'max' => _PS_VERSION_];
    }

    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install($reset = false): bool
    {
        $this->_clearCache('*');
        include dirname(__FILE__) . '/sql/install.php';

        return parent::install()
            && $this->registerHooks()
            && $this->insertAddressCustomerType();
    }

    public function registerHooks(): bool
    {
        if (!$this->registerHook('displayHeader')
            || !$this->registerHook('displayPDFInvoice')
            || !$this->registerHook('displayPDFOrderSlip')
            || !$this->registerHook('actionCustomerAddressFormBuilderModifier')
            || !$this->registerHook('actionAdminAddressesFormModifier')
            || !$this->registerHook('actionValidateCustomerAddressForm')
            || !$this->registerHook('actionObjectAddressAddAfter')
            || !$this->registerHook('actionObjectAddressUpdateAfter')
            || !$this->registerHook('actionObjectAddressDeleteAfter')
            || !$this->registerHook('actionObjectCustomerAddressAddAfter')
            || !$this->registerHook('actionObjectCustomerAddressUpdateAfter')
            || !$this->registerHook('actionSubmitCustomerAddressForm')
            || !$this->registerHook('actionAfterUpdateCustomerAddressFormHandler')
            || !$this->registerHook('actionAfterCreateCustomerAddressFormHandler')
            || !$this->registerHook('additionalCustomerFormFields')
            || !$this->registerHook('addWebserviceResources')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall(): bool
    {
        include dirname(__FILE__) . '/sql/uninstall.php';

        Configuration::deleteByName(ConfigurationDataConfiguration::EINVOICE_PEC_REQUIRED);
        Configuration::deleteByName(ConfigurationDataConfiguration::EINVOICE_SDI_REQUIRED);
        Configuration::deleteByName(ConfigurationDataConfiguration::EINVOICE_VAT_VIES_VALIDATE);
        Configuration::deleteByName(ConfigurationDataConfiguration::EINVOICE_DNI_VALIDATE);
        Configuration::deleteByName(ConfigurationDataConfiguration::EINVOICE_DNI_VALIDATE_MIOCODICEFISCALE_API);
        Configuration::deleteByName(ConfigurationDataConfiguration::EINVOICE_CHECK_USER_AGE);
        Configuration::deleteByName(ConfigurationDataConfiguration::EINVOICE_MINIMUM_USER_AGE);

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        Tools::redirectAdmin(SymfonyContainer::getInstance()->get('router')->generate('admin_electronic_invoice_configuration'));
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        if (!$this->active) {
            return;
        }
        $id_shop = (int) $this->context->shop->id;

        $sdi_required = (int) Configuration::get(ConfigurationDataConfiguration::EINVOICE_SDI_REQUIRED, null, null, $id_shop);
        $pec_required = (int) Configuration::get(ConfigurationDataConfiguration::EINVOICE_PEC_REQUIRED, null, null, $id_shop);

        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');

        if (isset($this->context->cart)) {
            $virtual = $this->context->cart->isVirtualCart();
        }
        Media::addJsDef(
            [
                'virtual' => $virtual ?? false,
                'sdi_required' => (int) $sdi_required,
                'pec_required' => (int) $pec_required,
                'dni_required' => Address::dniRequired($this->context->country->id),
                'ajax_link' => $this->context->link->getModuleLink($this->name, 'ajax'),
            ]
        );

        Media::addJsDefL('receipt', $this->trans('The receipt is valid for exercising the right of withdrawal and guarantee (where possible), but not for tax purposes.', [], 'Modules.Electronicinvoicefields.Einvoice'));
        Media::addJsDefL('invoice_virtual', $this->trans('The selected address will be used as your personal address (for invoice).', [], 'Shop.Theme.Checkout'));
        Media::addJsDefL('receipt_virtual', $this->trans('The selected address will be used as your personal address (for receipt)', [], 'Modules.Electronicinvoicefields.Einvoice'));
        Media::addJsDefL('invoice_no_virtual', $this->trans('The selected address will be used both as your personal address (for invoice) and as your delivery address.', [], 'Shop.Theme.Checkout'));
        Media::addJsDefL('receipt_no_virtual', $this->trans('The selected address will be used both as your personal address (for receipt) and as your delivery address.', [], 'Modules.Electronicinvoicefields.Einvoice'));
        Media::addJsDefL('address_delivery_as_receipt', $this->trans('Use this address for receipt too', [], 'Modules.Electronicinvoicefields.Einvoice'));
        Media::addJsDefL('address_delivery_as_invoice', $this->trans('Use this address for invoice too', [], 'Shop.Theme.Checkout'));
    }

    public function hookDisplayPDFInvoice($params)
    {
        if (!$this->active) {
            return '';
        }
        if ($params['object']->getOrder()->addressNeedInvoice()) {
            return $this->trans('Courtesy page, you\'ll receive the invoice in XML format via the revenue agency exchange system.', [], 'Modules.Electronicinvoicefields.Einvoice');
        } else {
            return $this->trans('Courtesy page, you\'ll receive the receipt with your pack.', [], 'Modules.Electronicinvoicefields.Einvoice');
        }
    }

    public function hookDisplayPDFOrderSlip($params)
    {
        if (!$this->active) {
            return '';
        }
        if ($params['object']->getOrder()->addressNeedInvoice()) {
            return $this->trans('Courtesy page, you\'ll receive the credit slip in XML format via the revenue agency exchange system.', [], 'Modules.Electronicinvoicefields.Einvoice');
        }

        return '';
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionCustomerAddressFormBuilderModifier($params)
    {
        if (!$this->active) {
            return;
        }

        $id_shop = $this->context->shop->id;

        $sdi_required = Configuration::get(ConfigurationDataConfiguration::EINVOICE_SDI_REQUIRED, null, null, $id_shop);
        $pec_required = Configuration::get(ConfigurationDataConfiguration::EINVOICE_PEC_REQUIRED, null, null, $id_shop);

        $id_address = isset($params['id']) ? (int) $params['id'] : null;

        /** @var EinvoiceAddressRepository $einvoiceAddressRepository */
        $einvoiceAddressRepository = $this->get('cdigruttola.module.electronicinvoicefields.repository.einvoice_address');
        /** @var EinvoiceAddress|null $einvoiceAddress */
        $einvoiceAddress = $einvoiceAddressRepository->findOneBy(['idAddress' => $id_address]);

        $formBuilder = $params['form_builder'];
        $formBuilder->add(
            'sdi',
            TextType::class,
            [
                'label' => $this->trans('SDI Code', [], 'Modules.Electronicinvoicefields.Einvoice'),
                'required' => $sdi_required,
                'constraints' => [
                    new CleanHtml(),
                    new Length([
                        'max' => 7,
                        'maxMessage' => $this->trans('Max caracters allowed : 7', [], 'Modules.Electronicinvoicefields.Einvoice'),
                    ]),
                ],
            ]
        );

        $params['data']['sdi'] = Tools::strtoupper($einvoiceAddress?->getSdi() ?? '');

        $formBuilder->add(
            'pec',
            EmailType::class,
            [
                'label' => $this->trans('PEC Address', [], 'Modules.Electronicinvoicefields.Einvoice'),
                'required' => $pec_required,
                'constraints' => [
                    new CleanHtml(),
                ],
            ]
        );

        $params['data']['pec'] = $einvoiceAddress?->getPec() ?? '';

        /** @var EinvoiceCustomerTypeRepository $customerTypeRepository */
        $customerTypeRepository = $this->get('cdigruttola.module.electronicinvoicefields.repository.einvoice_customer_type');
        $customerTypes = $customerTypeRepository->findByLang($this->context->language->id);

        $choices = [];
        foreach ($customerTypes as $customerType) {
            $choices[$customerType['name']] = $customerType['id_addresscustomertype'];
        }

        $formBuilder->add(
            'id_addresscustomertype',
            ChoiceType::class,
            [
                'choices' => $choices,
                'required' => true,
                'label' => $this->trans('Customer Type', [], 'Modules.Electronicinvoicefields.Einvoice'),
            ]
        );

        $params['data']['id_addresscustomertype'] = $einvoiceAddress?->getIdAddressCustomerType() ?? 1;

        $formBuilder->setData($params['data']);
    }

    public function hookActionAdminAddressesFormModifier($params)
    {
        if (!$this->active) {
            return;
        }
        $switch = 'radio';

        foreach ($params['fields'][0]['form']['input'] as $key => $value) {
            if ($value['name'] == 'vat_number') {
                break;
            }
        }

        /** @var EinvoiceCustomerTypeRepository $customerTypeRepository */
        $customerTypeRepository = $this->get('cdigruttola.module.electronicinvoicefields.repository.einvoice_customer_type');
        $customerTypes = $customerTypeRepository->findByLang($this->context->language->id);

        $choices = [];
        foreach ($customerTypes as $customerType) {
            $choices[$customerType['name']] = $customerType['id_addresscustomertype'];
        }

        $part1 = array_slice($params['fields'][0]['form']['input'], 0, $key + 1);
        $part2 = array_slice($params['fields'][0]['form']['input'], $key + 1);

        $fields = [
            [
                'type' => 'text',
                'label' => $this->trans('PEC Email', [], 'Modules.Electronicinvoicefields.Einvoice'),
                'name' => 'pec',
                'prefix' => "<i class='icon-envelope-o'></i>",
                'class' => 'fixed-width-xxl',
                'hint' => $this->trans('Invalid characters:', [], 'Modules.Electronicinvoicefields.Einvoice') . ' <>;=#{}',
            ],
            [
                'type' => 'text',
                'label' => $this->trans('SDI Code'),
                'name' => 'sdi',
                'class' => 'fixed-width-xxl',
                'hint' => $this->trans('Invalid characters:', [], 'Modules.Electronicinvoicefields.Einvoice') . ' <>;=#{}',
            ],
            [
                'type' => $switch,
                'label' => $this->trans('Customer Type', [], 'Modules.Electronicinvoicefields.Einvoice'),
                'name' => 'id_addresscustomertype',
                'class' => 't',
                'is_bool' => true,
                'values' => $choices,
            ],
        ];

        $params['fields'][0]['form']['input'] = array_merge($part1, $fields, $part2);

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $id_address = (int) $params['object']->id;
        } else {
            $id_address = (int) Tools::getValue('id_address');
        }

        /** @var EinvoiceAddressRepository $einvoiceAddressRepository */
        $einvoiceAddressRepository = $this->get('cdigruttola.module.electronicinvoicefields.repository.einvoice_address');
        /** @var EinvoiceAddress $einvoiceAddress */
        $einvoiceAddress = $einvoiceAddressRepository->findOneBy(['idAddress' => $id_address]);

        $params['fields_value']['sdi'] = Tools::strtoupper($einvoiceAddress->getSdi());
        $params['fields_value']['pec'] = $einvoiceAddress->getPec();
        $params['fields_value']['id_addresscustomertype'] = $einvoiceAddress->getIdAddressCustomerType();
    }

    public function hookActionValidateCustomerAddressForm($params)
    {
        $is_valid = true;
        $form = $params['form'];

        $id_country = $form->getField('id_country')->getValue();
        $iso_country = Country::getIsoById($id_country);
        if ($iso_country === 'IT') {
            $pec = $form->getField('pec');
            if (isset($pec)) {
                $pec_value = $pec->getValue();
                if (!empty($pec_value) && !Validate::isEmail($pec_value)) {
                    $is_valid &= false;
                    $pec->addError($this->trans('Invalid email address format', [], 'Modules.Electronicinvoicefields.Einvoice'));
                }
            }

            $sdi = $form->getField('sdi');
            if (isset($sdi)) {
                $sdi_value = $sdi->getValue();
                if (!empty($sdi_value) && Tools::strlen($sdi_value) != 7) {
                    $is_valid &= false;
                    $sdi->addError($this->trans('Invalid SDI Code', [], 'Modules.Electronicinvoicefields.Einvoice'));
                }
            }

            $dni = $form->getField('dni');
            $id_shop = $this->context->shop->id;
            if (isset($dni) && Configuration::get(ConfigurationDataConfiguration::EINVOICE_DNI_VALIDATE, null, null, $id_shop)) {
                $dni_value = $dni->getValue();
                if (!empty($dni_value) && !Validate::checkDNICode($dni_value, Configuration::get(ConfigurationDataConfiguration::EINVOICE_DNI_VALIDATE_MIOCODICEFISCALE_API, null, null, $id_shop))) {
                    $is_valid &= false;
                    $dni->addError($this->trans('Invalid DNI Code', [], 'Modules.Electronicinvoicefields.Einvoice'));
                }
            }
        }

        $vat_number = $form->getField('vat_number');
        if (isset($vat_number) && Configuration::get(ConfigurationDataConfiguration::EINVOICE_VAT_VIES_VALIDATE, null, null, $id_shop)) {
            $vat_number_value = $vat_number->getValue();
            if (!empty($vat_number_value) && !Validate::checkVatNumber($vat_number_value, $iso_country)) {
                $is_valid &= false;
                $vat_number->addError($this->trans('Invalid VAT Code', [], 'Modules.Electronicinvoicefields.Einvoice'));
            }
        }

        return $is_valid;
    }

    public function hookAdditionalCustomerFormFields($params)
    {
        if ($this->active) {
            $format = $params['fields'];
            if (isset($format['birthday'])) {
                $format['birthday']->setRequired(true);
            }
        }
    }

    public function hookActionAfterUpdateCustomerAddressFormHandler($params)
    {
        $this->retrieveValuesFromFormData($params);
    }

    public function hookActionAfterCreateCustomerAddressFormHandler($params)
    {
        $this->retrieveValuesFromFormData($params);
    }

    public function hookActionSubmitCustomerAddressForm($params)
    {
        if (!isset($params['address'])) {
            return;
        }
        if (!isset($params['object'])) {
            $params['object'] = $params['address'];
        }
        $this->setAddressParams($params);
    }

    public function hookActionObjectCustomerAddressAddAfter($params)
    {
        $this->retrieveValuesFromHttpMethod($params);
    }

    public function hookActionObjectCustomerAddressUpdateAfter($params)
    {
        $this->retrieveValuesFromHttpMethod($params);
    }

    public function hookActionObjectAddressAddAfter($params)
    {
        $this->retrieveValuesFromCustomerAddress($params);
    }

    public function hookActionObjectAddressUpdateAfter($params)
    {
        if (isset($params['object']) && !$params['object']->deleted) {
            $this->retrieveValuesFromCustomerAddress($params);
        }
    }

    public function hookActionObjectAddressDeleteAfter($params)
    {
        if (!$this->active) {
            return;
        }
        $id_address = (int) $params['object']->id;
        $address = new Address($id_address);
        if (!$address->isUsed()) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->get(EntityManagerInterface::class);
            /** @var EinvoiceAddressRepository $einvoiceAddressRepository */
            $einvoiceAddressRepository = $this->get('cdigruttola.module.electronicinvoicefields.repository.einvoice_address');
            /** @var EinvoiceAddress $einvoiceAddress */
            $einvoiceAddress = $einvoiceAddressRepository->findOneBy(['idAddress' => $id_address]);

            $entityManager->remove($einvoiceAddress);
            $entityManager->flush();
        }
    }

    public function hookAddWebserviceResources($params)
    {
        if ($this->active) {
            $def = [
                'pec' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName'],
                'sdi' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName'],
                'id_addresscustomertype' => ['type' => ObjectModel::TYPE_INT, 'validate' => 'isUnsignedInt'],
            ];
            Address::$definition['fields'] = array_merge(Address::$definition['fields'], $def);
            ksort(Address::$definition['fields']);
        }

        return true;
    }

    private function setAddressParams($params)
    {
        if (!$this->active) {
            return;
        }
        if (!$params['object']->id) {
            return;
        }

        $datas = [];
        $datas[$params['object']->id] = [
            'id_addresscustomertype' => isset($params['object']->id_addresscustomertype) ? (int) $params['object']->id_addresscustomertype : '',
            'sdi' => isset($params['object']->sdi) ? (string) $params['object']->sdi : '',
            'pec' => isset($params['object']->pec) ? (string) $params['object']->pec : '',
        ];

        foreach ($datas as $id_address => $data) {
            $id_addresscustomertype = isset($data['id_addresscustomertype']) ? trim((int) $data['id_addresscustomertype']) : 0;
            $sdi = isset($data['sdi']) ? trim((string) $data['sdi']) : '';
            $pec = isset($data['pec']) ? trim((string) $data['pec']) : '';

            if (empty($sdi)) {
                $address = new Address((int) $id_address);
                if (isset($address) && $address->id) {
                    $country = new Country((int) $address->id_country);
                    if ($country->iso_code !== 'IT') {
                        if (!empty($address->company) || !empty($address->vat_number)) {
                            $sdi = 'XXXXXXX';
                        } else {
                            $sdi = '0000000';
                        }
                    } else {
                        $sdi = '0000000';
                    }
                }
            }

            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->get('doctrine.orm.default_entity_manager');
            /** @var EinvoiceAddressRepository $einvoiceAddressRepository */
            $einvoiceAddressRepository = $this->get('cdigruttola.module.electronicinvoicefields.repository.einvoice_address');

            if ($id_address) {
                /** @var EinvoiceAddress|null $einvoiceAddress */
                $einvoiceAddress = $einvoiceAddressRepository->findOneBy(['idAddress' => $id_address]);
                if (null === $einvoiceAddress) {
                    /** @var EinvoiceAddress $einvoiceAddress */
                    $einvoiceAddress = new EinvoiceAddress();
                }
            } else {
                /** @var EinvoiceAddress $einvoiceAddress */
                $einvoiceAddress = new EinvoiceAddress();
            }
            $einvoiceAddress->setIdAddress($id_address);
            $einvoiceAddress->setSdi(Tools::strtoupper($sdi));
            $einvoiceAddress->setPec($pec);
            $einvoiceAddress->setidAddresscustomertype($id_addresscustomertype);

            $entityManager->persist($einvoiceAddress);
            $entityManager->flush();
        }
    }

    /**
     * @param $params
     *
     * @return void
     */
    private function retrieveValuesFromHttpMethod($params): void
    {
        if (!$this->active) {
            return;
        }
        $id_addresscustomertype = (int) Tools::getValue('id_addresscustomertype');
        $sdi = (string) Tools::getValue('sdi');
        $pec = (string) Tools::getValue('pec');

        $params['object']->id_addresscustomertype = $id_addresscustomertype;
        $params['object']->sdi = (string) $sdi;
        $params['object']->pec = (string) $pec;

        $this->setAddressParams($params);
    }

    /**
     * @param $params
     *
     * @return void
     */
    private function retrieveValuesFromFormData($params): void
    {
        if (!$this->active) {
            return;
        }
        if (version_compare(_PS_VERSION_, '1.7.7', '>=')) {
            if (!isset($params['object'])) {
                $params['object'] = (object) null;
            }

            $params['object']->id = (int) $params['id'];
            $params['object']->id_addresscustomertype = isset($params['form_data']['id_addresscustomertype']) ? (int) $params['form_data']['id_addresscustomertype'] : '';
            $params['object']->sdi = isset($params['form_data']['sdi']) ? (string) $params['form_data']['sdi'] : '';
            $params['object']->pec = isset($params['form_data']['pec']) ? (string) $params['form_data']['pec'] : '';

            $this->setAddressParams($params);
        }
    }

    /**
     * @param $params
     *
     * @return void
     */
    private function retrieveValuesFromCustomerAddress($params): void
    {
        if (!$this->active) {
            return;
        }
        $customer_address = Tools::getValue('customer_address');
        if (isset($customer_address) && !empty($customer_address)) {
            $params['object']->id_addresscustomertype = (int) $customer_address['id_addresscustomertype'];
            $params['object']->sdi = (string) $customer_address['sdi'];
            $params['object']->pec = (string) $customer_address['pec'];
        }
        $this->setAddressParams($params);
    }

    private function insertAddressCustomerType(): bool
    {
        $sql = [];
        $sql[] = 'INSERT INTO `' . _DB_PREFIX_ . 'einvoice_customer_type` (`id_addresscustomertype`,`removable`,`need_invoice`,`date_add`,`date_upd`) VALUES
        (1, 0, 0, NOW(), NOW()),
        (2, 0, 1, NOW(), NOW()),
        (3, 0, 1, NOW(), NOW()),
        (4, 0, 1, NOW(), NOW());';

        foreach (Language::getLanguages() as $lang) {
            $sql[] = 'INSERT INTO ' . _DB_PREFIX_ . 'einvoice_customer_type_lang (`id_addresscustomertype`, `id_lang`, `name`) VALUES '
                . '(1, ' . $lang['id_lang'] . ", '" . $this->trans('Private', [], 'Modules.Electronicinvoicefields.Einvoice', $lang['locale']) . "'),"
                . '(2, ' . $lang['id_lang'] . ", '" . $this->trans('Company/Professional', [], 'Modules.Electronicinvoicefields.Einvoice', $lang['locale']) . "'),"
                . '(3, ' . $lang['id_lang'] . ", '" . $this->trans('Association', [], 'Modules.Electronicinvoicefields.Einvoice', $lang['locale']) . "'),"
                . '(4, ' . $lang['id_lang'] . ", '" . $this->trans('Public Administration', [], 'Modules.Electronicinvoicefields.Einvoice', $lang['locale']) . "');";
        }

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }
}
