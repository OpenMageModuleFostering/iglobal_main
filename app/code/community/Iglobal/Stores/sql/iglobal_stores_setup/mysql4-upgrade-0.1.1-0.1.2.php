<?php


$installer = $this;

//add test order flag attribute to order table.
$installer->startSetup();
$installer->run('ALTER TABLE `sales_flat_order` ADD `iglobal_test_order` BOOLEAN NOT NULL DEFAULT 0');
$installer->endSetup();