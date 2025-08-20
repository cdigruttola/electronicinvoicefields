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

namespace cdigruttola\Module\Electronicinvoicefields\Form\DataConfiguration;

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConfigurationDataConfiguration extends AbstractMultistoreConfiguration
{
    public const EINVOICE_PEC_REQUIRED = 'EINVOICE_PEC_REQUIRED';
    public const EINVOICE_SDI_REQUIRED = 'EINVOICE_SDI_REQUIRED';
    public const EINVOICE_VAT_VIES_VALIDATE = 'EINVOICE_VAT_VIES_VALIDATE';
    public const EINVOICE_DNI_VALIDATE = 'EINVOICE_DNI_VALIDATE';
    public const EINVOICE_DNI_VALIDATE_MIOCODICEFISCALE_API = 'EINVOICE_DNI_VALIDATE_MIOCODICEFISCALE_API';
    public const EINVOICE_CHECK_USER_AGE = 'EINVOICE_CHECK_USER_AGE';
    public const EINVOICE_MINIMUM_USER_AGE = 'EINVOICE_MINIMUM_USER_AGE';
    private const CONFIGURATION_FIELDS = [
        'pec_required',
        'sdi_required',
        'vat_vies_validate',
        'dni_validate',
        'api_token_miocodicefiscale',
        'check_user_age',
        'minumum_age',
    ];

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('pec_required', 'bool')
            ->setAllowedTypes('sdi_required', 'bool')
            ->setAllowedTypes('vat_vies_validate', 'bool')
            ->setAllowedTypes('dni_validate', 'bool')
            ->setAllowedTypes('check_user_age', 'bool')
            ->setAllowedTypes('api_token_miocodicefiscale', 'string')
            ->setAllowedTypes('minumum_age', 'int');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        $return = [];
        $shopConstraint = $this->getShopConstraint();

        $return['pec_required'] = $this->configuration->get(self::EINVOICE_PEC_REQUIRED, false, $shopConstraint);
        $return['sdi_required'] = $this->configuration->get(self::EINVOICE_SDI_REQUIRED, false, $shopConstraint);
        $return['vat_vies_validate'] = $this->configuration->get(self::EINVOICE_VAT_VIES_VALIDATE, false, $shopConstraint);
        $return['dni_validate'] = $this->configuration->get(self::EINVOICE_DNI_VALIDATE, false, $shopConstraint);
        $return['check_user_age'] = $this->configuration->get(self::EINVOICE_CHECK_USER_AGE, false, $shopConstraint);
        $return['api_token_miocodicefiscale'] = $this->configuration->get(self::EINVOICE_DNI_VALIDATE_MIOCODICEFISCALE_API, '', $shopConstraint);
        $return['minumum_age'] = $this->configuration->get(self::EINVOICE_MINIMUM_USER_AGE, 16, $shopConstraint);

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration): array
    {
        $shopConstraint = $this->getShopConstraint();
        $this->updateConfigurationValue(self::EINVOICE_PEC_REQUIRED, 'pec_required', $configuration, $shopConstraint);
        $this->updateConfigurationValue(self::EINVOICE_SDI_REQUIRED, 'sdi_required', $configuration, $shopConstraint);
        $this->updateConfigurationValue(self::EINVOICE_VAT_VIES_VALIDATE, 'vat_vies_validate', $configuration, $shopConstraint);
        $this->updateConfigurationValue(self::EINVOICE_DNI_VALIDATE, 'dni_validate', $configuration, $shopConstraint);
        $this->updateConfigurationValue(self::EINVOICE_CHECK_USER_AGE, 'check_user_age', $configuration, $shopConstraint);
        $this->updateConfigurationValue(self::EINVOICE_DNI_VALIDATE_MIOCODICEFISCALE_API, 'api_token_miocodicefiscale', $configuration, $shopConstraint);
        $this->updateConfigurationValue(self::EINVOICE_MINIMUM_USER_AGE, 'minumum_age', $configuration, $shopConstraint);

        return [];
    }
}
