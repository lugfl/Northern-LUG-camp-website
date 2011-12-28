{if $ENABLE_EDITOR}
<script type="text/javascript">
	_editor_url  = "{$XINHA_DIR}";
	_editor_lang = "en";
	_editor_skin = "silva";
	_editor_icons = "classic";
{literal}
	function editor_show()
	{
		var edit_win = parent.document.getElementById("content_editor");
		edit_win.style.left = "100px";
		edit_win.style.top = "150px";		
	}
	function editor_hide()
	{
		var edit_win = parent.document.getElementById("content_editor");
		edit_win.style.left = "-1000px";
		edit_win.style.top = "-1000px";
	}
	function editor_save()
	{
		editor_hide();
		var eform = parent.document.getElementById("editor_form");
		eform.action = document.location.href;
		eform.submit();
	}
	function editor_discard()
	{
		editor_hide();
	}
{/literal}
</script>
<a href="javascript:editor_show();">[Seite bearbeiten]</a>
<div id="content_editor" style="position:absolute; z-index: 99; left: -10000px; top: -10000px;">
	<script type="text/javascript" src="{$XINHA_DIR}XinhaCore.js"></script>
	<script type="text/javascript" src="{$XINHA_DIR}xinha_config.js"></script>
	<form id="editor_form" method="POST">
	<textarea id="codeeditor" name="codeeditor" rows="30" cols="50" style="width: 900px">{$CONTENT}</textarea>
	</form>
	<span class="editor_buttons"><button onclick="editor_save();">save</button><button onclick="editor_discard();">discard</button></span>
</div>
{/if}
