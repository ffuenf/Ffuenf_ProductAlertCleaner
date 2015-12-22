-- add table prefix if you have one
DELETE FROM core_config_data WHERE path like 'ffuenf_productalertcleaner/%';
DELETE FROM core_config_data WHERE path = 'advanced/modules_disable_output/Ffuenf_ProductAlertCleaner';
DELETE FROM core_resource WHERE code = 'ffuenf_productalertcleaner_setup';