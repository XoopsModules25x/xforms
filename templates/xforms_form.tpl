<div id="xforms">
<h1 class="center"><{$form_output.title}></h1>
<{if '' != $form_is_hidden}><p><b><{$form_is_hidden}></b></p><{/if}>
<{if '' != $form_intro}><p><{$form_intro}></p><{/if}>
<{$form_output.javascript}>
<form name="<{$form_output.name}>" id="<{$form_output.name}>" action="<{$form_output.action}>" method="<{$form_output.method}>" class="xforms" <{$form_output.extra}>>
<table class="outer bspacing1">
<{foreach item=element from=$form_output.elements}>
    <{if 'html' != $element.ele_type}>
        <{if true != $element.hidden}>
            <{if 1 == $element.display_row}>
                <tr>
                <td class="head width33">
                <{if '' == $element.caption}>&nbsp;<{/if}>
                <{if 1 == $element.required}><{$form_req_prefix}><{/if}>
                <{$element.caption}>
                <{if 1 == $element.required}><{$form_req_suffix}><{/if}>
                </td>
                <td class="<{cycle values="even,odd"}> width66"><{$element.body}></td>
                </tr>
            <{else}>
                <tr><td class="head" colspan="2">
                <{if '' == $element.caption}>&nbsp;<{/if}>
                <{if 1 == $element.required}><{$form_req_prefix}><{/if}>
                <{$element.caption}>
                <{if 1 == $element.required}><{$form_req_suffix}><{/if}>
                </td></tr>
                <tr><td class="<{cycle values="even,odd"}>" colspan="2"><{$element.body}></td></tr>
            <{/if}>
        <{/if}>
    <{else}>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><td colspan="2" class="outer center bold pad5"><{$element.body}></td></tr>
    <{/if}>
<{/foreach}>
</table>
<{foreach item=element from=$form_output.elements}>
    <{if true == $element.hidden}><{$element.body}><{/if}>
<{/foreach}>
<{if '' != $form_text_global}><div><{$form_text_global}></div><{/if}>
</form>
<{if '' != $form_edit_link}>
    <div>
    <a href="<{$form_edit_link.location}>" class="floatleft" target="<{$form_edit_link.target}>"><img src="<{$form_edit_link.icon_location}>" alt="<{$form_edit_link.icon_alt}>" title="<{$form_edit_link.icon_title}>"></a>
    </div>
<{/if}>
</div>
