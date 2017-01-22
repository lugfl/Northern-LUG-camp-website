function show_window(name, state)
{
	if(arguments.length == 1){ state=true; }
	var obj = parent.document.getElementById(name);
	if(state == true)
	{
		obj.style.left = "0px";
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
function editor_save()
{
	submit_form('editor_form');
}
function pageadd_save()
{
	submit_form('pageadd_form');
}
function news_create_save()
{
	submit_form('news_add_form');
}
