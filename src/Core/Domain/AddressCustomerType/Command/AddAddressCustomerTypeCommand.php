<?php
/**
 * 2007-2022 Carmine Di Gruttola
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
 * @copyright 2007-2022 Carmine Di Gruttola
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

declare(strict_types=1);

namespace cdigruttola\Module\Electronic_invoice_fields\Core\Domain\AddressCustomerType\Command;

use cdigruttola\Module\Electronic_invoice_fields\Core\Domain\AddressCustomerType\Exception\AddressCustomerTypeConstraintException;

/**
 * Adds new address customer type with provided data
 */
class AddAddressCustomerTypeCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;
    /**
     * @var bool
     */
    private $active;

    public function __construct(
        array $localizedNames, bool $active)
    {
        $this->setLocalizedNames($localizedNames);
        $this->setActive($active);
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param string[] $localizedNames
     *
     * @return $this
     *
     * @throws AddressCustomerTypeConstraintException
     */
    public function setLocalizedNames(array $localizedNames)
    {
        if (empty($localizedNames)) {
            throw new AddressCustomerTypeConstraintException('Address customer name name cannot be empty', AddressCustomerTypeConstraintException::EMPTY_NAME);
        }

        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

}
