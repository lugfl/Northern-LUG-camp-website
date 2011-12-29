/* This compressed file is part of Xinha. For uncompressed sources, forum, and bug reports, go to xinha.org */
var SpellChecker=window.opener.SpellChecker;var Xinha=window.opener.Xinha;var _editor_url=window.opener._editor_url;var is_ie=Xinha.is_ie;var editor=SpellChecker.editor;var frame=null;var currentElement=null;var wrongWords=null;var modified=false;var allWords={};var fixedWords=[];var suggested_words={};var to_p_dict=[];var to_r_list=[];function _lc(a){return Xinha._lc(a,"SpellChecker")}function makeCleanDoc(d){var c=wrongWords.concat(fixedWords);for(var a=c.length;--a>=0;){var b=c[a];if(!(d&&/HA-spellcheck-fixed/.test(b.className))){if(b.firstChild){b.parentNode.insertBefore(b.firstChild,b)}b.parentNode.removeChild(b)}else{b.className="HA-spellcheck-fixed"}}return Xinha.getHTML(frame.contentWindow.document.body,true,editor)}function recheckClicked(){document.getElementById("status").innerHTML=_lc("Please wait: changing dictionary to")+': "'+document.getElementById("f_dictionary").value+'".';var a=document.getElementById("f_content");a.value=makeCleanDoc(true);a.form.submit()}function saveClicked(){if(modified){editor.setHTML(makeCleanDoc(false))}if(to_p_dict.length||to_r_list.length&&editor.config.SpellChecker.backend=="php"){var b={};for(var a=0;a<to_p_dict.length;a++){b["to_p_dict["+a+"]"]=to_p_dict[a]}for(var a=0;a<to_r_list.length;a++){b["to_r_list["+a+"][0]"]=to_r_list[a][0];b["to_r_list["+a+"][1]"]=to_r_list[a][1]}window.opener.Xinha._postback(Xinha.getPluginDir("SpellChecker")+"/spell-check-savedicts.php",b);window.close()}else{window.close()}return false}function cancelClicked(){var a=true;if(modified){a=confirm(_lc("This will drop changes and quit spell checker.  Please confirm."))}if(a){window.close()}return false}function replaceWord(b){var a=document.getElementById("v_replacement").value;var c=(b.innerHTML!=a);if(c){modified=true}if(b){b.className=b.className.replace(/\s*HA-spellcheck-(hover|fixed)\s*/g," ")}b.className+=" HA-spellcheck-fixed";b.__msh_fixed=true;if(!c){return false}to_r_list.push([b.innerHTML,a]);b.innerHTML=a}function replaceClicked(){replaceWord(currentElement);var b=currentElement.__msh_id;var a=b;do{++a;if(a==wrongWords.length){a=0}}while((a!=b)&&wrongWords[a].__msh_fixed);if(a==b){a=0;alert(_lc("Finished list of mispelled words"))}wrongWords[a].__msh_wordClicked(true);return false}function revertClicked(){document.getElementById("v_replacement").value=currentElement.__msh_origWord;replaceWord(currentElement);currentElement.className="HA-spellcheck-error HA-spellcheck-current";return false}function replaceAllClicked(){var d=document.getElementById("v_replacement").value;var c=true;var b=allWords[currentElement.__msh_origWord];if(b.length==0){alert("An impossible condition just happened.  Call FBI.  ;-)")}else{if(b.length==1){replaceClicked();return false}}if(c){for(var a=0;a<b.length;++a){if(b[a]!=currentElement){replaceWord(b[a])}}replaceClicked()}return false}function ignoreClicked(){document.getElementById("v_replacement").value=currentElement.__msh_origWord;replaceClicked();return false}function ignoreAllClicked(){document.getElementById("v_replacement").value=currentElement.__msh_origWord;replaceAllClicked();return false}function learnClicked(){to_p_dict.push(currentElement.__msh_origWord);return ignoreAllClicked()}function internationalizeWindow(){var f=["div","span","button"];for(var e=0;e<f.length;++e){var b=f[e];var d=document.getElementsByTagName(b);for(var c=d.length;--c>=0;){var g=d[c];if(g.childNodes.length==1&&/\S/.test(g.innerHTML)){var a=g.innerHTML;g.innerHTML=_lc(a)}}}}function initDocument(){internationalizeWindow();modified=false;frame=document.getElementById("i_framecontent");var b=document.getElementById("f_content");b.value=Xinha.getHTML(editor._doc.body,false,editor);var c=document.getElementById("f_dictionary");if(typeof editor.config.SpellChecker.defaultDictionary!="undefined"&&editor.config.SpellChecker.defaultDictionary!=""){c.value=editor.config.SpellChecker.defaultDictionary}else{c.value="en_GB"}if(editor.config.SpellChecker.backend=="php"){b.form.action=Xinha.getPluginDir("SpellChecker")+"/spell-check-logic.php"}if(editor.config.SpellChecker.utf8_to_entities){document.getElementById("utf8_to_entities").value=1}else{document.getElementById("utf8_to_entities").value=0}b.form.submit();document.getElementById("f_init").value="0";var a=document.getElementById("v_suggestions");a.onchange=function(){document.getElementById("v_replacement").value=this.value};if(is_ie){a.attachEvent("ondblclick",replaceClicked)}else{a.addEventListener("dblclick",replaceClicked,true)}document.getElementById("b_replace").onclick=replaceClicked;if(editor.config.SpellChecker.backend=="php"){document.getElementById("b_learn").onclick=learnClicked}else{document.getElementById("b_learn").parentNode.removeChild(document.getElementById("b_learn"))}document.getElementById("b_replall").onclick=replaceAllClicked;document.getElementById("b_ignore").onclick=ignoreClicked;document.getElementById("b_ignall").onclick=ignoreAllClicked;document.getElementById("b_recheck").onclick=recheckClicked;document.getElementById("b_revert").onclick=revertClicked;document.getElementById("b_info").onclick=displayInfo;document.getElementById("b_ok").onclick=saveClicked;document.getElementById("b_cancel").onclick=cancelClicked;a=document.getElementById("v_dictionaries");a.onchange=function(){document.getElementById("f_dictionary").value=this.value}}function getAbsolutePos(b){var c={x:b.offsetLeft,y:b.offsetTop};if(b.offsetParent){var a=getAbsolutePos(b.offsetParent);c.x+=a.x;c.y+=a.y}return c}function wordClicked(k){var h=this;if(k){(function(){var l=getAbsolutePos(h);var a={x:frame.offsetWidth-4,y:frame.offsetHeight-4};var i={x:frame.contentWindow.document.body.scrollLeft,y:frame.contentWindow.document.body.scrollTop};l.x-=Math.round(a.x/2);if(l.x<0){l.x=0}l.y-=Math.round(a.y/2);if(l.y<0){l.y=0}frame.contentWindow.scrollTo(l.x,l.y)})()}if(currentElement){var g=allWords[currentElement.__msh_origWord];currentElement.className=currentElement.className.replace(/\s*HA-spellcheck-current\s*/g," ");for(var f=0;f<g.length;++f){var b=g[f];if(b!=currentElement){b.className=b.className.replace(/\s*HA-spellcheck-same\s*/g," ")}}}currentElement=this;this.className+=" HA-spellcheck-current";var g=allWords[currentElement.__msh_origWord];for(var f=0;f<g.length;++f){var b=g[f];if(b!=currentElement){b.className+=" HA-spellcheck-same"}}var e;if(g.length==1){e="one occurrence"}else{if(g.length==2){e="two occurrences"}else{e=g.length+" occurrences"}}var c=suggested_words[this.__msh_origWord];if(c){c=c.split(/,/)}else{c=[]}var j=document.getElementById("v_suggestions");document.getElementById("statusbar").innerHTML="Found "+e+' for word "<b>'+currentElement.__msh_origWord+'</b>"';for(var f=j.length;--f>=0;){j.remove(f)}for(var f=0;f<c.length;++f){var e=c[f];var d=document.createElement("option");d.value=e;d.appendChild(document.createTextNode(e));j.appendChild(d)}document.getElementById("v_currentWord").innerHTML=this.__msh_origWord;if(c.length>0){j.selectedIndex=0;j.onchange()}else{document.getElementById("v_replacement").value=this.innerHTML}j.style.display="none";j.style.display="block";return false}function wordMouseOver(){this.className+=" HA-spellcheck-hover"}function wordMouseOut(){this.className=this.className.replace(/\s*HA-spellcheck-hover\s*/g," ")}function displayInfo(){var c=frame.contentWindow.spellcheck_info;if(!c){alert("No information available")}else{var a="** Document information **";for(var b in c){a+="\n"+b+" : "+c[b]}alert(a)}return false}function finishedSpellChecking(){currentElement=null;wrongWords=null;allWords={};fixedWords=[];suggested_words=frame.contentWindow.suggested_words;document.getElementById("status").innerHTML='Xinha Spell Checker (<a href="readme-tech.html" onclick="window.open(this.href,\'_blank\');return false;" title="Technical information">info</a>)';var n=frame.contentWindow.document;var k=n.getElementsByTagName("span");var b=[];var d=0;for(var j=0;j<k.length;++j){var e=k[j];if(/HA-spellcheck-error/.test(e.className)){b.push(e);e.__msh_wordClicked=wordClicked;e.onclick=function(a){a||(a=window.event);a&&Xinha._stopEvent(a);return this.__msh_wordClicked(false)};e.onmouseover=wordMouseOver;e.onmouseout=wordMouseOut;e.__msh_id=d++;var h=(e.__msh_origWord=e.firstChild.data);e.__msh_fixed=false;if(typeof allWords[h]=="undefined"){allWords[h]=[e]}else{allWords[h].push(e)}}else{if(/HA-spellcheck-fixed/.test(e.className)){fixedWords.push(e)}}}var c=n.getElementById("HA-spellcheck-dictionaries");if(c){c.parentNode.removeChild(c);c=c.innerHTML.split(/,/);var o=document.getElementById("v_dictionaries");for(var j=o.length;--j>=0;){o.remove(j)}var l=document.getElementById("f_dictionary").value;for(var j=0;j<c.length;++j){var h=c[j];var g=document.createElement("option");if(h==l){g.selected=true}g.value=h;g.appendChild(document.createTextNode(h));o.appendChild(g)}}wrongWords=b;if(b.length==0){if(!modified){alert(_lc("No mispelled words found with the selected dictionary."))}else{alert(_lc("No mispelled words found with the selected dictionary."))}return false}(currentElement=b[0]).__msh_wordClicked(true);var f=n.getElementsByTagName("a");for(var j=f.length;--j>=0;){var m=f[j];m.onclick=function(){if(confirm(_lc("Please confirm that you want to open this link")+":\n"+this.href+"\n"+_lc("I will open it in a new page."))){window.open(this.href)}return false}}};