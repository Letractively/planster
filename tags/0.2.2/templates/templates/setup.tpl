		<ul>
{foreach from=$tests key=id item=test}
			<li>
				<span class="query">{$test}</span>
{if $errors.$id}
				<br />
				<span class="error">{$errors.$id}</span>
{/if}
			</li>
{/foreach}
		</ul>
		<p id="navigation"><a href="?initdb">Initiailze database</a></p>
