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

namespace cdigruttola\Module\Electronicinvoicefields\Form\Admin;

if (!defined('_PS_VERSION_')) {
    exit;
}

use cdigruttola\Module\Electronicinvoicefields\Form\DataConfiguration\ConfigurationDataConfiguration;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;

class ConfigurationType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pec_required', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('PEC field required', 'Modules.Electronicinvoicefields.Einvoice'),
                'help' => $this->trans('This options set the PEC field mandatory only for Italian customer.', 'Modules.Electronicinvoicefields.Einvoice'),
                'multistore_configuration_key' => ConfigurationDataConfiguration::EINVOICE_PEC_REQUIRED,
            ])
            ->add('sdi_required', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('SDI field required', 'Modules.Electronicinvoicefields.Einvoice'),
                'help' => $this->trans('This options set the SDI field mandatory only for Italian customer.', 'Modules.Electronicinvoicefields.Einvoice'),
                'multistore_configuration_key' => ConfigurationDataConfiguration::EINVOICE_SDI_REQUIRED,
            ])
            ->add('vat_vies_validate', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('VAT Code validation with VIES', 'Modules.Electronicinvoicefields.Einvoice'),
                'help' => $this->trans('This options set enable the VAT Code validation with VIES.', 'Modules.Electronicinvoicefields.Einvoice'),
                'multistore_configuration_key' => ConfigurationDataConfiguration::EINVOICE_VAT_VIES_VALIDATE,
            ])
            ->add('dni_validate', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('DNI field validation', 'Modules.Electronicinvoicefields.Einvoice'),
                'help' => $this->trans('This options set enable the DNI validation only for Italian customer.', 'Modules.Electronicinvoicefields.Einvoice'),
                'multistore_configuration_key' => ConfigurationDataConfiguration::EINVOICE_DNI_VALIDATE,
            ])
            ->add('api_token_miocodicefiscale', TextType::class, [
                'required' => false,
                'label' => $this->trans('Mio Codice Fiscale API Token', 'Modules.Electronicinvoicefields.Einvoice'),
                'help' => $this->trans('Use https://www.miocodicefiscale.com/it/api-rest-verifica-e-calcolo-codice-fiscale API to validate DNI.', 'Modules.Electronicinvoicefields.Einvoice'),
                'multistore_configuration_key' => ConfigurationDataConfiguration::EINVOICE_DNI_VALIDATE_MIOCODICEFISCALE_API,
            ])
            ->add('check_user_age', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Check user age', 'Modules.Electronicinvoicefields.Einvoice'),
                'help' => $this->trans('This options set enable the check of user age during registration.', 'Modules.Electronicinvoicefields.Einvoice'),
                'multistore_configuration_key' => ConfigurationDataConfiguration::EINVOICE_CHECK_USER_AGE,
            ])
            ->add('minumum_age', NumberType::class, [
                'required' => false,
                'label' => $this->trans('Minimum age for user', 'Modules.Electronicinvoicefields.Einvoice'),
                'help' => $this->trans('Minimum age for customer, if not set default is 16', 'Modules.Electronicinvoicefields.Einvoice'),
                'multistore_configuration_key' => ConfigurationDataConfiguration::EINVOICE_MINIMUM_USER_AGE,
                'constraints' => [
                    new Range([
                        'min' => 16,
                    ]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
