-- update events table

ALTER TABLE {$smarty.const.dbTablePrefix}events ADD COLUMN sum_type INT NOT NULL DEFAULT 0;
