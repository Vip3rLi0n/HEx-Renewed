(function($,window,document){'use strict';var _templates={html:'<!DOCTYPE html>'+'<html>'+'<head>'+'<style>.ie * {min-height: auto !important}</style>'+'<meta http-equiv="Content-Type" content="text/html;charset={charset}" />'+'<link rel="stylesheet" type="text/css" href="{style}" />'+'</head>'+'<body contenteditable="true" {spellcheck}></body>'+'</html>',toolbarButton:'<a class="sceditor-button sceditor-button-{name}" data-sceditor-command="{name}" unselectable="on"><div unselectable="on">{dispName}</div></a>',emoticon:'<img src="{url}" data-sceditor-emoticon="{key}" alt="{key}" title="{tooltip}" />',fontOpt:'<a class="sceditor-font-option" href="#" data-font="{font}"><font face="{font}">{font}</font></a>',sizeOpt:'<a class="sceditor-fontsize-option" data-size="{size}" href="#"><font size="{size}">{size}</font></a>',pastetext:'<div><label for="txt">{label}</label> '+'<textarea cols="20" rows="7" id="txt"></textarea></div>'+'<div><input type="button" class="button" value="{insert}" /></div>',table:'<div><label for="rows">{rows}</label><input type="text" id="rows" value="2" /></div>'+'<div><label for="cols">{cols}</label><input type="text" id="cols" value="2" /></div>'+'<div><input type="button" class="button" value="{insert}" /></div>',image:'<div><label for="link">{url}</label> <input type="text" id="image" value="http://" /></div>'+'<div><label for="width">{width}</label> <input type="text" id="width" size="2" /></div>'+'<div><label for="height">{height}</label> <input type="text" id="height" size="2" /></div>'+'<div><input type="button" class="button" value="{insert}" /></div>',email:'<div><label for="email">{label}</label> <input type="text" id="email" /></div>'+'<div><input type="button" class="button" value="{insert}" /></div>',link:'<div><label for="link">{url}</label> <input type="text" id="link" value="http://" /></div>'+'<div><label for="des">{desc}</label> <input type="text" id="des" /></div>'+'<div><input type="button" class="button" value="{ins}" /></div>',youtubeMenu:'<div><label for="link">{label}</label> <input type="text" id="link" value="http://" /></div><div><input type="button" class="button" value="{insert}" /></div>',youtube:'<iframe width="560" height="315" src="http://www.youtube.com/embed/{id}?wmode=opaque" data-youtube-id="{id}" frameborder="0" allowfullscreen></iframe>'};var _tmpl=function(name,params,createHTML){var template=_templates[name];$.each(params,function(name,val){template=template.replace(new RegExp('\\{'+ name+'\\}','g'),val);});if(createHTML)
template=$(template);return template;};$.sceditor=function(el,options){var base=this;var original=el.get?el.get(0):el;var $original=$(original);var $editorContainer;var $toolbar;var $wysiwygEditor;var wysiwygEditor;var $wysiwygBody;var $wysiwygDoc;var $sourceEditor;var sourceEditor;var $dropdown;var keyPressFuncs=[];var lastRange;var locale;var preLoadCache=[];var rangeHelper;var requireNewLineFix=[];var btnStateHandlers=[];var $blurElm;var pluginManager;var currentNode;var currentBlockNode;var currentSelection;var isSelectionCheckPending;var isRequired;var inlineCss;var shortcutHandlers={};var currentEmoticons=[];var init,replaceEmoticons,handleCommand,saveRange,initEditor,initPlugins,initLocale,initToolBar,initOptions,initEvents,initCommands,initResize,initEmoticons,getWysiwygDoc,handlePasteEvt,handlePasteData,handleKeyDown,handleBackSpace,handleKeyPress,handleFormReset,handleMouseDown,handleEvent,handleDocumentClick,handleWindowResize,updateToolBar,updateActiveButtons,sourceEditorSelectedText,appendNewLine,checkSelectionChanged,checkNodeChanged,autofocus,emoticonsKeyPress,emoticonsCheckWhitespace,currentStyledBlockNode;base.commands=$.extend(true,{},(options.commands||$.sceditor.commands));base.opts=options=$.extend({},$.sceditor.defaultOptions,options);init=function(){$original.data("sceditor",base);$.each(options,function(key,val){if($.isPlainObject(val))
options[key]=$.extend(true,{},val);});if(options.locale&&options.locale!=='en')
initLocale();$editorContainer=$('<div class="sceditor-container" />').insertAfter($original).css('z-index',options.zIndex);if($.sceditor.ie)
$editorContainer.addClass('ie ie'+ $.sceditor.ie);isRequired=!!$original.attr('required');$original.removeAttr('required');initPlugins();initEmoticons();initToolBar();initEditor();initCommands();initOptions();initEvents();if(!$.sceditor.isWysiwygSupported)
base.toggleSourceMode();var loaded=function(){$(window).unbind('load',loaded);if(options.autofocus)
autofocus();if(options.autoExpand)
base.expandToContent();handleWindowResize();};$(window).load(loaded);if(document.readyState&&document.readyState==='complete')
loaded();updateActiveButtons();pluginManager.call('ready');};initPlugins=function(){var plugins=options.plugins;plugins=plugins?plugins.toString().split(','):[];pluginManager=new $.sceditor.PluginManager(base);$.each(plugins,function(idx,plugin){pluginManager.register($.trim(plugin));});};initLocale=function(){var lang;if($.sceditor.locale[options.locale])
locale=$.sceditor.locale[options.locale];else
{lang=options.locale.split('-');if($.sceditor.locale[lang[0]])
locale=$.sceditor.locale[lang[0]];}
if(locale&&locale.dateFormat)
options.dateFormat=locale.dateFormat;};initEditor=function(){var doc,tabIndex;$sourceEditor=$('<textarea></textarea>').hide();$wysiwygEditor=$('<iframe frameborder="0"></iframe>');if(!options.spellcheck)
$sourceEditor.attr('spellcheck','false');if(window.location.protocol==='https:')
$wysiwygEditor.attr('src','javascript:false');$editorContainer.append($wysiwygEditor).append($sourceEditor);wysiwygEditor=$wysiwygEditor[0];sourceEditor=$sourceEditor[0];base.width(options.width||$original.width());base.height(options.height||$original.height());doc=getWysiwygDoc();doc.open();doc.write(_tmpl('html',{spellcheck:options.spellcheck?'':'spellcheck="false"',charset:options.charset,style:options.style}));doc.close();$wysiwygDoc=$(doc);$wysiwygBody=$(doc.body);base.readOnly(!!options.readOnly);if($.sceditor.ie)
$wysiwygDoc.find('html').addClass('ie ie'+ $.sceditor.ie);if($.sceditor.ios||$.sceditor.ie)
{$wysiwygBody.height('100%');if(!$.sceditor.ie)
$wysiwygBody.bind('touchend',base.focus);}
rangeHelper=new $.sceditor.rangeHelper(wysiwygEditor.contentWindow);base.val($original.hide().val());tabIndex=$original.attr('tabindex');$sourceEditor.attr('tabindex',tabIndex);$wysiwygEditor.attr('tabindex',tabIndex);};initOptions=function(){if(options.autoUpdate)
{$wysiwygBody.bind('blur',base.updateOriginal);$sourceEditor.bind('blur',base.updateOriginal);}
if(options.rtl===null)
options.rtl=$sourceEditor.css('direction')==='rtl';base.rtl(!!options.rtl);if(options.autoExpand)
$wysiwygDoc.bind('keyup',base.expandToContent);if(options.resizeEnabled)
initResize();$editorContainer.attr('id',options.id);base.emoticons(options.emoticonsEnabled);};initEvents=function(){$(document).click(handleDocumentClick);$(original.form).bind('reset',handleFormReset).submit(base.updateOriginal);$(window).bind('resize orientationChanged',handleWindowResize);$wysiwygBody.keypress(handleKeyPress).keydown(handleKeyDown).keydown(handleBackSpace).keyup(appendNewLine).bind('paste',handlePasteEvt).bind($.sceditor.ie?'selectionchange':'keyup focus blur contextmenu mouseup touchend click',checkSelectionChanged).bind('keydown keyup keypress focus blur contextmenu',handleEvent);if(options.emoticonsCompat&&window.getSelection)
$wysiwygBody.keyup(emoticonsCheckWhitespace);$sourceEditor.bind('keydown keyup keypress focus blur contextmenu',handleEvent).keydown(handleKeyDown);$wysiwygDoc.keypress(handleKeyPress).mousedown(handleMouseDown).bind($.sceditor.ie?'selectionchange':'focus blur contextmenu mouseup click',checkSelectionChanged).bind('beforedeactivate keyup',saveRange).keyup(appendNewLine).focus(function(){lastRange=null;});$editorContainer.bind('selectionchanged',checkNodeChanged).bind('selectionchanged',updateActiveButtons).bind('selectionchanged',handleEvent).bind('nodechanged',handleEvent);};initToolBar=function(){var $group,$button,exclude=(options.toolbarExclude||'').split(','),groups=options.toolbar.split('|');$toolbar=$('<div class="sceditor-toolbar" unselectable="on" />');$.each(groups,function(idx,group){$group=$('<div class="sceditor-group" />');$.each(group.split(','),function(idx,button){if(!base.commands[button]||$.inArray(button,exclude)>-1)
return;$button=_tmpl('toolbarButton',{name:button,dispName:base._(base.commands[button].tooltip||button)},true);$button.data('sceditor-txtmode',!!base.commands[button].txtExec);$button.data('sceditor-wysiwygmode',!!base.commands[button].exec);$button.click(function(){var $this=$(this);if(!$this.hasClass('disabled'))
handleCommand($this,base.commands[button]);updateActiveButtons();return false;});if(base.commands[button].tooltip)
$button.attr('title',base._(base.commands[button].tooltip));if(!base.commands[button].exec)
$button.addClass('disabled');if(base.commands[button].shortcut)
base.addShortcut(base.commands[button].shortcut,button);$group.append($button);});if($group[0].firstChild)
$toolbar.append($group);});$(options.toolbarContainer||$editorContainer).append($toolbar);};initCommands=function(){$.each(base.commands,function(name,cmd){if(cmd.keyPress)
keyPressFuncs.push(cmd.keyPress);if(cmd.forceNewLineAfter&&$.isArray(cmd.forceNewLineAfter))
requireNewLineFix=$.merge(requireNewLineFix,cmd.forceNewLineAfter);if(cmd.state)
btnStateHandlers.push({name:name,state:cmd.state});else if(typeof cmd.exec==='string')
btnStateHandlers.push({name:name,state:cmd.exec});});appendNewLine();};initResize=function(){var minHeight,maxHeight,minWidth,maxWidth,mouseMoveFunc,mouseUpFunc,$grip=$('<div class="sceditor-grip" />'),$cover=$('<div class="sceditor-resize-cover" />'),startX=0,startY=0,startWidth=0,startHeight=0,origWidth=$editorContainer.width(),origHeight=$editorContainer.height(),dragging=false,rtl=base.rtl();minHeight=options.resizeMinHeight||origHeight/1.5;maxHeight=options.resizeMaxHeight||origHeight*2.5;minWidth=options.resizeMinWidth||origWidth/1.25;maxWidth=options.resizeMaxWidth||origWidth*1.25;mouseMoveFunc=function(e){if(e.type==='touchmove')
e=window.event;var newHeight=startHeight+(e.pageY- startY),newWidth=rtl?startWidth-(e.pageX- startX):startWidth+(e.pageX- startX);if(maxWidth>0&&newWidth>maxWidth)
newWidth=maxWidth;if(maxHeight>0&&newHeight>maxHeight)
newHeight=maxHeight;if(!options.resizeWidth||newWidth<minWidth||(maxWidth>0&&newWidth>maxWidth))
newWidth=false;if(!options.resizeHeight||newHeight<minHeight||(maxHeight>0&&newHeight>maxHeight))
newHeight=false;if(newWidth||newHeight)
{base.dimensions(newWidth,newHeight);if($.sceditor.ie<7)
$editorContainer.height(newHeight);}
e.preventDefault();};mouseUpFunc=function(e){if(!dragging)
return;dragging=false;$cover.hide();$editorContainer.removeClass('resizing').height('auto');$(document).unbind('touchmove mousemove',mouseMoveFunc);$(document).unbind('touchend mouseup',mouseUpFunc);e.preventDefault();};$editorContainer.append($grip);$editorContainer.append($cover.hide());$grip.bind('touchstart mousedown',function(e){if(e.type==='touchstart')
e=window.event;startX=e.pageX;startY=e.pageY;startWidth=$editorContainer.width();startHeight=$editorContainer.height();dragging=true;$editorContainer.addClass('resizing');$cover.show();$(document).bind('touchmove mousemove',mouseMoveFunc);$(document).bind('touchend mouseup',mouseUpFunc);if($.sceditor.ie<7)
$editorContainer.height(startHeight);e.preventDefault();});};initEmoticons=function(){var emoticon,emoticons=options.emoticons,root=options.emoticonsRoot;if(!$.isPlainObject(emoticons)||!options.emoticonsEnabled)
return;$.each(emoticons,function(idx,val){$.each(val,function(key,url){if(root)
{url={url:root+(url.url||url),tooltip:url.tooltip||key};emoticons[idx][key]=url;}
emoticon=document.createElement('img');emoticon.src=url.url||url;preLoadCache.push(emoticon);});});};autofocus=function(){var rng,elm,txtPos,doc=$wysiwygDoc[0],body=$wysiwygBody[0],focusEnd=!!options.autofocusEnd;if(!$editorContainer.is(':visible'))
return;if(base.sourceMode())
{txtPos=sourceEditor.value.length;if(sourceEditor.setSelectionRange)
sourceEditor.setSelectionRange(txtPos,txtPos);else if(sourceEditor.createTextRange)
{rng=sourceEditor.createTextRange();rng.moveEnd('character',txtPos);rng.moveStart('character',txtPos);rangeHelper.selectRange(rng);}}
else
{$.sceditor.dom.removeWhiteSpace(body);if(focusEnd)
{if(!(elm=body.lastChild))
$wysiwygBody.append((elm=doc.createElement('div')));while(elm.lastChild)
{elm=elm.lastChild;if(/br/i.test(elm.nodeName)&&elm.previousSibling)
elm=elm.previousSibling;}}
else
elm=body.firstChild;if(doc.createRange)
{rng=doc.createRange();if(/br/i.test(elm.nodeName))
rng.setStartBefore(elm);else
rng.selectNodeContents(elm);rng.collapse(false);}
else
{rng=body.createTextRange();rng.moveToElementText(elm.nodeType!==3?elm:elm.parentNode);rng.collapse(false);}
rangeHelper.selectRange(rng);if(focusEnd)
{$wysiwygDoc.scrollTop(body.scrollHeight);$wysiwygBody.scrollTop(body.scrollHeight);}}
base.focus();};base.readOnly=function(readOnly){if(typeof readOnly!=='boolean')
return $sourceEditor.attr('readonly')==='readonly';$wysiwygBody[0].contentEditable=!readOnly;if(!readOnly)
$sourceEditor.removeAttr('readonly');else
$sourceEditor.attr('readonly','readonly');updateToolBar(readOnly);return this;};base.rtl=function(rtl){var dir=rtl?'rtl':'ltr';if(typeof rtl!=='boolean')
return $sourceEditor.attr('dir')==='rtl';$wysiwygBody.attr('dir',dir);$sourceEditor.attr('dir',dir);$editorContainer.removeClass('rtl').removeClass('ltr').addClass(dir);return this;};updateToolBar=function(disable){var inSourceMode=base.inSourceMode();$toolbar.find('.sceditor-button').removeClass('disabled').each(function(){var button=$(this);if(disable===true||(inSourceMode&&!button.data('sceditor-txtmode')))
button.addClass('disabled');else if(!inSourceMode&&!button.data('sceditor-wysiwygmode'))
button.addClass('disabled');});};base.width=function(width,saveWidth){if(!width&&width!==0)
return $editorContainer.width();base.dimensions(width,null,saveWidth);return this;};base.dimensions=function(width,height,save){var ieBorderBox=$.sceditor.ie<8||document.documentMode<8?2:0;width=(!width&&width!==0)?false:width;height=(!height&&height!==0)?false:height;if(width===false&&height===false)
return{width:base.width(),height:base.height()};if(typeof $wysiwygEditor.data('outerWidthOffset')==='undefined')
base.updateStyleCache();if(width!==false)
{if(save!==false)
options.width=width;if(height===false)
{height=$editorContainer.height();save=false;}
$editorContainer.width(width);if(width&&width.toString().indexOf('%')>-1)
width=$editorContainer.width();$wysiwygEditor.width(width- $wysiwygEditor.data('outerWidthOffset'));$sourceEditor.width(width- $sourceEditor.data('outerWidthOffset'));if($.sceditor.ios&&$wysiwygBody)
$wysiwygBody.width(width- $wysiwygEditor.data('outerWidthOffset')-($wysiwygBody.outerWidth(true)- $wysiwygBody.width()));}
if(height!==false)
{if(save!==false)
options.height=height;if(height&&height.toString().indexOf('%')>-1)
{height=$editorContainer.height(height).height();$editorContainer.height('auto');}
height-=!options.toolbarContainer?$toolbar.outerHeight(true):0;$wysiwygEditor.height(height- $wysiwygEditor.data('outerHeightOffset'));$sourceEditor.height(height- ieBorderBox- $sourceEditor.data('outerHeightOffset'));}
return this;};base.updateStyleCache=function(){$wysiwygEditor.data('outerWidthOffset',$wysiwygEditor.outerWidth(true)- $wysiwygEditor.width());$sourceEditor.data('outerWidthOffset',$sourceEditor.outerWidth(true)- $sourceEditor.width());$wysiwygEditor.data('outerHeightOffset',$wysiwygEditor.outerHeight(true)- $wysiwygEditor.height());$sourceEditor.data('outerHeightOffset',$sourceEditor.outerHeight(true)- $sourceEditor.height());};base.height=function(height,saveHeight){if(!height&&height!==0)
return $editorContainer.height();base.dimensions(null,height,saveHeight);return this;};base.maximize=function(maximize){if(typeof maximize==='undefined')
return $editorContainer.is('.sceditor-maximize');maximize=!!maximize;if($.sceditor.ie<7)
$('html, body').toggleClass('sceditor-maximize',maximize);$editorContainer.toggleClass('sceditor-maximize',maximize);base.width(maximize?'100%':options.width,false);base.height(maximize?'100%':options.height,false);return this;};base.expandToContent=function(ignoreMaxHeight){var currentHeight=$editorContainer.height(),height=$wysiwygBody[0].scrollHeight||$wysiwygDoc[0].documentElement.scrollHeight,padding=(currentHeight- $wysiwygEditor.height()),maxHeight=options.resizeMaxHeight||((options.height||$original.height())*2);height+=padding;if(ignoreMaxHeight!==true&&height>maxHeight)
height=maxHeight;if(height>currentHeight)
base.height(height);};base.destroy=function(){pluginManager.destroy();rangeHelper=null;lastRange=null;pluginManager=null;$(document).unbind('click',handleDocumentClick);$(window).unbind('resize orientationChanged',handleWindowResize);$(original.form).unbind('reset',handleFormReset).unbind('submit',base.updateOriginal);$wysiwygBody.unbind();$wysiwygDoc.unbind().find('*').remove();$sourceEditor.unbind().remove();$toolbar.remove();$editorContainer.unbind().find('*').unbind().remove();$editorContainer.remove();$original.removeData('sceditor').removeData('sceditorbbcode').show();if(isRequired)
$original.attr('required','required');};base.createDropDown=function(menuItem,dropDownName,content,ieUnselectable){var css,onlyclose=$dropdown&&$dropdown.is('.sceditor-'+ dropDownName);base.closeDropDown();if(onlyclose)return;if(ieUnselectable!==false)
{$(content).find(':not(input,textarea)').filter(function(){return this.nodeType===1;}).attr('unselectable','on');}
css={top:menuItem.offset().top,left:menuItem.offset().left,marginTop:menuItem.outerHeight()};$.extend(css,options.dropDownCss);$dropdown=$('<div class="sceditor-dropdown sceditor-'+ dropDownName+'" />').css(css).append(content).appendTo($('body')).click(function(e){e.stopPropagation();});};handleDocumentClick=function(e){if(e.which!==3)
base.closeDropDown();};handlePasteEvt=function(e){var html,handlePaste,elm=$wysiwygBody[0],doc=$wysiwygDoc[0],checkCount=0,pastearea=document.createElement('div'),prePasteContent=doc.createDocumentFragment();if(options.disablePasting)
return false;if(!options.enablePasteFiltering)
return;rangeHelper.saveRange();document.body.appendChild(pastearea);if(e&&e.clipboardData&&e.clipboardData.getData)
{if((html=e.clipboardData.getData('text/html'))||(html=e.clipboardData.getData('text/plain')))
{pastearea.innerHTML=html;handlePasteData(elm,pastearea);return false;}}
while(elm.firstChild)
prePasteContent.appendChild(elm.firstChild);handlePaste=function(elm,pastearea){if(elm.childNodes.length>0)
{while(elm.firstChild)
pastearea.appendChild(elm.firstChild);while(prePasteContent.firstChild)
elm.appendChild(prePasteContent.firstChild);handlePasteData(elm,pastearea);}
else
{if(checkCount>25)
{while(prePasteContent.firstChild)
elm.appendChild(prePasteContent.firstChild);rangeHelper.restoreRange();return;}
++checkCount;setTimeout(function(){handlePaste(elm,pastearea);},20);}};handlePaste(elm,pastearea);base.focus();return true;};handlePasteData=function(elm,pastearea){$.sceditor.dom.fixNesting(pastearea);var pasteddata=pastearea.innerHTML;if(pluginManager.hasHandler('toSource'))
pasteddata=pluginManager.callOnlyFirst('toSource',pasteddata,$(pastearea));pastearea.parentNode.removeChild(pastearea);if(pluginManager.hasHandler('toWysiwyg'))
pasteddata=pluginManager.callOnlyFirst('toWysiwyg',pasteddata,true);rangeHelper.restoreRange();base.wysiwygEditorInsertHtml(pasteddata,null,true);};base.closeDropDown=function(focus){if($dropdown){$dropdown.unbind().remove();$dropdown=null;}
if(focus===true)
base.focus();};getWysiwygDoc=function(){if(wysiwygEditor.contentDocument)
return wysiwygEditor.contentDocument;if(wysiwygEditor.contentWindow&&wysiwygEditor.contentWindow.document)
return wysiwygEditor.contentWindow.document;if(wysiwygEditor.document)
return wysiwygEditor.document;return null;};base.wysiwygEditorInsertHtml=function(html,endHtml,overrideCodeBlocking){var scrollTo,$marker,marker='<span id="sceditor-cursor">&nbsp;</span>';base.focus();if(!overrideCodeBlocking&&($(currentBlockNode).is('code')||$(currentBlockNode).parents('code').length!==0))
return;if(endHtml)
endHtml+=marker;else
html+=marker;rangeHelper.insertHTML(html,endHtml);$marker=$wysiwygBody.find('#sceditor-cursor');scrollTo=($marker.offset().top+($marker.outerHeight(true)*2))- $wysiwygEditor.height();$marker.remove();$wysiwygDoc.scrollTop(scrollTo);$wysiwygBody.scrollTop(scrollTo);rangeHelper.saveRange();replaceEmoticons($wysiwygBody[0]);rangeHelper.restoreRange();appendNewLine();};base.wysiwygEditorInsertText=function(text,endText){base.wysiwygEditorInsertHtml($.sceditor.escapeEntities(text),$.sceditor.escapeEntities(endText));};base.insertText=function(text,endText){if(base.inSourceMode())
base.sourceEditorInsertText(text,endText);else
base.wysiwygEditorInsertText(text,endText);return this;};base.sourceEditorInsertText=function(text,endText){var range,start,end,txtLen,scrollTop;scrollTop=sourceEditor.scrollTop;sourceEditor.focus();if(typeof sourceEditor.selectionStart!=='undefined')
{start=sourceEditor.selectionStart;end=sourceEditor.selectionEnd;txtLen=text.length;if(endText)
text+=sourceEditor.value.substring(start,end)+ endText;sourceEditor.value=sourceEditor.value.substring(0,start)+ text+ sourceEditor.value.substring(end,sourceEditor.value.length);sourceEditor.selectionStart=(start+ text.length)-(endText?endText.length:0);sourceEditor.selectionEnd=sourceEditor.selectionStart;}
else if(typeof document.selection.createRange!=='undefined')
{range=document.selection.createRange();if(endText)
text+=range.text+ endText;range.text=text;if(endText)
range.moveEnd('character',0-endText.length);range.moveStart('character',range.End- range.Start);range.select();}
else
sourceEditor.value+=text+ endText;sourceEditor.scrollTop=scrollTop;sourceEditor.focus();};base.getRangeHelper=function(){return rangeHelper;};base.val=function(val,filter){if(typeof val==="string")
{if(base.inSourceMode())
base.setSourceEditorValue(val);else
{if(filter!==false&&pluginManager.hasHandler('toWysiwyg'))
val=pluginManager.callOnlyFirst('toWysiwyg',val);base.setWysiwygEditorValue(val);}
return this;}
return base.inSourceMode()?base.getSourceEditorValue(false):base.getWysiwygEditorValue();};base.insert=function(start,end,filter,convertEmoticons,allowMixed){if(base.inSourceMode())
base.sourceEditorInsertText(start,end);else
{if(end)
{var html=base.getRangeHelper().selectedHtml(),frag=$('<div>').appendTo($('body')).hide().html(html);if(filter!==false&&pluginManager.hasHandler('toSource'))
html=pluginManager.callOnlyFirst('toSource',html,frag);frag.remove();start+=html+ end;}
if(filter!==false&&pluginManager.hasHandler('toWysiwyg'))
start=pluginManager.callOnlyFirst('toWysiwyg',start,true);if(filter!==false&&allowMixed===true)
{start=start.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');}
base.wysiwygEditorInsertHtml(start);}
return this;};base.getWysiwygEditorValue=function(filter){var html,ieBookmark,hasSelection=rangeHelper.hasSelection();if(hasSelection)
rangeHelper.saveRange();else if(lastRange&&lastRange.getBookmark)
ieBookmark=lastRange.getBookmark();$.sceditor.dom.fixNesting($wysiwygBody[0]);html=$wysiwygBody.html();if(filter!==false&&pluginManager.hasHandler('toSource'))
html=pluginManager.callOnlyFirst('toSource',html,$wysiwygBody);if(hasSelection)
{rangeHelper.restoreRange();lastRange=null;}
else if(ieBookmark)
{lastRange.moveToBookmark(ieBookmark);lastRange=null;}
return html;};base.getBody=function(){return $wysiwygBody;};base.getContentAreaContainer=function(){return $wysiwygEditor;};base.getSourceEditorValue=function(filter){var val=$sourceEditor.val();if(filter!==false&&pluginManager.hasHandler('toWysiwyg'))
val=pluginManager.callOnlyFirst('toWysiwyg',val);return val;};base.setWysiwygEditorValue=function(value){if(!value)
value='<p>'+($.sceditor.ie?'':'<br />')+'</p>';$wysiwygBody[0].innerHTML=value;replaceEmoticons($wysiwygBody[0]);appendNewLine();};base.setSourceEditorValue=function(value){$sourceEditor.val(value);};base.updateOriginal=function(){$original.val(base.val());};replaceEmoticons=function(node){if(!options.emoticonsEnabled||$(node).parents('code').length)
return;var doc=node.ownerDocument,emoticonCodes=[],emoticonRegex=[],emoticons=$.extend({},options.emoticons.more,options.emoticons.dropdown,options.emoticons.hidden);$.each(emoticons,function(key){if(options.emoticonsCompat)
emoticonRegex[key]=new RegExp('(>|^|\\s|\xA0|\u2002|\u2003|\u2009|&nbsp;)'+ $.sceditor.regexEscape(key)+'(\\s|$|<|\xA0|\u2002|\u2003|\u2009|&nbsp;)');emoticonCodes.push(key);});(function convertEmoticons(node){node=node.firstChild;while(node!=null)
{var parts,key,emoticon,parsedHtml,emoticonIdx,nextSibling,startIdx,nodeParent=node.parentNode,nodeValue=node.nodeValue;if(node.nodeType!==3)
{if(!$(node).is('code'))
convertEmoticons(node);}
else if(nodeValue)
{emoticonIdx=emoticonCodes.length;while(emoticonIdx--)
{key=emoticonCodes[emoticonIdx];startIdx=options.emoticonsCompat?nodeValue.search(emoticonRegex[key]):nodeValue.indexOf(key);if(startIdx>-1)
{nextSibling=node.nextSibling;emoticon=emoticons[key];parts=nodeValue.substr(startIdx).split(key);nodeValue=nodeValue.substr(0,startIdx)+ parts.shift();node.nodeValue=nodeValue;parsedHtml=$.sceditor.dom.parseHTML(_tmpl('emoticon',{key:key,url:emoticon.url||emoticon,tooltip:emoticon.tooltip||key}),doc);nodeParent.insertBefore(parsedHtml[0],nextSibling);nodeParent.insertBefore(doc.createTextNode(parts.join(key)),nextSibling);}}}
node=node.nextSibling;}}(node));if(options.emoticonsCompat)
currentEmoticons=$wysiwygBody.find('img[data-sceditor-emoticon]');};base.inSourceMode=function(){return $editorContainer.hasClass('sourceMode');};base.sourceMode=function(enable){if(typeof enable!=='boolean')
return base.inSourceMode();if((base.inSourceMode()&&!enable)||(!base.inSourceMode()&&enable))
base.toggleSourceMode();return this;};base.toggleSourceMode=function(){if(!$.sceditor.isWysiwygSupported&&base.inSourceMode())
return;base.blur();if(base.inSourceMode())
base.setWysiwygEditorValue(base.getSourceEditorValue());else
base.setSourceEditorValue(base.getWysiwygEditorValue());lastRange=null;$sourceEditor.toggle();$wysiwygEditor.toggle();if(!base.inSourceMode())
$editorContainer.removeClass('wysiwygMode').addClass('sourceMode');else
$editorContainer.removeClass('sourceMode').addClass('wysiwygMode');updateToolBar();updateActiveButtons();};sourceEditorSelectedText=function(){sourceEditor.focus();if(sourceEditor.selectionStart!=null)
return sourceEditor.value.substring(sourceEditor.selectionStart,sourceEditor.selectionEnd);else if(document.selection.createRange)
return document.selection.createRange().text;};handleCommand=function(caller,command){if(base.inSourceMode())
{if(command.txtExec)
{if($.isArray(command.txtExec))
base.sourceEditorInsertText.apply(base,command.txtExec);else
command.txtExec.call(base,caller,sourceEditorSelectedText());}
return;}
if(!command.exec)
return;if($.isFunction(command.exec))
command.exec.call(base,caller);else
base.execCommand(command.exec,command.hasOwnProperty('execParam')?command.execParam:null);};saveRange=function(){if($.sceditor.ie)
lastRange=rangeHelper.selectedRange();};base.execCommand=function(command,param){var executed=false,$parentNode=$(rangeHelper.parentNode());base.focus();if($parentNode.is('code')||$parentNode.parents('code').length!==0)
return;try
{executed=$wysiwygDoc[0].execCommand(command,false,param);}
catch(e){}
if(!executed&&base.commands[command]&&base.commands[command].errorMessage)
alert(base._(base.commands[command].errorMessage));};checkSelectionChanged=function(){var check=function(){if(rangeHelper&&!rangeHelper.compare(currentSelection))
{currentSelection=rangeHelper.cloneSelected();$editorContainer.trigger($.Event('selectionchanged'));}
isSelectionCheckPending=false;};if(isSelectionCheckPending)
return;isSelectionCheckPending=true;if($.sceditor.ie)
check();else
setTimeout(check,100);};checkNodeChanged=function(){var oldNode,node=rangeHelper.parentNode();if(currentNode!==node)
{oldNode=currentNode;currentNode=node;currentBlockNode=rangeHelper.getFirstBlockParent(node);$editorContainer.trigger($.Event('nodechanged',{oldNode:oldNode,newNode:currentNode}));}};base.currentNode=function(){return currentNode;};base.currentBlockNode=function(){return currentBlockNode;};updateActiveButtons=function(e){var state,stateHandler,firstBlock,$button,parent,doc=$wysiwygDoc[0],i=btnStateHandlers.length,inSourceMode=base.sourceMode();if(!base.sourceMode()&&!base.readOnly())
{parent=e?e.newNode:rangeHelper.parentNode();firstBlock=rangeHelper.getFirstBlockParent(parent);while(i--)
{state=0;stateHandler=btnStateHandlers[i];$button=$toolbar.find('.sceditor-button-'+ stateHandler.name);if(inSourceMode&&!$button.data('sceditor-txtmode'))
$button.addClass('disabled');else if(!inSourceMode&&!$button.data('sceditor-wysiwygmode'))
$button.addClass('disabled');else
{if(typeof stateHandler.state==='string')
{try
{state=doc.queryCommandEnabled(stateHandler.state)?0:-1;if(state>-1)
state=doc.queryCommandState(stateHandler.state)?1:0;}
catch(ex){}}
else
state=stateHandler.state.call(base,parent,firstBlock);if(state<0)
$button.addClass('disabled');else
$button.removeClass('disabled');if(state>0)
$button.addClass('active');else
$button.removeClass('active');}}}
else
$toolbar.find('.sceditor-button').removeClass('active');};handleKeyPress=function(e){var $parentNode,i=keyPressFuncs.length;base.closeDropDown();$parentNode=$(currentNode);if(e.which===13)
{if($parentNode.is('code,blockquote,pre')||$parentNode.parents('code,blockquote,pre').length!==0)
{lastRange=null;base.wysiwygEditorInsertHtml('<br />',null,true);return false;}}
if($parentNode.is('code')||$parentNode.parents('code').length!==0)
return;while(i--)
keyPressFuncs[i].call(base,e,wysiwygEditor,$sourceEditor);};appendNewLine=function(){var name,requiresNewLine,div;$.sceditor.dom.rTraverse($wysiwygBody[0],function(node){name=node.nodeName.toLowerCase();if($.inArray(name,requireNewLineFix)>-1)
requiresNewLine=true;if((node.nodeType===3&&!/^\s*$/.test(node.nodeValue))||name==='br'||($.sceditor.ie&&!node.firstChild&&!$.sceditor.dom.isInline(node,false)))
{if(requiresNewLine)
{div=$wysiwygBody[0].ownerDocument.createElement('div');div.className='sceditor-nlf';div.innerHTML=!$.sceditor.ie?'<br />':'';$wysiwygBody[0].appendChild(div);}
return false;}});};handleFormReset=function(){base.val($original.val());};handleMouseDown=function(){base.closeDropDown();lastRange=null;};handleWindowResize=function(){var height=options.height,width=options.width;if(!base.maximize())
{if(height&&height.toString().indexOf("%")>-1)
base.height(height);if(width&&width.toString().indexOf("%")>-1)
base.width(width);}
else
base.dimensions('100%','100%',false);};base._=function(){var args=arguments;if(locale&&locale[args[0]])
args[0]=locale[args[0]];return args[0].replace(/\{(\d+)\}/g,function(str,p1){return typeof args[p1-0+1]!=='undefined'?args[p1-0+1]:'{'+ p1+'}';});};handleEvent=function(e){var customEvent,clone=$.extend({},e);pluginManager.call(clone.type+'Event',e,base);delete clone.type;customEvent=$.Event((e.target===sourceEditor?'scesrc':'scewys')+ e.type,clone);$editorContainer.trigger.apply($editorContainer,[customEvent,base]);if(customEvent.isDefaultPrevented())
e.preventDefault();if(customEvent.isImmediatePropagationStopped())
customEvent.stopImmediatePropagation();if(customEvent.isPropagationStopped())
customEvent.stopPropagation();};base.bind=function(events,handler,excludeWysiwyg,excludeSource){var i=events.length;events=events.split(" ");while(i--)
{if($.isFunction(handler))
{if(!excludeWysiwyg)
$editorContainer.bind('scewys'+ events[i],handler);if(!excludeSource)
$editorContainer.bind('scesrc'+ events[i],handler);}}
return this;};base.unbind=function(events,handler,excludeWysiwyg,excludeSource){var i=events.length;events=events.split(" ");while(i--)
{if($.isFunction(handler))
{if(!excludeWysiwyg)
$editorContainer.unbind('scewys'+ events[i],handler);if(!excludeSource)
$editorContainer.unbind('scesrc'+ events[i],handler);}}
return this;};base.blur=function(handler,excludeWysiwyg,excludeSource){if($.isFunction(handler))
base.bind('blur',handler,excludeWysiwyg,excludeSource);else if(!base.sourceMode())
{if(!$blurElm)
$blurElm=$('<input style="position:absolute;width:0;height:0;opacity:0;border:0;padding:0;filter:alpha(opacity=0)" type="text" />').appendTo($editorContainer);$blurElm.removeAttr('disabled').show().focus().blur().hide().attr('disabled','disabled');}
else
$sourceEditor.blur();return this;};base.focus=function(handler,excludeWysiwyg,excludeSource){if($.isFunction(handler))
base.bind('focus',handler,excludeWysiwyg,excludeSource);else
{if(!base.inSourceMode())
{wysiwygEditor.contentWindow.focus();$wysiwygBody[0].focus();if(lastRange)
{rangeHelper.selectRange(lastRange);lastRange=null;}}
else
sourceEditor.focus();}
return this;};base.keyDown=function(handler,excludeWysiwyg,excludeSource){return base.bind('keydown',handler,excludeWysiwyg,excludeSource);};base.keyPress=function(handler,excludeWysiwyg,excludeSource){return base.bind('keypress',handler,excludeWysiwyg,excludeSource);};base.keyUp=function(handler,excludeWysiwyg,excludeSource){return base.bind('keyup',handler,excludeWysiwyg,excludeSource);};base.nodeChanged=function(handler){return base.bind('nodechanged',handler,false,true);};base.selectionChanged=function(handler){return base.bind('selectionchanged',handler,false,true);};emoticonsKeyPress=function(e){var pos=0,curChar=String.fromCharCode(e.which);if($(currentBlockNode).is('code')||$(currentBlockNode).parents('code').length)
return;if(!base.emoticonsCache)
{base.emoticonsCache=[];$.each($.extend({},options.emoticons.more,options.emoticons.dropdown,options.emoticons.hidden),function(key,url){base.emoticonsCache[pos++]=[key,_tmpl('emoticon',{key:key,url:url.url||url,tooltip:url.tooltip||key})];});base.emoticonsCache.sort(function(a,b){return a[0].length- b[0].length;});base.longestEmoticonCode=base.emoticonsCache[base.emoticonsCache.length- 1][0].length;}
if(base.getRangeHelper().raplaceKeyword(base.emoticonsCache,true,true,base.longestEmoticonCode,options.emoticonsCompat,curChar))
{if(options.emoticonsCompat)
currentEmoticons=$wysiwygBody.find('img[data-sceditor-emoticon]');return(/^\s$/.test(curChar)&&options.emoticonsCompat);}};emoticonsCheckWhitespace=function(){if(!currentEmoticons.length)
return;var prev,next,parent,range,previousText,rangeStartContainer,currentBlock=base.currentBlockNode(),rangeStart=false,noneWsRegex=/[^\s\xA0\u2002\u2003\u2009]+/;currentEmoticons=$.map(currentEmoticons,function(emoticon){if(!emoticon||!emoticon.parentNode)
return null;if(!$.contains(currentBlock,emoticon))
return emoticon;prev=emoticon.previousSibling;next=emoticon.nextSibling;previousText=prev.nodeValue;if(previousText===null)
previousText=prev.innerText||'';if((!prev||!noneWsRegex.test(prev.nodeValue.slice(-1)))&&(!next||!noneWsRegex.test((next.nodeValue||'')[0])))
return emoticon;parent=emoticon.parentNode;range=rangeHelper.cloneSelected();rangeStartContainer=range.startContainer;previousText=previousText+ $(emoticon).data('sceditor-emoticon');if(rangeStartContainer===next)
rangeStart=previousText.length+ range.startOffset;else if(rangeStartContainer===currentBlock&&currentBlock.childNodes[range.startOffset]===next)
rangeStart=previousText.length;else if(rangeStartContainer===prev)
rangeStart=range.startOffset;if(!next||next.nodeType!==3)
next=parent.insertBefore(parent.ownerDocument.createTextNode(''),next);next.insertData(0,previousText);parent.removeChild(prev);parent.removeChild(emoticon);if(rangeStart!==false)
{range.setStart(next,rangeStart);range.collapse(true);rangeHelper.selectRange(range);}
return null;});};base.emoticons=function(enable){if(!enable&&enable!==false)
return options.emoticonsEnabled;options.emoticonsEnabled=enable;if(enable)
{$wysiwygBody.keypress(emoticonsKeyPress);if(!base.sourceMode())
{rangeHelper.saveRange();replaceEmoticons($wysiwygBody[0]);currentEmoticons=$wysiwygBody.find('img[data-sceditor-emoticon]');rangeHelper.restoreRange();}}
else
{$wysiwygBody.find('img[data-sceditor-emoticon]').replaceWith(function(){return $(this).data('sceditor-emoticon');});currentEmoticons=[];$wysiwygBody.unbind('keypress',emoticonsKeyPress);}
return this;};base.css=function(css){if(!inlineCss)
inlineCss=$('<style id="#inline" />').appendTo($wysiwygDoc.find('head'))[0];if(typeof css!='string')
return inlineCss.styleSheet?inlineCss.styleSheet.cssText:inlineCss.innerHTML;if(inlineCss.styleSheet)
inlineCss.styleSheet.cssText=css;else
inlineCss.innerHTML=css;return this;};handleKeyDown=function(e){var shortcut=[],shift_keys={'`':'~','1':'!','2':'@','3':'#','4':'$','5':'%','6':'^','7':'&','8':'*','9':'(','0':')','-':'_','=':'+',';':':','\'':'"',',':'<','.':'>','/':'?','\\':'|','[':'{',']':'}'},special_keys={8:'backspace',9:'tab',13:'enter',19:'pause',20:'capslock',27:'esc',32:'space',33:'pageup',34:'pagedown',35:'end',36:'home',37:'left',38:'up',39:'right',40:'down',45:'insert',46:'del',91:'win',92:'win',93:'select',96:'0',97:'1',98:'2',99:'3',100:'4',101:'5',102:'6',103:'7',104:'8',105:'9',106:'*',107:'+',109:'-',110:'.',111:'/',112:'f1',113:'f2',114:'f3',115:'f4',116:'f5',117:'f6',118:'f7',119:'f8',120:'f9',121:'f10',122:'f11',123:'f12',144:'numlock',145:'scrolllock',186:';',187:'=',188:',',189:'-',190:'.',191:'/',192:'`',219:'[',220:'\\',221:']',222:'\''},numpad_shift_keys={109:'-',110:'del',111:'/',96:'0',97:'1',98:'2',99:'3',100:'4',101:'5',102:'6',103:'7',104:'8',105:'9'},which=e.which,character=special_keys[which]||String.fromCharCode(which).toLowerCase();if(e.ctrlKey)
shortcut.push('ctrl');if(e.altKey)
shortcut.push('alt');if(e.shiftKey)
{shortcut.push('shift');if(numpad_shift_keys[which])
character=numpad_shift_keys[which];else if(shift_keys[character])
character=shift_keys[character];}
if(character&&(which<16||which>18))
shortcut.push(character);shortcut=shortcut.join('+');if(shortcutHandlers[shortcut])
return shortcutHandlers[shortcut].call(base);};base.addShortcut=function(shortcut,cmd){shortcut=shortcut.toLowerCase();if(typeof cmd==="string")
{shortcutHandlers[shortcut]=function(){handleCommand($toolbar.find('.sceditor-button-'+ cmd),base.commands[cmd]);return false;};}
else
shortcutHandlers[shortcut]=cmd;return this;};base.removeShortcut=function(shortcut){delete shortcutHandlers[shortcut.toLowerCase()];return this;};handleBackSpace=function(e){var node,offset,tmpRange,range,parent;if($.sceditor.ie||options.disableBlockRemove||e.which!==8||!(range=rangeHelper.selectedRange()))
return;if(!window.getSelection)
{node=range.parentElement();tmpRange=$wysiwygDoc[0].selection.createRange();tmpRange.moveToElementText(node);tmpRange.setEndPoint('EndToStart',range);offset=tmpRange.text.length;}
else
{node=range.startContainer;offset=range.startOffset;}
if(offset!==0||!(parent=currentStyledBlockNode()))
return;while(node!==parent)
{while(node.previousSibling)
{node=node.previousSibling;if(node.nodeType!==3||node.nodeValue)
return;}
if(!(node=node.parentNode))
return;}
if(!parent||$(parent).is('body'))
return;base.clearBlockFormatting(parent);return false;};currentStyledBlockNode=function(){var block=currentBlockNode;while(!$.sceditor.dom.hasStyling(block))
{if(!(block=block.parentNode)||$(block).is('body'))
return;}
return block;};base.clearBlockFormatting=function(block){block=block||currentStyledBlockNode();if(!block||$(block).is('body'))
return this;rangeHelper.saveRange();lastRange=null;block.className='';$(block).attr('style','');if(!$(block).is('p,div'))
$.sceditor.dom.convertElement(block,'p');rangeHelper.restoreRange();return this;};init();};$.sceditor.ie=(function(){var undef,v=3,div=document.createElement('div'),all=div.getElementsByTagName('i');do{div.innerHTML='<!--[if gt IE '+(++v)+']><i></i><![endif]-->';}while(all[0]);if((document.documentMode&&document.all&&window.atob))
v=10;if(v===4&&document.documentMode)
v=11;return v>4?v:undef;}());$.sceditor.ios=/iPhone|iPod|iPad| wosbrowser\//i.test(navigator.userAgent);$.sceditor.isWysiwygSupported=(function(){var match,isUnsupported,contentEditable=$('<div contenteditable="true">')[0].contentEditable,contentEditableSupported=typeof contentEditable!=='undefined'&&contentEditable!=='inherit',userAgent=navigator.userAgent;if(!contentEditableSupported)
return false;isUnsupported=/Opera Mobi|Opera Mini/i.test(userAgent);if(/Android/i.test(userAgent))
{isUnsupported=true;if(/Safari/.test(userAgent))
{match=/Safari\/(\d+)/.exec(userAgent);isUnsupported=(!match||!match[1]?true:match[1]<534);}}
if(/ Silk\//i.test(userAgent))
{match=/AppleWebKit\/(\d+)/.exec(userAgent);isUnsupported=(!match||!match[1]?true:match[1]<534);}
if($.sceditor.ios)
isUnsupported=!/OS [5-9](_\d)+ like Mac OS X/i.test(userAgent);if(/fennec/i.test(userAgent))
isUnsupported=false;if(/OneBrowser/i.test(userAgent))
isUnsupported=false;if(navigator.vendor==='UCWEB')
isUnsupported=false;return!isUnsupported;}());$.sceditor.regexEscape=function(str){return str.replace(/[\$\?\[\]\.\*\(\)\|\\]/g,'\\$&');};$.sceditor.escapeEntities=function(str){if(!str)
return str;return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/ {2}/g,' &nbsp;').replace(/\r\n|\r/g,'\n').replace(/\n/g,'<br />');};$.sceditor.locale={};$.sceditor.commands={bold:{exec:'bold',tooltip:'Bold',shortcut:'ctrl+b'},italic:{exec:'italic',tooltip:'Italic',shortcut:'ctrl+i'},underline:{exec:'underline',tooltip:'Underline',shortcut:'ctrl+u'},strike:{exec:'strikethrough',tooltip:'Strikethrough'},subscript:{exec:'subscript',tooltip:'Subscript'},superscript:{exec:'superscript',tooltip:'Superscript'},left:{exec:'justifyleft',tooltip:'Align left'},center:{exec:'justifycenter',tooltip:'Center'},right:{exec:'justifyright',tooltip:'Align right'},justify:{exec:'justifyfull',tooltip:'Justify'},font:{_dropDown:function(editor,caller,callback){var fonts=editor.opts.fonts.split(','),content=$('<div />'),clickFunc=function(){callback($(this).data('font'));editor.closeDropDown(true);return false;};for(var i=0;i<fonts.length;i++)
content.append(_tmpl('fontOpt',{font:fonts[i]},true).click(clickFunc));editor.createDropDown(caller,'font-picker',content);},exec:function(caller){var editor=this;$.sceditor.command.get('font')._dropDown(editor,caller,function(fontName){editor.execCommand('fontname',fontName);});},tooltip:'Font Name'},size:{_dropDown:function(editor,caller,callback){var content=$('<div />'),clickFunc=function(e){callback($(this).data('size'));editor.closeDropDown(true);e.preventDefault();};for(var i=1;i<=7;i++)
content.append(_tmpl('sizeOpt',{size:i},true).click(clickFunc));editor.createDropDown(caller,'fontsize-picker',content);},exec:function(caller){var editor=this;$.sceditor.command.get('size')._dropDown(editor,caller,function(fontSize){editor.execCommand('fontsize',fontSize);});},tooltip:'Font Size'},color:{_dropDown:function(editor,caller,callback){var i,x,color,colors,genColor={r:255,g:255,b:255},content=$('<div />'),colorColumns=editor.opts.colors?editor.opts.colors.split('|'):new Array(21),html=[],cmd=$.sceditor.command.get('color');if(!cmd._htmlCache)
{for(i=0;i<colorColumns.length;++i)
{colors=colorColumns[i]?colorColumns[i].split(','):new Array(21);html.push('<div class="sceditor-color-column">');for(x=0;x<colors.length;++x)
{color=colors[x]||"#"+ genColor.r.toString(16)+ genColor.g.toString(16)+ genColor.b.toString(16);html.push('<a href="#" class="sceditor-color-option" style="background-color: '+color+'" data-color="'+color+'"></a>');if(x%5===0)
{genColor.g-=51;genColor.b=255;}
else
genColor.b-=51;}
html.push('</div>');if(i%5===0)
{genColor.r-=51;genColor.g=255;genColor.b=255;}
else
{genColor.g=255;genColor.b=255;}}
cmd._htmlCache=html.join('');}
content.append(cmd._htmlCache).find('a').click(function(e){callback($(this).attr('data-color'));editor.closeDropDown(true);e.preventDefault();});editor.createDropDown(caller,'color-picker',content);},exec:function(caller){var editor=this;$.sceditor.command.get('color')._dropDown(editor,caller,function(color){editor.execCommand('forecolor',color);});},tooltip:'Font Color'},removeformat:{exec:'removeformat',tooltip:'Remove Formatting'},cut:{exec:'cut',tooltip:'Cut',errorMessage:'Your browser does not allow the cut command. Please use the keyboard shortcut Ctrl/Cmd-X'},copy:{exec:'copy',tooltip:'Copy',errorMessage:'Your browser does not allow the copy command. Please use the keyboard shortcut Ctrl/Cmd-C'},paste:{exec:'paste',tooltip:'Paste',errorMessage:'Your browser does not allow the paste command. Please use the keyboard shortcut Ctrl/Cmd-V'},pastetext:{exec:function(caller){var val,editor=this,content=_tmpl('pastetext',{label:editor._('Paste your text inside the following box:'),insert:editor._('Insert')},true);content.find('.button').click(function(e){val=content.find('#txt').val();if(val)
editor.wysiwygEditorInsertText(val);editor.closeDropDown(true);e.preventDefault();});editor.createDropDown(caller,'pastetext',content);},tooltip:'Paste Text'},bulletlist:{exec:'insertunorderedlist',tooltip:'Bullet list'},orderedlist:{exec:'insertorderedlist',tooltip:'Numbered list'},table:{exec:function(caller){var editor=this,content=_tmpl('table',{rows:editor._('Rows:'),cols:editor._('Cols:'),insert:editor._('Insert')},true);content.find('.button').click(function(e){var rows=content.find('#rows').val()- 0,cols=content.find('#cols').val()- 0,html='<table>';if(rows<1||cols<1)
return;for(var row=0;row<rows;row++){html+='<tr>';for(var col=0;col<cols;col++)
html+='<td>'+($.sceditor.ie?'':'<br />')+'</td>';html+='</tr>';}
html+='</table>';editor.wysiwygEditorInsertHtml(html);editor.closeDropDown(true);e.preventDefault();});editor.createDropDown(caller,'inserttable',content);},tooltip:'Insert a table'},horizontalrule:{exec:'inserthorizontalrule',tooltip:'Insert a horizontal rule'},code:{forceNewLineAfter:['code'],exec:function(){this.wysiwygEditorInsertHtml('<code>','<br /></code>');},tooltip:'Code'},image:{exec:function(caller){var editor=this,content=_tmpl('image',{url:editor._('URL:'),width:editor._('Width (optional):'),height:editor._('Height (optional):'),insert:editor._('Insert')},true);content.find('.button').click(function(e){var val=content.find('#image').val(),width=content.find('#width').val(),height=content.find('#height').val(),attrs='';if(width)
attrs+=' width="'+ width+'"';if(height)
attrs+=' height="'+ height+'"';if(val&&val!=='http://')
editor.wysiwygEditorInsertHtml('<img'+ attrs+' src="'+ val+'" />');editor.closeDropDown(true);e.preventDefault();});editor.createDropDown(caller,'insertimage',content);},tooltip:'Insert an image'},email:{exec:function(caller){var editor=this,content=_tmpl('email',{label:editor._('E-mail:'),insert:editor._('Insert')},true);content.find('.button').click(function(e){var val=content.find('#email').val();if(val)
{editor.focus();if(!editor.getRangeHelper().selectedHtml())
editor.wysiwygEditorInsertHtml('<a href="'+'mailto:'+ val+'">'+ val+'</a>');else
editor.execCommand('createlink','mailto:'+ val);}
editor.closeDropDown(true);e.preventDefault();});editor.createDropDown(caller,'insertemail',content);},tooltip:'Insert an email'},link:{exec:function(caller){var editor=this,content=_tmpl('link',{url:editor._('URL:'),desc:editor._('Description (optional):'),ins:editor._('Insert')},true);content.find('.button').click(function(e){var val=content.find('#link').val(),description=content.find('#des').val();if(val&&val!=='http://'){editor.focus();if(!editor.getRangeHelper().selectedHtml()||description)
{if(!description)
description=val;editor.wysiwygEditorInsertHtml('<a href="'+ val+'">'+ description+'</a>');}
else
editor.execCommand('createlink',val);}
editor.closeDropDown(true);e.preventDefault();});editor.createDropDown(caller,'insertlink',content);},tooltip:'Insert a link'},unlink:{state:function(){var $current=$(this.currentNode());return $current.is('a')||$current.parents('a').length>0?0:-1;},exec:function(){var $current=$(this.currentNode()),$anchor=$current.is('a')?$current:$current.parents('a').first();if($anchor.length)
$anchor.replaceWith($anchor.contents());},tooltip:'Unlink'},quote:{forceNewLineAfter:['blockquote'],exec:function(caller,html,author){var before='<blockquote>',end='</blockquote>';if(html)
{author=(author?'<cite>'+ author+'</cite>':'');before=before+ author+ html+ end;end=null;}
else if(this.getRangeHelper().selectedHtml()==='')
end=$.sceditor.ie?'':'<br />'+ end;this.wysiwygEditorInsertHtml(before,end);},tooltip:'Insert a Quote'},emoticon:{exec:function(caller){var editor=this;var createContent=function(includeMore){var emoticonsCompat=editor.opts.emoticonsCompat,rangeHelper=editor.getRangeHelper(),startSpace=emoticonsCompat&&rangeHelper.getOuterText(true,1)!==' '?' ':'',endSpace=emoticonsCompat&&rangeHelper.getOuterText(false,1)!==' '?' ':'',$content=$('<div />'),$line=$('<div />').appendTo($content),emoticons=$.extend({},editor.opts.emoticons.dropdown,includeMore?editor.opts.emoticons.more:{}),perLine=0;$.each(emoticons,function(){perLine++;});perLine=Math.sqrt(perLine);$.each(emoticons,function(code,emoticon){$line.append($('<img />').attr({src:emoticon.url||emoticon,alt:code,title:emoticon.tooltip||code}).click(function(){editor.insert(startSpace+ $(this).attr('alt')+ endSpace,null,false).closeDropDown(true);return false;}));if($line.children().length>=perLine)
$line=$('<div />').appendTo($content);});if(!includeMore)
{$content.append($(editor._('<a class="sceditor-more">{0}</a>',editor._('More'))).click(function(){editor.createDropDown(caller,'more-emoticons',createContent(true));return false;}));}
return $content;};editor.createDropDown(caller,'emoticons',createContent(false));},txtExec:function(caller){$.sceditor.command.get('emoticon').exec.call(this,caller);},tooltip:'Insert an emoticon'},youtube:{_dropDown:function(editor,caller,handleIdFunc){var matches,content=_tmpl('youtubeMenu',{label:editor._('Video URL:'),insert:editor._('Insert')},true);content.find('.button').click(function(e){var val=content.find('#link').val().replace('http://','');if(val!==''){matches=val.match(/(?:v=|v\/|embed\/|youtu.be\/)(.{11})/);if(matches)
val=matches[1];if(/^[a-zA-Z0-9_\-]{11}$/.test(val))
handleIdFunc(val);else
alert('Invalid YouTube video');}
editor.closeDropDown(true);e.preventDefault();});editor.createDropDown(caller,'insertlink',content);},exec:function(caller){var editor=this;$.sceditor.command.get('youtube')._dropDown(editor,caller,function(id){editor.wysiwygEditorInsertHtml(_tmpl('youtube',{id:id}));});},tooltip:'Insert a YouTube video'},date:{_date:function(editor){var now=new Date(),year=now.getYear(),month=now.getMonth()+1,day=now.getDate();if(year<2000)
year=1900+ year;if(month<10)
month='0'+ month;if(day<10)
day='0'+ day;return editor.opts.dateFormat.replace(/year/i,year).replace(/month/i,month).replace(/day/i,day);},exec:function(){this.insertText($.sceditor.command.get('date')._date(this));},txtExec:function(){this.insertText($.sceditor.command.get('date')._date(this));},tooltip:'Insert current date'},time:{_time:function(){var now=new Date(),hours=now.getHours(),mins=now.getMinutes(),secs=now.getSeconds();if(hours<10)
hours='0'+ hours;if(mins<10)
mins='0'+ mins;if(secs<10)
secs='0'+ secs;return hours+':'+ mins+':'+ secs;},exec:function(){this.insertText($.sceditor.command.get('time')._time());},txtExec:function(){this.insertText($.sceditor.command.get('time')._time());},tooltip:'Insert current time'},ltr:{state:function(parents,firstBlock){return firstBlock&&firstBlock.style.direction==='ltr';},exec:function(){var editor=this,elm=editor.getRangeHelper().getFirstBlockParent(),$elm=$(elm);editor.focus();if(!elm||$elm.is('body'))
{editor.execCommand('formatBlock','p');elm=editor.getRangeHelper().getFirstBlockParent();$elm=$(elm);if(!elm||$elm.is('body'))
return;}
if($elm.css('direction')==='ltr')
$elm.css('direction','');else
$elm.css('direction','ltr');},tooltip:'Left-to-Right'},rtl:{state:function(parents,firstBlock){return firstBlock&&firstBlock.style.direction==='rtl';},exec:function(){var editor=this,elm=editor.getRangeHelper().getFirstBlockParent(),$elm=$(elm);editor.focus();if(!elm||$elm.is('body'))
{editor.execCommand('formatBlock','p');elm=editor.getRangeHelper().getFirstBlockParent();$elm=$(elm);if(!elm||$elm.is('body'))
return;}
if($elm.css('direction')==='rtl')
$elm.css('direction','');else
$elm.css('direction','rtl');},tooltip:'Right-to-Left'},print:{exec:'print',tooltip:'Print'},maximize:{state:function(){return this.maximize();},exec:function(){this.maximize(!this.maximize());},txtExec:function(){this.maximize(!this.maximize());},tooltip:'Maximize',shortcut:'ctrl+shift+m'},source:{exec:function(){this.toggleSourceMode();},txtExec:function(){this.toggleSourceMode();},tooltip:'View source',shortcut:'ctrl+shift+s'},ignore:{}};$.sceditor.rangeHelper=function(w,d){var win,doc,init,_createMarker,_isOwner,isW3C=true,startMarker='sceditor-start-marker',endMarker='sceditor-end-marker',characterStr='character',base=this;init=function(window,document){doc=document||window.contentDocument||window.document;win=window;isW3C=!!window.getSelection;}(w,d);base.insertHTML=function(html,endHTML){var node,div,range=base.selectedRange();if(endHTML)
html+=base.selectedHtml()+ endHTML;if(isW3C)
{div=doc.createElement('div');node=doc.createDocumentFragment();div.innerHTML=html;while(div.firstChild)
node.appendChild(div.firstChild);base.insertNode(node);}
else
{if(!range)
return false;range.pasteHTML(html);}};base.insertNode=function(node,endNode){if(isW3C)
{var selection,selectAfter,toInsert=doc.createDocumentFragment(),range=base.selectedRange();if(!range)
return false;toInsert.appendChild(node);if(endNode)
{toInsert.appendChild(range.extractContents());toInsert.appendChild(endNode);}
selectAfter=toInsert.lastChild;if(!selectAfter)
return;range.deleteContents();range.insertNode(toInsert);selection=doc.createRange();selection.setStartAfter(selectAfter);base.selectRange(selection);}
else
base.insertHTML(node.outerHTML,endNode?endNode.outerHTML:null);};base.cloneSelected=function(){var range=base.selectedRange();if(range)
return isW3C?range.cloneRange():range.duplicate();};base.selectedRange=function(){var range,firstChild,sel=isW3C?win.getSelection():doc.selection;if(!sel)
return;if(sel.getRangeAt&&sel.rangeCount<=0)
{firstChild=doc.body;while(firstChild.firstChild)
firstChild=firstChild.firstChild;range=doc.createRange();range.setStart(firstChild,0);sel.addRange(range);}
if(isW3C)
range=sel.getRangeAt(0);if(!isW3C&&sel.type!=='Control')
range=sel.createRange();return _isOwner(range)?range:null;};_isOwner=function(range){var parent;return(range&&range.parentElement&&(parent=range.parentElement()))?parent.ownerDocument===doc:true;};base.hasSelection=function(){var range,sel=isW3C?win.getSelection():doc.selection;if(isW3C||!sel)
return sel&&sel.rangeCount>0;range=sel.createRange();return range&&_isOwner(range);};base.selectedHtml=function(){var div,range=base.selectedRange();if(!range)
return'';if(!isW3C&&range.text!==''&&range.htmlText)
return range.htmlText;if(isW3C)
{div=doc.createElement('div');div.appendChild(range.cloneContents());return div.innerHTML;}
return'';};base.parentNode=function(){var range=base.selectedRange();if(range)
return range.parentElement?range.parentElement():range.commonAncestorContainer;};base.getFirstBlockParent=function(n){var func=function(node){if(!$.sceditor.dom.isInline(node,true))
return node;node=node?node.parentNode:null;return node?func(node):null;};return func(n||base.parentNode());};base.insertNodeAt=function(start,node){var currentRange=base.selectedRange(),range=base.cloneSelected();if(!range)
return false;range.collapse(start);if(range.insertNode)
range.insertNode(node);else
range.pasteHTML(node.outerHTML);base.selectRange(currentRange);};_createMarker=function(id){base.removeMarker(id);var marker=doc.createElement('span');marker.id=id;marker.style.lineHeight='0';marker.style.display='none';marker.className='sceditor-selection sceditor-ignore';marker.innerHTML=' ';return marker;};base.insertMarkers=function(){base.insertNodeAt(true,_createMarker(startMarker));base.insertNodeAt(false,_createMarker(endMarker));};base.getMarker=function(id){return doc.getElementById(id);};base.removeMarker=function(id){var marker=base.getMarker(id);if(marker)
marker.parentNode.removeChild(marker);};base.removeMarkers=function(){base.removeMarker(startMarker);base.removeMarker(endMarker);};base.saveRange=function(){base.insertMarkers();};base.selectRange=function(range){if(isW3C)
{win.getSelection().removeAllRanges();win.getSelection().addRange(range);}
else
range.select();};base.restoreRange=function(){var marker,range=base.selectedRange(),start=base.getMarker(startMarker),end=base.getMarker(endMarker);if(!start||!end||!range)
return false;if(!isW3C)
{range=doc.body.createTextRange();marker=doc.body.createTextRange();marker.moveToElementText(start);range.setEndPoint('StartToStart',marker);range.moveStart(characterStr,0);marker.moveToElementText(end);range.setEndPoint('EndToStart',marker);range.moveEnd(characterStr,0);base.selectRange(range);}
else
{range=doc.createRange();range.setStartBefore(start);range.setEndAfter(end);base.selectRange(range);}
base.removeMarkers();};base.selectOuterText=function(left,right){var range=base.cloneSelected();if(!range)
return false;range.collapse(false);if(!isW3C)
{range.moveStart(characterStr,0-left);range.moveEnd(characterStr,right);}
else
{range.setStart(range.startContainer,range.startOffset-left);range.setEnd(range.endContainer,range.endOffset+right);}
base.selectRange(range);};base.getOuterText=function(before,length){var ret='',range=base.cloneSelected();if(!range)
return'';range.collapse(false);if(before)
{if(!isW3C)
{range.moveStart(characterStr,0-length);ret=range.text;}
else
{ret=range.startContainer.textContent.substr(0,range.startOffset);ret=ret.substr(Math.max(0,ret.length- length));}}
else
{if(!isW3C)
{range.moveEnd(characterStr,length);ret=range.text;}
else
ret=range.startContainer.textContent.substr(range.startOffset,length);}
return ret;};base.raplaceKeyword=function(keywords,includeAfter,keywordsSorted,longestKeyword,requireWhiteSpace,currrentChar){if(!keywordsSorted)
{keywords.sort(function(a,b){return a.length- b.length;});}
var beforeStr,str,keywordIdx,numberCharsLeft,keywordRegex,startIdx,keyword,i=keywords.length,maxKeyLen=longestKeyword||keywords[i-1][0].length;if(requireWhiteSpace)
{if(!isW3C)
return false;++maxKeyLen;}
beforeStr=base.getOuterText(true,maxKeyLen);str=beforeStr+(currrentChar!=null?currrentChar:'');if(includeAfter)
str+=base.getOuterText(false,maxKeyLen);while(i--)
{keyword=keywords[i][0];keywordRegex=new RegExp('(?:[\\s\xA0\u2002\u2003\u2009])'+ $.sceditor.regexEscape(keyword)+'(?=[\\s\xA0\u2002\u2003\u2009])');startIdx=beforeStr.length- 1- keyword.length;if(requireWhiteSpace)
--startIdx;startIdx=Math.max(0,startIdx);if((keywordIdx=requireWhiteSpace?str.substr(startIdx).search(keywordRegex):str.indexOf(keyword,startIdx))>-1)
{if(requireWhiteSpace)
keywordIdx+=startIdx+ 1;if(keywordIdx>beforeStr.length||(keywordIdx+ keyword.length+(requireWhiteSpace?1:0))<beforeStr.length)
continue;numberCharsLeft=beforeStr.length- keywordIdx;base.selectOuterText(numberCharsLeft,keyword.length- numberCharsLeft-(currrentChar!=null&&/^\S/.test(currrentChar)?1:0));base.insertHTML(keywords[i][1]);return true;}}
return false;};base.compare=function(rangeA,rangeB){if(!rangeB)
rangeB=base.selectedRange();if(!rangeA||!rangeB)
return!rangeA&&!rangeB;if(!isW3C)
{return _isOwner(rangeA)&&_isOwner(rangeB)&&rangeA.compareEndPoints('EndToEnd',rangeB)===0&&rangeA.compareEndPoints('StartToStart',rangeB)===0;}
return rangeA.compareBoundaryPoints(Range.END_TO_END,rangeB)===0&&rangeA.compareBoundaryPoints(Range.START_TO_START,rangeB)===0;};};$.sceditor.dom={traverse:function(node,func,innermostFirst,siblingsOnly,reverse){if(node)
{node=reverse?node.lastChild:node.firstChild;while(node!=null)
{var next=reverse?node.previousSibling:node.nextSibling;if(!innermostFirst&&func(node)===false)
return false;if(!siblingsOnly&&this.traverse(node,func,innermostFirst,siblingsOnly,reverse)===false)
return false;if(innermostFirst&&func(node)===false)
return false;node=next;}}},rTraverse:function(node,func,innermostFirst,siblingsOnly){this.traverse(node,func,innermostFirst,siblingsOnly,true);},parseHTML:function(html,context){var ret=[],tmp=(context||document).createElement('div');tmp.innerHTML=html;$.merge(ret,tmp.childNodes);return ret;},hasStyling:function(elm){var $elm=$(elm);return elm&&(!$elm.is('p,div')||elm.className||$elm.attr('style')||!$.isEmptyObject($elm.data()));},convertElement:function(elm,newElement){var child,attr,i=elm.attributes.length,newTag=elm.ownerDocument.createElement(newElement);while(i--)
{attr=elm.attributes[i];if(!$.sceditor.ie||attr.specified)
{if($.sceditor.ie<8&&/style/i.test(attr.name))
elm.style.cssText=elm.style.cssText;else
newTag.setAttribute(attr.name,attr.value);}}
while((child=elm.firstChild))
newTag.appendChild(child);elm.parentNode.replaceChild(newTag,elm);return newTag;},blockLevelList:'|body|hr|p|div|h1|h2|h3|h4|h5|h6|address|pre|form|table|tbody|thead|tfoot|th|tr|td|li|ol|ul|blockquote|center|',isInline:function(elm,includeCodeAsBlock){if(!elm||elm.nodeType!==1)
return true;elm=elm.tagName.toLowerCase();if(elm==='code')
return!includeCodeAsBlock;return $.sceditor.dom.blockLevelList.indexOf('|'+ elm+'|')<0;},copyCSS:function(from,to){to.style.cssText=from.style.cssText+ to.style.cssText;},fixNesting:function(node){var base=this,getLastInlineParent=function(node){while(base.isInline(node.parentNode,true))
node=node.parentNode;return node;};base.traverse(node,function(node){if(node.nodeType===1&&!base.isInline(node,true)&&base.isInline(node.parentNode,true))
{var parent=getLastInlineParent(node),rParent=parent.parentNode,before=base.extractContents(parent,node),middle=node;base.copyCSS(parent,middle);rParent.insertBefore(before,parent);rParent.insertBefore(middle,parent);}});},findCommonAncestor:function(node1,node2){return $(node1).parents().has($(node2)).first();},getSibling:function(node,previous){var sibling;if(!node)
return null;if((sibling=node[previous?'previousSibling':'nextSibling']))
return sibling;return $.sceditor.dom.getSibling(node.parentNode,previous);},removeWhiteSpace:function(root,preserveNewLines){var nodeValue,nodeType,next,previous,cssWS,nextNode,trimStart,sibling,getSibling=$.sceditor.dom.getSibling,isInline=$.sceditor.dom.isInline,node=root.firstChild,whitespace=/[\t ]+/g,witespaceAndLines=/[\t\n\r ]+/g;while(node)
{nextNode=node.nextSibling;nodeValue=node.nodeValue;nodeType=node.nodeType;if(nodeType===1&&node.firstChild)
{cssWS=$(node).css('whiteSpace');if(!/pre(?:\-wrap)?$/i.test(cssWS))
$.sceditor.dom.removeWhiteSpace(node,/line$/i.test(cssWS));}
if(nodeType===3&&nodeValue)
{next=getSibling(node);previous=getSibling(node,true);sibling=previous;trimStart=false;while($(sibling).hasClass('sceditor-ignore'))
sibling=getSibling(sibling,true);if(isInline(node)&&sibling)
{while(sibling.lastChild)
sibling=sibling.lastChild;trimStart=sibling.nodeType===3?/[\t\n\r ]$/.test(sibling.nodeValue):!isInline(sibling);}
if(!isInline(node)||!previous||!isInline(previous)||trimStart)
nodeValue=nodeValue.replace(/^[\t\n\r ]+/,'');if(!isInline(node)||!next||!isInline(next))
nodeValue=nodeValue.replace(/[\t\n\r ]+$/,'');if(!nodeValue.length)
root.removeChild(node);else
node.nodeValue=nodeValue.replace(preserveNewLines?whitespace:witespaceAndLines,' ');}
node=nextNode;}},extractContents:function(startNode,endNode){var base=this,$commonAncestor=base.findCommonAncestor(startNode,endNode),commonAncestor=!$commonAncestor?null:$commonAncestor[0],startReached=false,endReached=false;return(function extract(root){var df=startNode.ownerDocument.createDocumentFragment();base.traverse(root,function(node){if(endReached||(node===endNode&&startReached))
{endReached=true;return false;}
if(node===startNode)
startReached=true;var c,n;if(startReached)
{if(jQuery.contains(node,endNode)&&node.nodeType===1)
{c=extract(node);n=node.cloneNode(false);n.appendChild(c);df.appendChild(n);}
else
df.appendChild(node);}
else if(jQuery.contains(node,startNode)&&node.nodeType===1)
{c=extract(node);n=node.cloneNode(false);n.appendChild(c);df.appendChild(n);}},false);return df;}(commonAncestor));}};$.sceditor.plugins={};$.sceditor.PluginManager=function(owner){var base=this;var plugins=[];var editorInstance=owner;var formatSignalName=function(signal){return'signal'+ signal.charAt(0).toUpperCase()+ signal.slice(1);};var callHandlers=function(args,returnAtFirst){args=[].slice.call(args);var i=plugins.length,signal=formatSignalName(args.shift());while(i--)
{if(signal in plugins[i])
{if(returnAtFirst)
return plugins[i][signal].apply(editorInstance,args);plugins[i][signal].apply(editorInstance,args);}}};base.call=function(){callHandlers(arguments,false);};base.callOnlyFirst=function(){return callHandlers(arguments,true);};base.iter=function(signal){signal=formatSignalName(signal);return(function(){var i=plugins.length;return{callNext:function(args){while(i--)
if(plugins[i]&&signal in plugins[i])
return plugins[i].apply(editorInstance,args);},hasNext:function(){var j=i;while(j--)
if(plugins[j]&&signal in plugins[j])
return true;return false;}};}());};base.hasHandler=function(signal){var i=plugins.length;signal=formatSignalName(signal);while(i--)
if(signal in plugins[i])
return true;return false;};base.exsists=function(plugin){if(plugin in $.sceditor.plugins)
{plugin=$.sceditor.plugins[plugin];return typeof plugin==='function'&&typeof plugin.prototype==='object';}
return false;};base.isRegistered=function(plugin){var i=plugins.length;if(!base.exsists(plugin))
return false;while(i--)
if(plugins[i]instanceof $.sceditor.plugins[plugin])
return true;return false;};base.register=function(plugin){if(!base.exsists(plugin))
return false;plugin=new $.sceditor.plugins[plugin]();plugins.push(plugin);if('init'in plugin)
plugin.init.apply(editorInstance);return true;};base.deregister=function(plugin){var removedPlugin,i=plugins.length,ret=false;if(!base.isRegistered(plugin))
return false;while(i--)
{if(plugins[i]instanceof $.sceditor.plugins[plugin])
{removedPlugin=plugins.splice(i,1)[0];ret=true;if('destroy'in removedPlugin)
removedPlugin.destroy.apply(editorInstance);}}
return ret;};base.destroy=function(){var i=plugins.length;while(i--)
if('destroy'in plugins[i])
plugins[i].destroy.apply(editorInstance);plugins=null;editorInstance=null;};};$.sceditor.command={get:function(name){return $.sceditor.commands[name]||null;},set:function(name,cmd){if(!name||!cmd)
return false;cmd=$.extend($.sceditor.commands[name]||{},cmd);cmd.remove=function(){$.sceditor.command.remove(name);};$.sceditor.commands[name]=cmd;return this;},remove:function(name){if($.sceditor.commands[name])
delete $.sceditor.commands[name];return this;}};$.sceditor.defaultOptions={toolbar:'bold,italic,underline,strike,subscript,superscript|left,center,right,justify|'+'font,size,color,removeformat|cut,copy,paste,pastetext|bulletlist,orderedlist|'+'table|code,quote|horizontalrule,image,email,link,unlink|emoticon,youtube,date,time|'+'ltr,rtl|print,maximize,source',toolbarExclude:null,style:'css/wysiwyg.css',fonts:'Arial,Arial Black,Comic Sans MS,Courier New,Georgia,Impact,Sans-serif,Serif,Times New Roman,Trebuchet MS,Verdana',colors:null,locale:'en',charset:'utf-8',emoticonsCompat:false,emoticonsEnabled:true,emoticonsRoot:'',emoticons:{dropdown:{':)':'../images/emoticons/smile.png',':angel:':'../images/emoticons/angel.png',':angry:':'../images/emoticons/angry.png','8-)':'../images/emoticons/cool.png',":'(":'../images/emoticons/cwy.png',':ermm:':'../images/emoticons/ermm.png',':D':'../images/emoticons/grin.png','<3':'../images/emoticons/heart.png',':(':'../images/emoticons/sad.png',':O':'../images/emoticons/shocked.png',':P':'../images/emoticons/tongue.png',';)':'../images/emoticons/wink.png'},more:{':alien:':'../images/emoticons/alien.png',':blink:':'../images/emoticons/blink.png',':blush:':'../images/emoticons/blush.png',':cheerful:':'../images/emoticons/cheerful.png',':devil:':'../images/emoticons/devil.png',':dizzy:':'../images/emoticons/dizzy.png',':getlost:':'../images/emoticons/getlost.png',':happy:':'../images/emoticons/happy.png',':kissing:':'../images/emoticons/kissing.png',':ninja:':'../images/emoticons/ninja.png',':pinch:':'../images/emoticons/pinch.png',':pouty:':'../images/emoticons/pouty.png',':sick:':'../images/emoticons/sick.png',':sideways:':'../images/emoticons/sideways.png',':silly:':'../images/emoticons/silly.png',':sleeping:':'../images/emoticons/sleeping.png',':unsure:':'../images/emoticons/unsure.png',':woot:':'../images/emoticons/w00t.png',':wassat:':'../images/emoticons/wassat.png',':whistling:':'../images/emoticons/whistling.png',':love:':'../images/emoticons/wub.png'},hidden:{}},width:null,height:null,resizeEnabled:true,resizeMinWidth:null,resizeMinHeight:null,resizeMaxHeight:null,resizeMaxWidth:null,resizeHeight:true,resizeWidth:true,getHtmlHandler:null,getTextHandler:null,dateFormat:'year-month-day',toolbarContainer:null,enablePasteFiltering:false,disablePasting:false,readOnly:false,rtl:false,autofocus:false,autofocusEnd:true,autoExpand:false,autoUpdate:false,spellcheck:true,runWithoutWysiwygSupport:false,id:null,plugins:'',zIndex:null,bbcodeTrim:false,disableBlockRemove:false,parserOptions:{},dropDownCss:{}};$.fn.sceditor=function(options){var $this,ret=[];options=options||{};if(!options.runWithoutWysiwygSupport&&!$.sceditor.isWysiwygSupported)
return;this.each(function(){$this=this.jquery?this:$(this);if($this.parents('.sceditor-container').length>0)
return;if(options==='state')
ret.push(!!$this.data('sceditor'));else if(options==='instance')
ret.push($this.data('sceditor'));else if(!$this.data('sceditor'))
(new $.sceditor(this,options));});if(!ret.length)
return this;return ret.length===1?ret[0]:$(ret);};})(jQuery,window,document);function toggleEditor(t){if(!t.hasClass('hide-editor')){t.html('Hide editor')
t.addClass('hide-editor')
return true;}else{var text=$('textarea#wysiwyg').val();$('#wysiwyg').parent().html('<textarea id="wysiwyg" name="text">'+$('#wysiwyg').val()+'</textarea>')
$('#wysiwyg').focus();t.html('Show editor')
t.removeClass('hide-editor')
return false;}}
function wysiwyg(opts){var id='#wysiwyg';var toolbar='bold,italic,underline|emoticon|left,center,right,justify|size,color|bulletlist,orderedlist|removeformat,maximize'
var height=150;var width="82%";var AE=true;switch(opts.type){case'new':toolbar="bold,italic,underline|emoticon|left,center,right,justify|size,color|bulletlist,orderedlist|removeformat,maximize";height=150;break;case'reply':toolbar="bold,italic,underline|emoticon|left,center,right,justify|size,color|bulletlist,orderedlist|removeformat,maximize";height=350;AE=false;break;case'clan':toolbar="bold,italic,underline|emoticon|left,center,right,justify|size,color|bulletlist,orderedlist|horizontalrule,link,unlink|removeformat,maximize";height=350;break;case'text':width="90%"
toolbar="bold,italic,underline|emoticon|left,center,right,justify|size,color|bulletlist,orderedlist|removeformat,maximize";height=450;break;}
if(opts.loadcss){$('<link rel="stylesheet" type="text/css" href="css/wysiwyg.css" >').appendTo("head");}
$(id).sceditor({'style':'css/wysiwyg.css',toolbar:toolbar,height:height,width:width,autoExpand:AE,autofocus:opts.autofocus,});}
function openEditor(type){switch(type){case'new':wysiwyg({type:'new',loadcss:false,autofocus:false});break;case'new-focus':wysiwyg({type:'new',loadcss:false,autofocus:true});break;case'reply':wysiwyg({type:'reply',loadcss:true,autofocus:true})
break;case'clan':wysiwyg({type:'clan',loadcss:true,autofocus:true})
break;case'text':wysiwyg({type:'text',loadcss:true,autofocus:true})
break;}}