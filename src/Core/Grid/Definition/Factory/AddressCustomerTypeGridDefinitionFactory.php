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

namespace cdigruttola\Module\Electronicinvoicefields\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractFilterableGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class OrderStatesGridDefinitionFactory defines order_states grid structure.
 */
final class AddressCustomerTypeGridDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    public const GRID_ID = 'address_customer_type';

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Address Customer Type', [], 'Modules.Electronicinvoicefields.Einvoice');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add(
                (new DataColumn('id_addresscustomertype'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_addresscustomertype',
                    ])
            )
            ->add(
                (new DataColumn('name'))
                ->setName($this->trans('Customer Type', [], 'Modules.Electronicinvoicefields.Einvoice'))
                ->setOptions([
                    'field' => 'name',
                ])
            )
            ->add(
                (new ToggleColumn('active'))
                    ->setName($this->trans('Active', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'active',
                        'primary_field' => 'id_addresscustomertype',
                        'route' => 'admin_address_customer_type_toggle_status',
                        'route_param_name' => 'addressCustomerTypeId',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new ToggleColumn('need_invoice'))
                    ->setName($this->trans('Need Invoice', [], 'Modules.Electronicinvoicefields.Einvoice'))
                    ->setOptions([
                        'field' => 'need_invoice',
                        'primary_field' => 'id_addresscustomertype',
                        'route' => 'admin_address_customer_type_toggle_need_invoice',
                        'route_param_name' => 'addressCustomerTypeId',
                        'sortable' => false,
                    ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add(
                            (new LinkRowAction('edit'))
                                ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                ->setIcon('edit')
                                ->setOptions([
                                    'route' => 'admin_address_customer_type_edit',
                                    'route_param_name' => 'addressCustomerTypeId',
                                    'route_param_field' => 'id_addresscustomertype',
                                    'clickable_row' => true,
                                ])
                        )
                        ->add(
                            (new SubmitRowAction('delete'))
                                ->setName($this->trans('Delete', [], 'Admin.Actions'))
                                ->setIcon('delete')
                                ->setOptions([
                                    'confirm_message' => 'Delete selected item?',
                                    'route' => 'admin_address_customer_type_delete',
                                    'route_param_name' => 'addressCustomerTypeId',
                                    'route_param_field' => 'id_addresscustomertype',
                                ])
                        ),
                ]));

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $filters = (new FilterCollection())
            ->add(
                (new Filter('id_addresscustomertype', NumberType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_addresscustomertype')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search name', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('active', YesAndNoChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'choice_translation_domain' => false,
                    ])
                    ->setAssociatedColumn('active')
            )
            ->add(
                (new Filter('need_invoice', YesAndNoChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'choice_translation_domain' => false,
                    ])
                    ->setAssociatedColumn('need_invoice')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_address_customer_type',
                    ])
                    ->setAssociatedColumn('actions')
            );

        return $filters;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                    ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                    ->setIcon('refresh')
            )
            ->add(
                (new SimpleGridAction('common_show_query'))
                    ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                    ->setIcon('code')
            )
            ->add(
                (new SimpleGridAction('common_export_sql_manager'))
                    ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
                    ->setIcon('storage')
            );
    }
}
