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
class ElectronicinvoicefieldsAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->ajax = true;
        parent::initContent();
    }

    /**
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    public function displayAjax()
    {
        header('Content-Type: application/json');
        $value = Tools::getValue('id');
        if (!empty($value)) {
            $cacheId = 'Addresscustomertype::needInvoice_' . $value;
            if (!Cache::isStored($cacheId)) {
                $addressCustomerType = new Addresscustomertype((int) $value);
                $var = ['need_invoice' => (bool) $addressCustomerType->need_invoice];
                Cache::store($cacheId, $var);
            }
            $json_encode = json_encode(Cache::retrieve($cacheId));
            unset($addressCustomerType);
        } else {
            $value = Tools::getValue('id_address');
            if (!empty($value)) {
                $cacheId = 'Address::needInvoice_' . $value;
                if (!Cache::isStored($cacheId)) {
                    $address = new Address((int) $value);
                    $var = ['need_invoice' => (bool) $address->needInvoice()];
                    Cache::store($cacheId, $var);
                }
                $json_encode = json_encode(Cache::retrieve($cacheId));
                unset($address);
            } else {
                $json_encode = json_encode(['need_invoice' => false]);
            }
        }
        $this->ajaxRender($json_encode);
    }
}
