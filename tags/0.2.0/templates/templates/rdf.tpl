<rdf:RDF
    xmlns="http://purl.org/rss/1.0/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:admin="http://webns.net/mvcb/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
>
    <channel rdf:about="{$event_url}">
	<title>PLANster: {$event_title}</title>
	<link>{$event_url}</link>
        <description>PLANster event "{$event_title}"</description>
        <dc:language>en</dc:language>
        <dc:date>{$last_change}</dc:date>
	<items>
	    <rdf:Seq>
{foreach from=$items item=change}
		<rdf:li rdf:resource="{$event_url}&amp;ch={$change->getID()}"/>
{/foreach}
	    </rdf:Seq>
	</items>
    </channel>
{foreach from=$items item=change}
    <item rdf:about="{$event_url}&amp;ch={$change->getID()}">
	<title>{$change->getTitle()}</title>
	<link>{$event_url}</link>
	<dc:date>{$change->getDate('Y-m-d\TH:iO')}</dc:date>
	<description>{$change->getDescription()}</description>
    </item>
{/foreach}
</rdf:RDF>
