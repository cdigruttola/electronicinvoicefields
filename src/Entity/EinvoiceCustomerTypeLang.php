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
use PrestaShopBundle\Entity\Lang;

/**
 * @ORM\Table()
 *
 * @ORM\Entity
 */
class EinvoiceCustomerTypeLang
{
    /**
     * @var EinvoiceCustomerType
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="cdigruttola\Module\Electronicinvoicefields\Entity\EinvoiceCustomerType", inversedBy="nameLangs")
     *
     * @ORM\JoinColumn(name="id_addresscustomertype", referencedColumnName="id_addresscustomertype", nullable=false)
     */
    private $einvoiceCustomerType;
    /**
     * @var Lang
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     *
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, onDelete="CASCADE")
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    public function getEinvoiceCustomerType(): EinvoiceCustomerType
    {
        return $this->einvoiceCustomerType;
    }

    public function setEinvoiceCustomerType(EinvoiceCustomerType $einvoiceCustomerType): self
    {
        $this->einvoiceCustomerType = $einvoiceCustomerType;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $title): self
    {
        $this->name = $title;

        return $this;
    }

    public function getLang(): Lang
    {
        return $this->lang;
    }

    public function setLang(Lang $lang): self
    {
        $this->lang = $lang;

        return $this;
    }
}
