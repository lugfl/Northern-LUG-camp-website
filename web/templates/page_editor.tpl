{if $ENABLE_EDITOR}
<script type="text/javascript" src="lib/javascript/editor.js"></script>
<script type="text/javascript">
	_editor_url  = "{$XINHA_DIR}";
	_editor_lang = "en";
	_editor_skin = "silva";
	_editor_icons = "classic";
	var css_path = "/templates/{$TEMPLATE_STYLE}/style.css";
</script>
<script type="text/javascript" src="{$XINHA_DIR}XinhaCore.js"></script>
<script type="text/javascript" src="{$XINHA_DIR}xinha_config.js"></script>
<div id="content_editor" style="position:absolute; z-index: 99; left: -10000px; top: -10000px;">
	<form id="editor_form" method="POST">
		<input type="hidden" name="editor" value="1">
		<textarea id="codeeditor" name="codeeditor" rows="30" cols="50" style="width: 900px">{$CONTENT}</textarea>
	</form>
	<span class="editor_buttons"><button onclick="editor_save();">save</button><button onclick="editor_discard();">discard</button></span>
</div>
<div id="content_addform" style="position:absolute; z-index: 99; left: -10000px; top: -10000px; background: white; border: 1px solid #685e9c; padding: 10px;">
	<form id="pageadd_form" method="POST">
		<label for="page_title">Titel der Seite:</label><input type="text" id="page_title" name="page_title" value="neue Seite" /><br />
		<fieldset>
			<legend>Position der Seite:</legend>
			<label for="before">Vor</label><input id="before" name="page_relation" type="radio" value="above"><br />
			<label for="after">Nach</label><input id="after" name="page_relation" type="radio" value="below" selected="selected"><br />
			<label for="in">Unterseite von</label><input id="in" name="page_relation" type="radio" value="in"><br />
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
		<label for="page_role">Berechtigung:</label><select id="page_role" name="page_role">
				<option value="-">jeder</option>
				<option value="user">eingeloggte Benutzer</option>
				<option value="admin">nur Admins</option>
			</select><br />
		<span class="editor_buttons"><button onclick="pageadd_save();">save</button><button onclick="pageadd_discard();">discard</button></span>
	</form>
</div>
<div id="news_addform" style="position:absolute; z-index: 99; left: -10000px; top: -10000px; background: white; border: 1px solid #685e9c; padding: 10px;">
	<form id="newsadd_form" method="POST">
		<input type="hidden" name="editor" value="1" />
		<label for="news_title">Title:</label><input type="text" id="news_title" name="news_title" value="News" /><br />
		<label for="news_short">Kurztext:</label><input type="text" id="news_short" name="news_short" value="Kurztext" /><br />
		<label for="newseditor">Text:</label><textarea id="newseditor" name="news_txt" rows="10" cols="50" style="width: 900px"></textarea><br />
		<span class="editor_buttons"><button name="news_submit" value="submitted" onclick="news_create_save();">save</button><button onclick="editor_hide();">discard</button></span>
	</form>
</div>
{/if}
