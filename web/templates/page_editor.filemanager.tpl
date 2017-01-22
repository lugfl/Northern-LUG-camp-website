{if $ENABLE_EDITOR}
<style>
{literal}
formfield{border: 1px solid #999;margin:5px;padding:4px;width:100%;}
.filemanager_file{border: 1px solid #999;float:left;margin:5px;padding:4px;text-align:center;}
{/literal}
</style>

<form method="POST" enctype="multipart/form-data">
	<formfield>
		<input type="file" name="upload" style="width:87%;">
		<input type="submit" value="Upload">
	</formfield>
</form>

{foreach item=file from=$filelist }
	<div class='filemanager_file' data-src='{$file_path}/{$file}'><img src="{$file_path}/{$file}" style="height:128px;"><br />{$file}</div>
{/foreach}

<script src="/templates/{$TEMPLATE_STYLE}/js/jquery-3.1.1.min.js"></script>
<script type="text/javascript">
$(document).on("click","div.filemanager_file",function(){
	item_url = $(this).data("src");
	var args = top.tinymce.activeEditor.windowManager.getParams();
	win = (args.window);
	input = (args.input);
	win.document.getElementById(input).value = item_url;
	top.tinymce.activeEditor.windowManager.close();
});
</script>
{/if}
