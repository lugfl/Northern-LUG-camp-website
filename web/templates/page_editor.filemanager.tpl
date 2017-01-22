{if $ENABLE_EDITOR}
{foreach item=file from=$filelist }
	<div class='filemanager_file' data-src='{$file_path}/{$file}'><img src="{$file_path}/{$file}" style="height:128px;"><br />{$file}</div>
{/foreach}

<form method="POST" enctype="multipart/form-data">
	<input type="file" name="upload">
	<input type="submit">
</form>


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
