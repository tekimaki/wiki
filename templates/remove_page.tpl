{strip}
<div class="display wiki">
	<div class="header">
		<h1>{tr}Remove page{/tr}</h1>
	</div>

	<div class="body">

		{form legend="Remove page: $page"}
			<input type="hidden"  name="page" value="{$page|escape}" />
			<input type="hidden" name="version" value="{$version|escape}" />

			{formfeedback warning="These actions are permanent and cannot be undone"}

			<div class="row">
				{formlabel label="Page to remove"}
				{forminput}
					{$page}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Version"}
				{forminput}
					{$version}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Remove all versions" for="all"}
				{forminput}
					<input type="checkbox" name="all" id="all" />
				{/forminput}
			</div>

			<div class="buttonHolder row submit">
				<input type="submit" name="remove" value="{tr}remove{/tr}" />
			</div>
		{/form}
	</div> <!-- end .body -->
</div> <!-- end .wiki -->
{/strip}
