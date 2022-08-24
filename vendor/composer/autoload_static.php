<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc37d6450b0d4011af3ce091ac26f26d8
{
    public static $prefixLengthsPsr4 = array (
        'c' => 
        array (
            'cdigruttola\\Module\\Electronicinvoicefields\\' => 45,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'cdigruttola\\Module\\Electronicinvoicefields\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Addresscustomertype' => __DIR__ . '/../..' . '/classes/Addresscustomertype.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'EInvoiceAddress' => __DIR__ . '/../..' . '/classes/EInvoiceAddress.php',
        'Electronicinvoicefields' => __DIR__ . '/../..',
        'cdigruttola\\Module\\Electronicinvoicefields\\Controller\\Admin\\AdminAddressCustomerTypeController' => __DIR__ . '/../..' . '/src/Controller/Admin/AdminAddressCustomerTypeController.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\AddressCustomerTypeFormDataProvider' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/AddressCustomerTypeFormDataProvider.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\CommandHandler\\AbstractAddressCustomerTypeHandler' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/CommandHandler/AbstractAddressCustomerTypeHandler.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\CommandHandler\\AddAddressCustomerTypeHandler' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/CommandHandler/AddAddressCustomerTypeHandler.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\CommandHandler\\AddAddressCustomerTypeHandlerInterface' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/CommandHandler/AddAddressCustomerTypeHandlerInterface.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\CommandHandler\\EditAddressCustomerTypeHandler' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/CommandHandler/EditAddressCustomerTypeHandler.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\CommandHandler\\EditAddressCustomerTypeHandlerInterface' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/CommandHandler/EditAddressCustomerTypeHandlerInterface.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\CommandHandler\\ToggleStatusAddressCustomerTypeHandler' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/CommandHandler/ToggleStatusAddressCustomerTypeHandler.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\CommandHandler\\ToggleStatusAddressCustomerTypeHandlerInterface' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/CommandHandler/ToggleStatusAddressCustomerTypeHandlerInterface.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\Command\\AddAddressCustomerTypeCommand' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/Command/AddAddressCustomerTypeCommand.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\Command\\EditAddressCustomerTypeCommand' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/Command/EditAddressCustomerTypeCommand.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\Command\\ToggleStatusAddressCustomerTypeCommand' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/Command/ToggleStatusAddressCustomerTypeCommand.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\Exception\\AddressCustomerTypeConstraintException' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/Exception/AddressCustomerTypeConstraintException.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\Exception\\AddressCustomerTypeException' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/Exception/AddressCustomerTypeException.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\Exception\\AddressCustomerTypeNotFoundException' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/Exception/AddressCustomerTypeNotFoundException.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\Exception\\CannotToggleStatusAddressCustomerTypeException' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/Exception/CannotToggleStatusAddressCustomerTypeException.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\Exception\\DuplicateAddressCustomerTypeNameException' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/Exception/DuplicateAddressCustomerTypeNameException.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\Exception\\InvalidAddressCustomerTypeIdException' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/Exception/InvalidAddressCustomerTypeIdException.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\Exception\\InvalidAddressCustomerTypeRequiredFieldsException' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/Exception/InvalidAddressCustomerTypeRequiredFieldsException.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\Exception\\MissingAddressCustomerTypeRequiredFieldsException' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/Exception/MissingAddressCustomerTypeRequiredFieldsException.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\QueryHandler\\GetAddressCustomerTypeForEditingHandler' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/QueryHandler/GetAddressCustomerTypeForEditingHandler.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\QueryHandler\\GetAddressCustomerTypeForEditingHandlerInterface' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/QueryHandler/GetAddressCustomerTypeForEditingHandlerInterface.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\QueryResult\\EditableAddressCustomerType' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/QueryResult/EditableAddressCustomerType.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\Query\\GetAddressCustomerTypeForEditing' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/Query/GetAddressCustomerTypeForEditing.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\ValueObject\\AddressCustomerTypeId' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/ValueObject/AddressCustomerTypeId.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Domain\\AddressCustomerType\\ValueObject\\Name' => __DIR__ . '/../..' . '/src/Core/Domain/AddressCustomerType/ValueObject/Name.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Grid\\Definition\\Factory\\AddressCustomerTypeGridDefinitionFactory' => __DIR__ . '/../..' . '/src/Core/Grid/Definition/Factory/AddressCustomerTypeGridDefinitionFactory.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Grid\\Query\\AddressCustomerTypeQueryBuilder' => __DIR__ . '/../..' . '/src/Core/Grid/Query/AddressCustomerTypeQueryBuilder.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Core\\Search\\Filters\\AddressCustomerTypeFilters' => __DIR__ . '/../..' . '/src/Core/Search/Filters/AddressCustomerTypeFilters.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Form\\Admin\\AddressCustomerTypeType' => __DIR__ . '/../..' . '/src/Form/Admin/AddressCustomerTypeType.php',
        'cdigruttola\\Module\\Electronicinvoicefields\\Form\\DataHandler\\AddressCustomerTypeFormDataHandler' => __DIR__ . '/../..' . '/src/Form/DataHandler/AddressCustomerTypeFormDataHandler.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc37d6450b0d4011af3ce091ac26f26d8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc37d6450b0d4011af3ce091ac26f26d8::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc37d6450b0d4011af3ce091ac26f26d8::$classMap;

        }, null, ClassLoader::class);
    }
}
