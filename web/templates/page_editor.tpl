{if $ENABLE_EDITOR}
<script type="text/javascript" src="lib/javascript/editor.js"></script>
<script type="text/javascript">
	_editor_url  = "{$XINHA_DIR}";
	_editor_lang = "en";
	_editor_skin = "silva";
	_editor_icons = "classic";
	var css_path = "/templates/{$TEMPLATE_STYLE}/style.css";
</script>
<div id="content_editor" style="position:absolute; z-index: 99; left: -10000px; top: -10000px;">
	<script type="text/javascript" src="{$XINHA_DIR}XinhaCore.js"></script>
	<script type="text/javascript" src="{$XINHA_DIR}xinha_config.js"></script>
	<form id="editor_form" method="POST">
		<input type="hidden" name="editor" value="1">
		<textarea id="codeeditor" name="codeeditor" rows="30" cols="50" style="width: 900px">{$CONTENT}</textarea>
	</form>
	<span class="editor_buttons"><button onclick="editor_save();">save</button><button onclick="editor_discard();">discard</button></span>
</div>
<div id="content_addform" style="position:absolute; z-index: 99; left: -10000px; top: -10000px; background: white; border: 1px solid #685e9c; padding: 10px;">
	<form id="pageadd_form" method="POST">
		<label for="page_title">Titel der Seite:</label><input type="text" name="page_title" value="neue Seite" /><br />
		<label for="page_position">Position der Seite:</label>
		<fieldset name="page_position">
			<label for="page_relation">Vor</label><input name="page_relation" type="radio" value="above"><br />
			<label for="page_relation">Nach</label><input name="page_relation" type="radio" value="below"><br />
			<select name="page_pos">
				{* generate list from navigation *}
				{foreach from=$NAVI item=lvl1}
					<option value="{$lvl1.pageid}">|-- {$lvl1.title}</option>
					{foreach from=$lvl1.subitems item=lvl2}
						<option value="{$lvl2.pageid}">&nbsp;&nbsp;|-- {$lvl2.title}</option>
					{/foreach}
				{/foreach}
			</select><br />
		</fieldset>
		<label for="page_role">Berechtigung:</label><select name="page_role">
				<option value="-">jeder</option>
				<option value="user">eingeloggte Benutzer</option>
				<option value="admin">nur Admins</option>
			</select><br />
		<span class="editor_buttons"><button onclick="pageadd_save();">save</button><button onclick="pageadd_discard();">discard</button></span>
	</form>
</div>
<div id="news_addform" style="position:absolute; z-index: 99; left: -10000px; top: -10000px; background: white; border: 1px solid #685e9c; padding: 10px;">
	<script type="text/javascript" src="{$XINHA_DIR}XinhaCore.js"></script>
	<script type="text/javascript" src="{$XINHA_DIR}xinha_config.js"></script>
	<form id="newsadd_form" method="POST">
		<input type="hidden" name="editor" value="1" />
		<label for="news_title">Title:</label><input type="text" name="news_title" value="News" /><br />
		<label for="news_short">Kurztext:</label><input type="text" name="news_short" value="Kurztext" /><br />
		<label for="news_txt">Text:</label><textarea id="codeeditor" name="news_text" rows="10" cols="50" style="width: 900px"></textarea><br />
		<span class="editor_buttons"><button onclick="newsadd_save();">save</button><button onclick="newsadd_discard();">discard</button></span>
	</form>
</div>
{/if}
