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
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Validate extends ValidateCore
{
    const VIES_URL = 'https://ec.europa.eu/taxation_customs/vies/rest-api/ms/%iso%/vat/%vat%';
    const MIOCODICEFISCALE_URL = 'http://api.miocodicefiscale.com/reverse?cf=%dni%&access_token=%api%';
    const VIES_COUNTRY = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'EL', 'ES', 'FI', 'FR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK', 'XI'];

    /**
     * @throws Exception
     */
    public static function isBirthDate($date, $format = 'Y-m-d')
    {
        $toReturn = parent::isBirthDate($date, $format);

        $einvoice = Module::getInstanceByName('electronicinvoicefields');
        if (isset($einvoice) && isset($einvoice->active) && $einvoice->active) {
            $id_shop = (int) Context::getContext()->shop->id;
            if (Configuration::get(ConfigurationDataConfiguration::EINVOICE_CHECK_USER_AGE, null, null, $id_shop)) {
                $minimum = (int) Configuration::get(ConfigurationDataConfiguration::EINVOICE_MINIMUM_USER_AGE, null, null, $id_shop);
                $d = DateTime::createFromFormat($format, $date);
                if (!empty(DateTime::getLastErrors()['warning_count']) || false === $d) {
                    return false;
                }
                $d->add(new DateInterval('P' . $minimum . 'Y'));

                return $toReturn && $d->setTime(0, 0, 0)->getTimestamp() <= time();
            }
        }

        return $toReturn;
    }

    /**
     * @throws Exception
     */
    public static function checkDNICode($dni, $api)
    {
        $dni = Tools::strtoupper($dni);
        $regex = '/^[a-zA-Z]{6}[0-9]{2}[AaBbCcDdEeHhLlMmPpRrSsTt][0-9]{2}[a-zA-Z][0-9LlQqUuMmRrVvNnSsPpTt]{3}[a-zA-Z]$/';
        if (empty($dni) || Tools::strlen($dni) !== 16) {
            return false;
        }
        if (!preg_match($regex, $dni)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < Tools::strlen($dni) - 1; ++$i) {
            $extracted = $dni[$i];
            if ($i % 2 === 0) {
                $sum += OddPositionTranslationTable::getValue($extracted);
            } else {
                $sum += EvenPositionAndControlCharTranslationTable::getValue($extracted);
            }
        }

        $divisionRemainder = $sum % 26;
        $controlChar = EvenPositionAndControlCharTranslationTable::fromOrdinal($divisionRemainder);
        if ($controlChar !== $dni[Tools::strlen($dni) - 1]) {
            return false;
        }

        if ($api !== '') {
            $url = self::MIOCODICEFISCALE_URL;
            $url = str_replace(['%api%', '%dni%'], [$api, $dni], $url);
            try {
                $client = HttpClient::create();
                $response = $client->request('GET', $url);
                $data = json_decode($response->getContent(), true);
                return $data['status'];
            } catch (ClientException | ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
                $response = $e->getResponse();
                PrestaShopLogger::addLog('Status error ' . $response->getStatusCode() . ', reason ' . self::getReasonPhraseFromResponse($response));
            }
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public static function checkVatNumber($vat_number, $country_iso)
    {
        $country_iso = Tools::strtoupper($country_iso);
        if (!in_array($country_iso, self::VIES_COUNTRY)) {
            PrestaShopLogger::addLog('No VIES Verification for ' . $country_iso);

            return true;
        }

        $vat_number = Tools::strtoupper($vat_number);

        $url = self::VIES_URL;
        $url = str_replace(['%iso%', '%vat%'], [$country_iso, $vat_number], $url);
        try {
            $client = HttpClient::create();
            $response = $client->request('GET', $url);
            $data = json_decode($response->getContent(), true);
            return $data['isValid'];
        } catch (ClientException | ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            $response = $e->getResponse();
            PrestaShopLogger::addLog('Status error ' . $response->getStatusCode() . ', reason ' . self::getReasonPhraseFromResponse($response));

            return false;
        }
    }

    private static function getReasonPhraseFromResponse(ResponseInterface $response): string
    {
        $statusCode = $response->getStatusCode();

        return Response::$statusTexts[$statusCode] ?? 'GENERIC_ERROR';
    }

}
