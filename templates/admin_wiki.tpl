{strip}

{jstabs}
	{jstab title="Wiki Features"}
		{form legend="Wiki Features"}
			<input type="hidden" name="page" value="{$page}" />

			{foreach from=$formWikiFeatures key=item item=output}
				<div class="row">
					{formlabel label=`$output.label` for=$item}
					{forminput}
						{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
						{formhelp note=`$output.note` page=`$output.page`}
					{/forminput}
				</div>
			{/foreach}

			<div class="row">
				{formlabel label="Edit Page Sections" for="wiki_section_edit"}
				{forminput}
					<select name="wiki_section_edit" id="wiki_section_edit">
						<option value="0" {if $gBitSystem->getConfig('wiki_section_edit') eq 0}selected="selected"{/if}>{tr}Disabled{/tr}</option>
						<option value="1" {if $gBitSystem->getConfig('wiki_section_edit') eq 1}selected="selected"{/if}>h1</option>
						<option value="2" {if $gBitSystem->getConfig('wiki_section_edit') eq 2}selected="selected"{/if}>h2</option>
					</select>
					{formhelp note="Using this feature, it is possible to edit only sections of your wiki pages ranging from one heading to another. specify what headings you want to use as boundaries."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Tables syntax" for="wiki_tables"}
				{forminput}
					<select name="wiki_tables" id="wiki_tables">
						<option value="old" {if $gBitSystem->getConfig('wiki_tables') eq 'old'}selected="selected"{/if}>{tr}|| for rows{/tr}</option>
						<option value="new" {if $gBitSystem->getConfig('wiki_tables') eq 'new'}selected="selected"{/if}>{tr}\n for rows{/tr}</option>
					</select>
					{formhelp note="Either use || to start a new row in a table, or start a new line (recommended)."}
				{/forminput}
			</div>

			<div class="buttonHolder row submit">
				<input type="submit" name="wikifeatures" value="{tr}Change preferences{/tr}" />
			</div>
		{/form}
	{/jstab}

	{jstab title="Wiki Books"}
		{form legend="Wiki Book Settings"}
			<input type="hidden" name="page" value="{$page}" />

			{foreach from=$formWikiBooks key=item item=output}
				<div class="row">
					{formlabel label=`$output.label` for=$item}
					{forminput}
						{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
						{formhelp note=`$output.note` page=`$output.page`}
					{/forminput}
				</div>
			{/foreach}
			<div class="buttonHolder row submit">
				<input type="submit" name="wikibooks" value="{tr}Change preferences{/tr}" />
			</div>
		{/form}
	{/jstab}

	{jstab title="In and Output"}
		{form legend="Wiki Input and Output Settings"}
			<input type="hidden" name="page" value="{$page}" />

			{foreach from=$formWikiInOut key=item item=output}
				<div class="row">
					{formlabel label=`$output.label` for=$item}
					{forminput}
						{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
						{formhelp note=`$output.note` page=`$output.page`}
					{/forminput}
				</div>
			{/foreach}
			<div class="buttonHolder row submit">
				<input type="submit" name="wikiinout" value="{tr}Change preferences{/tr}" />
			</div>
		{/form}
	{/jstab}

	{jstab title="List Settings"}
		{form legend="List Settings"}
			<input type="hidden" name="page" value="{$page}" />

			{foreach from=$formWikiLists key=item item=output}
				<div class="row">
					{formlabel label=`$output.label` for=$item}
					{forminput}
						{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
						{formhelp note=`$output.note` page=`$output.page`}
					{/forminput}
				</div>
			{/foreach}

			<div class="buttonHolder row submit">
				<input type="submit" name="wikilistconf" value="{tr}Change preferences{/tr}" />
			</div>
		{/form}
	{/jstab}

	{jstab title="Wiki Settings"}
		{form}
			{legend legend="Wiki Home Page"}
				<input type="hidden" name="page" value="{$page}" />
				<div class="row">
					{formlabel label="Disable Wiki Home Page" for="wiki_disable_auto_home"}
					{forminput}
						{html_checkboxes name="wiki_disable_auto_home" values="y" checked=$gBitSystem->getConfig('wiki_disable_auto_home') labels=false id="wiki_disabled_auto_home"}
						{formhelp note="When checked the default wiki url will load a list of recently edited pages. This will disable the auto-fetching of the home page value below."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Wiki Home Page" for="wiki_home_page"}
					{forminput}
						<input type="text" name="wiki_home_page" id="wiki_home_page" size="25" value="{$gBitSystem->getConfig('wiki_home_page')|escape}" />
						{formhelp note="When the wiki is accessed, this is the page that will be displayed as the first page."}
					{/forminput}
				</div>

				<div class="buttonHolder row submit">
					<input type="submit" name="setwikihome" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}

			{legend legend="Wiki Link Format"}
				<div class="row">
					{formlabel label="Wiki Link Format" for="wiki_page_regex"}
					{forminput}
						<select name="wiki_page_regex" id="wiki_page_regex">
							<option value="strict"   {if $gBitSystem->getConfig('wiki_page_regex') eq 'strict'}selected="selected"{/if}>{tr}English{/tr}</option>
							<option value="full"     {if $gBitSystem->getConfig('wiki_page_regex') eq 'full'}selected="selected"{/if}>{tr}Latin{/tr}</option>
							<option value="complete" {if $gBitSystem->getConfig('wiki_page_regex') eq 'complete'}selected="selected"{/if}>{tr}Complete{/tr}</option>
						</select>
						{formhelp note="Controls recognition of Wiki links using the two parenthesis Wiki link syntax <em>((page name))</em>.
							<dl>
								<dt>English</dt><dd>allows only letters, numbers, space, underscore, dash, period and semicolon. <em>Space, dash, period and semicolon not allowed as the first character.</em></dd>
								<dt>Latin</dt><dd>adds accented characters such as äöüåñòôùçèêîâööúä.</dd>
								<dt>Complete</dt><dd>allows any character at all but is not guaranteed to be bug-free or secure.</dd>
							</dl>"}
					{/forminput}
				</div>

				<div class="buttonHolder row submit">
					<input type="submit" name="setwikiregex" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}

			{legend legend="Copyright Management"}
				<div class="row">
					{formlabel label="Enable Feature" for="wiki_copyrights"}
					{forminput}
						{html_checkboxes name="wiki_copyrights" values="y" checked=$gBitSystem->getConfig('wiki_copyrights') labels=false id="wiki_copyrights"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="License Page" for="wiki_license_page"}
					{forminput}
						<input type="text" name="wiki_license_page" id="wiki_license_page" value="{$wiki_license_page|escape}" />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Submit Notice" for="wiki_submit_notice"}
					{forminput}
						<input type="text" name="wiki_submit_notice" id="wiki_submit_notice" value="{$wiki_submit_notice|escape}" />
					{/forminput}
				</div>

				<div class="buttonHolder row submit">
					<input type="submit" name="wikisetcopyright" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}

			{legend legend="Wiki Watch"}
				{formfeedback warning="This feature has been disabled for now since it's not functional."}

				{foreach from=$formWikiWatch key=item item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$item}
						{forminput}
							{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}

				<div class="buttonHolder row submit">
					<input type="submit" name="wikiwatch" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/form}
	{/jstab}
{/jstabs}
{/strip}
