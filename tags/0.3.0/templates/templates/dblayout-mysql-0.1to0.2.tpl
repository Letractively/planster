CREATE TABLE {$smarty.const.dbTablePrefix}log (
	entry_id BIGINT NOT NULL auto_increment,
	event_id VARCHAR ({$smarty.const.EVENT_ID_LENGTH}) NOT NULL,
	action ENUM ('CRE', 'ADD', 'MOD', 'INV') NOT NULL,
	extra VARCHAR ({$smarty.const.EXTRA_FIELD_LENGTH}) NOT NULL,
	time TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	UNIQUE KEY entry_id (entry_id)
);

ALTER TABLE {$smarty.const.dbTablePrefix}dates MODIFY COLUMN date_id int auto_increment;

ALTER TABLE {$smarty.const.dbTablePrefix}people MODIFY COLUMN person_id int auto_increment;

ALTER TABLE {$smarty.const.dbTablePrefix}status MODIFY COLUMN person_id int NOT NULL;

ALTER TABLE {$smarty.const.dbTablePrefix}status MODIFY COLUMN date_id int NOT NULL;
