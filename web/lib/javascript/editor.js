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
