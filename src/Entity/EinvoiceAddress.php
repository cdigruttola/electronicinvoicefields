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

namespace cdigruttola\Module\Electronicinvoicefields\Entity;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="cdigruttola\Module\Electronicinvoicefields\Repository\EinvoiceAddressRepository")
 *
 * @ORM\Table()
 */
class EinvoiceAddress
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_address", type="integer")
     */
    private $idAddress;

    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_addresscustomertype", type="integer")
     */
    private $idAddressCustomerType;

    /**
     * @var string
     *
     * @ORM\Column(name="pec", type="string")
     */
    private $pec;

    /**
     * @var string
     *
     * @ORM\Column(name="sdi", type="string")
     */
    private $sdi;

    public function getIdAddress(): int
    {
        return $this->idAddress;
    }

    public function setIdAddress(int $idAddress): self
    {
        $this->idAddress = $idAddress;

        return $this;
    }

    public function getIdAddressCustomerType(): int
    {
        return $this->idAddressCustomerType;
    }

    public function setIdAddressCustomerType(int $idAddressCustomerType): self
    {
        $this->idAddressCustomerType = $idAddressCustomerType;

        return $this;
    }

    public function getPec(): string
    {
        return $this->pec;
    }

    public function setPec(string $pec): self
    {
        $this->pec = $pec;

        return $this;
    }

    public function getSdi(): string
    {
        return $this->sdi;
    }

    public function setSdi(string $sdi): self
    {
        $this->sdi = $sdi;

        return $this;
    }
}
