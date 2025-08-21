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
if (!defined('_PS_VERSION_')) {
    exit;
}

class AddressFormat extends AddressFormatCore
{
    public static function getFormattedAddressFieldsValues($address, $addressFormat, $id_lang = null)
    {
        $tab = parent::getFormattedAddressFieldsValues($address, $addressFormat, $id_lang);

        $einvoice = Module::getInstanceByName('electronicinvoicefields');
        if ($einvoice && $einvoice->active) {
            if (isset($tab['sdi'])) {
                if (in_array((string) $tab['sdi'], ['000000', '0000000', 'XXXXXX', 'XXXXXXX'])) {
                    $tab['sdi'] = '';
                }
            }
        }

        return $tab;
    }

    public static function getFieldsRequired()
    {
        $address = new CustomerAddress();
        $einvoice = Module::getInstanceByName('electronicinvoicefields');
        if ($einvoice && $einvoice->active) {
            $id_shop = (int) Context::getContext()->shop->id;
            $type = new Addresscustomertype($address->id_addresscustomertype);
            if ($type->need_invoice && (int) Configuration::get('EINVOICE_PEC_REQUIRED', null, null, $id_shop)) {
                AddressFormat::$requireFormFieldsList[] = 'pec';
            }
            if ($type->need_invoice && (int) Configuration::get('EINVOICE_SDI_REQUIRED', null, null, $id_shop)) {
                AddressFormat::$requireFormFieldsList[] = 'sdi';
            }
        }

        return array_unique(array_merge($address->getFieldsRequiredDB(), AddressFormat::$requireFormFieldsList));
    }
}
