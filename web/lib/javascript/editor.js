function show_object(obj, state)
{
	if(state == true)
	{
		obj.style.left = "100px";
		obj.style.top = "150px";
	}
	else
	{
		obj.style.left = "-1000px";
		obj.style.top = "-1000px";
	}
}
function editor_show()
{
	show_object(parent.document.getElementById("content_editor"), true);
}
function editor_hide()
{
	show_object(parent.document.getElementById("content_editor"), false);
}
function editor_save()
{
	var eform = parent.document.getElementById("editor_form");
	show_object(eform, false);
	eform.action = document.location.href;
	eform.submit();
}
function editor_discard()
{
	show_object(parent.document.getElementById("content_editor"), false);
}
function page_create_show()
{
	show_object(parent.document.getElementById("content_addform"), true);
}
