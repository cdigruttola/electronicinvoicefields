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

// the name isn't in UpperCamelCase because of AddressFormat::_checkLiableAssociation calls on _checkValidateClassField
class Addresscustomertype extends ObjectModel
{
    /** @var array<string> name */
    public $name;
    /**
     * @var bool
     */
    public $removable;
    /**
     * @var bool
     */
    public $active;
    /**
     * @var bool
     */
    public $need_invoice;
    /**
     * @var string
     */
    public $date_add;
    /**
     * @var string
     */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'einvoice_customer_type',
        'primary' => 'id_addresscustomertype',
        'multilang' => true,
        'fields' => [
            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 40],
            'removable' => ['type' => self::TYPE_BOOL, 'required' => true, 'validate' => 'isBool'],
            'active' => ['type' => self::TYPE_BOOL, 'required' => true, 'validate' => 'isBool'],
            'need_invoice' => ['type' => self::TYPE_BOOL, 'required' => true, 'validate' => 'isBool'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /**
     * @param $idLang
     * @param bool $activeOnly
     *
     * @return array
     *
     * @throws PrestaShopDatabaseException
     */
    public static function getAddressCustomerType($idLang, bool $activeOnly = true): array
    {
        $customerTypes = [];
        $sql = 'SELECT c.*, cl.`name`
		FROM `' . _DB_PREFIX_ . 'einvoice_customer_type` c
		LEFT JOIN `' . _DB_PREFIX_ . 'einvoice_customer_type_lang` cl ON (c.`id_addresscustomertype` = cl.`id_addresscustomertype` AND cl.`id_lang` = ' . (int) $idLang . ')';
        if ($activeOnly) {
            $sql .= ' WHERE c.`active` = 1 ';
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        foreach ($result as $row) {
            $customerTypes[$row['id_addresscustomertype']] = $row;
        }

        return $customerTypes;
    }

    /**
     * @param $addressCustomerTypeId
     *
     * @return bool
     */
    public static function checkAssociatedAddressToAddressCustomerType($addressCustomerTypeId): bool
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(DISTINCT a.id_address)
		FROM `' . _DB_PREFIX_ . 'einvoice_customer_type` c
		JOIN `' . _DB_PREFIX_ . 'einvoice_address` a ON a.`id_addresscustomertype` = c.`id_addresscustomertype`
		WHERE c.`id_addresscustomertype` = ' . $addressCustomerTypeId);

        return $result > 0;
    }
}
