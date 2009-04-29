DROP TABLE IF EXISTS {$smarty.const.dbTablePrefix}events;

CREATE TABLE {$smarty.const.dbTablePrefix}events (
	event_id VARCHAR ({$smarty.const.EVENT_ID_LENGTH}) NOT NULL,
	name varchar ({$smarty.const.MAX_EVENT_TITLE_LENGTH}) NOT NULL,
	owner varchar ({$smarty.const.MAX_MAIL_ADDRESS_LENGTH}) NOT NULL,
	orientation tinyint(1) NOT NULL default 1,
	expires DATE NOT NULL,
	sum_type INT NOT NULL default 0,
	PRIMARY KEY (event_id),
	UNIQUE KEY event_id (event_id)
);

DROP TABLE IF EXISTS {$smarty.const.dbTablePrefix}people;

CREATE TABLE {$smarty.const.dbTablePrefix}people (
	person_id int NOT NULL auto_increment,
	event_id VARCHAR ({$smarty.const.EVENT_ID_LENGTH}) NOT NULL,
	name varchar ({$smarty.const.MAX_USER_NAME_LENGTH}) NOT NULL,
	PRIMARY KEY (person_id),
	UNIQUE KEY person_id (person_id)
);

ALTER TABLE {$smarty.const.dbTablePrefix}people ADD CONSTRAINT FOREIGN KEY (event_id) REFERENCES {$smarty.const.dbTablePrefix}events(event_id) ON UPDATE CASCADE ON DELETE CASCADE;

DROP TABLE IF EXISTS {$smarty.const.dbTablePrefix}groups;

CREATE TABLE {$smarty.const.dbTablePrefix}groups (
	group_id int NOT NULL auto_increment,
	group_name TEXT,
	event_id VARCHAR ({$smarty.const.EVENT_ID_LENGTH}) NOT NULL,
	PRIMARY KEY (group_id),
	UNIQUE KEY group_id (group_id)
);

DROP TABLE IF EXISTS {$smarty.const.dbTablePrefix}dates;

CREATE TABLE {$smarty.const.dbTablePrefix}dates (
	date_id int NOT NULL auto_increment,
	event_id VARCHAR ({$smarty.const.EVENT_ID_LENGTH}) NOT NULL,
	date varchar ({$smarty.const.MAX_DATE_LENGTH}) NOT NULL,
	position int(3) NOT NULL,
	group_id int DEFAULT '{$smarty.const.DEFAULT_GROUP}',
	PRIMARY KEY (date_id),
	UNIQUE KEY date_id (date_id)
);

ALTER TABLE {$smarty.const.dbTablePrefix}dates ADD CONSTRAINT FOREIGN KEY (event_id) REFERENCES {$smarty.const.dbTablePrefix}events(event_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE {$smarty.const.dbTablePrefix}dates ADD CONSTRAINT FOREIGN KEY (group_id) REFERENCES {$smarty.const.dbTablePrefix}groups(group_id) ON UPDATE CASCADE ON DELETE CASCADE;

DROP TABLE IF EXISTS {$smarty.const.dbTablePrefix}status;

CREATE TABLE {$smarty.const.dbTablePrefix}status (
	person_id int NOT NULL,
	event_id VARCHAR ({$smarty.const.EVENT_ID_LENGTH}) NOT NULL,
	date_id int NOT NULL,
	status tinyint(1) NOT NULL,
	UNIQUE KEY (person_id, event_id, date_id)
);

ALTER TABLE {$smarty.const.dbTablePrefix}status ADD CONSTRAINT FOREIGN KEY (person_id) REFERENCES {$smarty.const.dbTablePrefix}people(person_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE {$smarty.const.dbTablePrefix}status ADD CONSTRAINT FOREIGN KEY (event_id) REFERENCES {$smarty.const.dbTablePrefix}events(event_id) ON UPDATE CASCADE ON DELETE CASCADE;

DROP TABLE IF EXISTS {$smarty.const.dbTablePrefix}log;

CREATE TABLE {$smarty.const.dbTablePrefix}log (
	entry_id BIGINT NOT NULL auto_increment,
	event_id VARCHAR ({$smarty.const.EVENT_ID_LENGTH}) NOT NULL,
	action ENUM ('CRE', 'ADD', 'MOD', 'INV') NOT NULL,
	extra VARCHAR ({$smarty.const.EXTRA_FIELD_LENGTH}) NOT NULL,
	time TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	UNIQUE KEY entry_id (entry_id),
	PRIMARY KEY entry_id (entry_id)
);

ALTER TABLE {$smarty.const.dbTablePrefix}log ADD CONSTRAINT FOREIGN KEY (event_id) REFERENCES {$smarty.const.dbTablePrefix}events(event_id) ON UPDATE CASCADE ON DELETE CASCADE;
