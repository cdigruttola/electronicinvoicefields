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

use cdigruttola\Module\Electronicinvoicefields\Form\DataConfiguration\ConfigurationDataConfiguration;
use Symfony\Contracts\Translation\TranslatorInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ValidateConstraintTranslator extends ValidateConstraintTranslatorCore
{
    private $translator;

    /**
     * ValidateConstraintTranslatorCore constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->translator = $translator;
    }

    public function translate($validator)
    {
        $einvoice = Module::getInstanceByName('electronicinvoicefields');
        if (isset($einvoice) && isset($einvoice->active) && $einvoice->active) {
            if ($validator === 'isBirthDate') {
                $id_shop = (int) Context::getContext()->shop->id;

                return $this->translator->trans(
                    'Format should be %s and your age must be greater then %s.',
                    [Tools::formatDateStr('31 May 1970'),
                        Configuration::get(ConfigurationDataConfiguration::EINVOICE_MINIMUM_USER_AGE, null, null, $id_shop), ],
                    'Modules.Electronicinvoicefields.Einvoice'
                );
            }
        }

        return parent::translate($validator);
    }
}
