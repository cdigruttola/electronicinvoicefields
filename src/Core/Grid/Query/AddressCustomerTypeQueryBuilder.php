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

namespace cdigruttola\Module\Electronicinvoicefields\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class AddressCustomerTypeQueryBuilder builds queries to fetch data for address_customer_type grid.
 */
final class AddressCustomerTypeQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $criteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator
     * @param int $contextLangId
     * @param int[] $contextShopIds
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator,
        int $contextLangId,
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->contextLangId = $contextLangId;
        $this->criteriaApplicator = $criteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $searchQueryBuilder = $this->getAddressCustomerTypeQueryBuilder($searchCriteria)
            ->select('c.*, cl.name');

        $this->applySorting($searchQueryBuilder, $searchCriteria);

        $this->criteriaApplicator->applyPagination(
            $searchCriteria,
            $searchQueryBuilder
        );

        return $searchQueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $countQueryBuilder = $this->getAddressCustomerTypeQueryBuilder($searchCriteria)
            ->select('COUNT(*)');

        return $countQueryBuilder;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getAddressCustomerTypeQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'einvoice_customer_type', 'c')
            ->leftJoin(
                'c',
                $this->dbPrefix . 'einvoice_customer_type_lang',
                'cl',
                'c.id_addresscustomertype = cl.id_addresscustomertype AND cl.id_lang = :context_lang_id'
            )
            ->setParameter('context_lang_id', $this->contextLangId);

        $this->applyFilters($searchCriteria->getFilters(), $queryBuilder);

        return $queryBuilder;
    }

    /**
     * Apply filters to address_customer_type query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(array $filters, QueryBuilder $qb)
    {
        $allowedFilters = [
            'id_addresscustomertype',
            'name',
            'active',
            'need_invoice',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, ['id_addresscustomertype', 'active', 'need_invoice'])) {
                $qb->andWhere('c.`' . $filterName . '` = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);
                continue;
            }

            if ($filterName == 'name') {
                $qb->andWhere('cl.`' . $filterName . '` LIKE :' . $filterName);
                $qb->setParameter($filterName, '%' . $filterValue . '%');
            }
        }
    }

    /**
     * Apply sorting so search query builder for address_customer_type.
     *
     * @param QueryBuilder $searchQueryBuilder
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function applySorting(QueryBuilder $searchQueryBuilder, SearchCriteriaInterface $searchCriteria)
    {
        switch ($searchCriteria->getOrderBy()) {
            case 'id_addresscustomertype':
                $orderBy = 'c.' . $searchCriteria->getOrderBy();
                break;
            case 'name':
                $orderBy = 'cl.' . $searchCriteria->getOrderBy();
                break;
            default:
                return;
        }
        $searchQueryBuilder->orderBy($orderBy, $searchCriteria->getOrderWay());
    }
}
