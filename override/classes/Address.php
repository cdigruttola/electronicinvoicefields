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

require_once _PS_MODULE_DIR_ . '/electronicinvoicefields/vendor/autoload.php';
class Address extends AddressCore
{
    /** @var int Customer Type */
    public $id_addresscustomertype;

    /** @var string SDI */
    public $sdi;

    /** @var string PEC */
    public $pec;

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function __construct($id_address = null, $id_lang = null)
    {
        parent::__construct($id_address, $id_lang);

        $einvoice = Module::getInstanceByName('electronicinvoicefields');
        if (isset($einvoice) && isset($einvoice->active) && $einvoice->active) {
            $eiaddress = new EInvoiceAddress($this->id);
            if ($eiaddress->id_address) {
                $this->id_addresscustomertype = $eiaddress->id_addresscustomertype;
                $this->sdi = $eiaddress->sdi;
                $this->pec = $eiaddress->pec;
            }
            unset($eiaddress);
        }
    }

    /**
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function needInvoice(): bool
    {
        $address_type = new Addresscustomertype($this->id_addresscustomertype);

        return $address_type->need_invoice;
    }
}
