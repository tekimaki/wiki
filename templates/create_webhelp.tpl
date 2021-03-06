<a href="{$smarty.const.WIKI_PKG_URL}create_webhelp.php" class="pagetitle">{tr}Create WebHelp{/tr}</a>

Here you can generate static HTML files from Wiki Book.

<br /><br />

{if  $generated eq 'y'}
<a href="{$smarty.const.BITHELP_PKG_URL}{$dir}/index.html">{tr}You can browse the generated WebHelp here{/tr}</a><br /><br />
{/if}
<form method="post" action="{$smarty.const.WIKI_PKG_URL}create_webhelp.php">
<table class="panel">
  <tr>
  	<td>{tr}Structure{/tr}</td>
  	<td>{$struct_info.title|escape}</td>
  </tr>
  <input type="hidden" name="name" value="{$struct_info.title|escape}" />
  <input type="hidden" name="struct" value="{$struct_info.structure_id}" />
  <tr>
  	<td>{tr}Directory{/tr}</td>
  	<td><input type="text" name="dir" value="{$struct_info.title|escape}" /></td>
  </tr>
  <tr>
  	<td>{tr}Top page{/tr}</td>
  	<td><input type="text" name="top" value="{$struct_info.title|escape}" /></td>
  </tr>
  <tr>
  	<td colspan="2"><input type="submit" name="create" value="{tr}Create{/tr}" /></td>
  </tr>
</table>
</form>
