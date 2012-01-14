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
function submit_form(form_id)
{
	var sform = parent.document.getElementById(form_id);
	sform.action = document.location.href;
	sform.submit();
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
	editor_hide();
	submit_form('editor_form');
}
function pageadd_save()
{
	editor_hide();
	submit_form('pageadd_form');
}
function editor_discard()
{
	show_object(parent.document.getElementById("content_editor"), false);
}
function page_create_show()
{
	show_object(parent.document.getElementById("content_addform"), true);
}
function news_create_show()
{
	show_object(parent.document.getElementById("news_addform"), true);
}
function news_create_save()
{
	parent.document.getElementById("news_add_form").submit();
}
