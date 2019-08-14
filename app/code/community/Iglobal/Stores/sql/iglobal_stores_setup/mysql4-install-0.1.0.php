<?php

$installer = $this;

// This whole /sql setup idea is the most awesome way to make updates and new tables in the DB
$installer->startSetup();
//$installer->run('ALTER TABLE `sales_flat_order` ADD `international_order` BOOLEAN NOT NULL DEFAULT 0');

$installer->getConnection()
    ->addColumn($installer->getTable('sales_flat_order'),
        'international_order', "BOOLEAN NOT NULL DEFAULT 0" 

    );
$installer->getConnection()    
    ->addColumn($installer->getTable('sales_flat_order'),
        'ig_order_number',  "VARCHAR( 15 ) NULL DEFAULT NULL , ADD INDEX ( `ig_order_number` )" 

    );


//$installer->run('ALTER TABLE `sales_flat_order` ADD `ig_order_number` VARCHAR( 15 ) NULL DEFAULT NULL , ADD INDEX ( `ig_order_number` )');


$installer->createAttribute('iGlobal Length','ig_length', 'text');
$installer->createAttribute('iGlobal Width', 'ig_width', 'text');
$installer->createAttribute('iGlobal Height', 'ig_height', 'text');
$installer->createAttribute('iGlobal Weight', 'ig_weight', 'text');
    $weightUnits['value']['option_1'][0] = 'lbs';
    $weightUnits['value']['option_2'][0] = 'kg';
    $weightUnits['value']['option_3'][0] = 'oz';
    $weightUnits['value']['option_4'][0] = 'g';
$installer->createAttribute('iGlobal Weight Units','ig_weight_units','select', $weightUnits );
    $dimUnits['value']['option_1'][0] = 'in';
    $dimUnits['value']['option_2'][0] = 'cm';
$installer->createAttribute('iGlobal Dimension Units','ig_dimension_units','select', $dimUnits );
$installer->endSetup();