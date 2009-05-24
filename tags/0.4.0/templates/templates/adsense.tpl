{if $smarty.const.adsense_id && $smarty.const.adsense_id != 'adsense_id'}
	<div id="adsense" style="float: left">
		<script type="text/javascript">
		<!--
			var SITE_BASE_URL="{$smarty.const.base_url}";
		-->
		</script>
		<script type="text/javascript"><!--
		google_ad_client = "{$smarty.const.adsense_id}";
		google_ad_width = 180;
		google_ad_height = 150;
		google_ad_format = "180x150_as";
		google_ad_type = "text_image";
		google_ad_channel = "";
		google_color_border = "336699";
		google_color_bg = "ffffff";
		google_color_link = "0000FF";
		google_color_text = "000000";
		google_color_url = "008000";
		//--></script>
		<script type="text/javascript"
		  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>
	</div>
{/if}
