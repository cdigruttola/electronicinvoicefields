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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="cdigruttola\Module\Electronicinvoicefields\Repository\EinvoiceCustomerTypeRepository")
 *
 * @ORM\Table()
 */
class EinvoiceCustomerType
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_addresscustomertype", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="removable", type="boolean")
     */
    private $removable;

    /**
     * @var bool
     *
     * @ORM\Column(name="need_invoice", type="boolean")
     */
    private $needInvoice;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="EinvoiceCustomerTypeLang", cascade={"persist", "remove"}, mappedBy="einvoiceCustomerType")
     */
    private $nameLangs;

    public function __construct()
    {
        $this->nameLangs = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function isRemovable(): bool
    {
        return $this->removable;
    }

    public function setRemovable(bool $removable): self
    {
        $this->removable = $removable;

        return $this;
    }

    public function isNeedInvoice(): bool
    {
        return $this->needInvoice;
    }

    public function setNeedInvoice(bool $needInvoice): self
    {
        $this->needInvoice = $needInvoice;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getNameLangs(): Collection
    {
        return $this->nameLangs;
    }

    public function getNameLangByLangId(int $langId): ?EinvoiceCustomerTypeLang
    {
        foreach ($this->nameLangs as $nameLang) {
            if ($langId === $nameLang->getLang()->getId()) {
                return $nameLang;
            }
        }

        return null;
    }

    public function addNameLang(EinvoiceCustomerTypeLang $nameLang): self
    {
        $nameLang->setEinvoiceCustomerType($this);
        $this->nameLangs->add($nameLang);

        return $this;
    }

}
