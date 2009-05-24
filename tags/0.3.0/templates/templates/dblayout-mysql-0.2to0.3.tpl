-- create groups table

CREATE TABLE {$smarty.const.dbTablePrefix}groups (
	group_id int NOT NULL auto_increment,
	group_name TEXT,
	event_id VARCHAR ({$smarty.const.EVENT_ID_LENGTH}) NOT NULL,
	PRIMARY KEY (group_id),
	UNIQUE KEY group_id (group_id)
);

INSERT INTO {$smarty.const.dbTablePrefix}groups VALUES (1,'DEFAULT_GROUP','-1');

-- update dates table

ALTER TABLE {$smarty.const.dbTablePrefix}dates MODIFY COLUMN date VARCHAR ({$smarty.const.MAX_DATE_LENGTH}) NOT NULL;

ALTER TABLE {$smarty.const.dbTablePrefix}dates ADD COLUMN group_id int default 1;

ALTER TABLE {$smarty.const.dbTablePrefix}dates CHANGE event event_id VARCHAR ({$smarty.const.EVENT_ID_LENGTH}) NOT NULL;

ALTER TABLE {$smarty.const.dbTablePrefix}dates ADD CONSTRAINT FOREIGN KEY (event_id) REFERENCES {$smarty.const.dbTablePrefix}events(event_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE {$smarty.const.dbTablePrefix}dates ADD CONSTRAINT FOREIGN KEY (group_id) REFERENCES {$smarty.const.dbTablePrefix}groups(group_id) ON UPDATE CASCADE ON DELETE CASCADE;

-- update events table

ALTER TABLE {$smarty.const.dbTablePrefix}events MODIFY COLUMN name VARCHAR ({$smarty.const.MAX_EVENT_TITLE_LENGTH}) NOT NULL;

-- update log table

ALTER TABLE {$smarty.const.dbTablePrefix}log ADD CONSTRAINT PRIMARY KEY (entry_id);

ALTER TABLE {$smarty.const.dbTablePrefix}log ADD CONSTRAINT FOREIGN KEY (event_id) REFERENCES {$smarty.const.dbTablePrefix}events(event_id) ON UPDATE CASCADE ON DELETE CASCADE;

-- update people table

ALTER TABLE {$smarty.const.dbTablePrefix}people MODIFY COLUMN name VARCHAR ({$smarty.const.MAX_USER_NAME_LENGTH}) NOT NULL;

ALTER TABLE {$smarty.const.dbTablePrefix}people DROP KEY date_id;

ALTER TABLE {$smarty.const.dbTablePrefix}people ADD CONSTRAINT UNIQUE KEY person_id (person_id);

ALTER TABLE {$smarty.const.dbTablePrefix}people ADD CONSTRAINT FOREIGN KEY (event_id) REFERENCES {$smarty.const.dbTablePrefix}events(event_id) ON UPDATE CASCADE ON DELETE CASCADE;

-- update status table

ALTER TABLE {$smarty.const.dbTablePrefix}status ADD CONSTRAINT UNIQUE KEY (person_id, event_id, date_id);

ALTER TABLE {$smarty.const.dbTablePrefix}status ADD CONSTRAINT FOREIGN KEY (person_id) REFERENCES {$smarty.const.dbTablePrefix}people(person_id) ON UPDATE CASCADE ON DELETE CASCADE

ALTER TABLE {$smarty.const.dbTablePrefix}status ADD CONSTRAINT FOREIGN KEY (event_id) REFERENCES {$smarty.const.dbTablePrefix}events(event_id) ON UPDATE CASCADE ON DELETE CASCADE;
