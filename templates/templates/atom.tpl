<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<title>PLANster: {$event_title}</title>
	<subtitle>PLANster event "{$event_title}"</subtitle>
	<link href="{$event_url}"/>
	<link rel="self" href="{$event_url}/feed"/>
	<updated>{$latest->getDate(true)}</updated>
	<author>
		<name>Stefan Ott</name>
	</author>
	<id>urn:uuid:planster-{$smarty.const.base_url|urlencode}-event-{$event_id}-feed</id>
{foreach from=$items item=change}
	<entry>
		<title>{$change->getTitle()}</title>
		<link rel="alternate" href="{$event_url}"/>
		<id>urn:uuid:planster-{$smarty.const.base_url|urlencode}-event-{$event_id}-change-{$change->getID()}</id>
		<updated>{$change->getDate(true)}</updated>
		<content>{$change->getDescription()}</content>
	</entry>
{/foreach}
</feed>
