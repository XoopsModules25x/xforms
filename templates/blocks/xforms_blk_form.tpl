<div class="xform_block_form">
<h4 class="center"><{$block.form_output.title}></h4>
<{if '' != $block.form_is_hidden}><p><b><{$block.form_is_hidden}></b></p><{/if}>
<{if '' != $block.form_intro}><p><{$block.form_intro}></p><{/if}>
<{$block.form_output.javascript}>
<form name="<{$block.$form_output.name}>" id="<{$block.form_output.name}>" action="<{$block.form_output.action}>" method="<{$block.form_output.method}>" class="xforms" <{$block.form_output.extra}>>
<table class="outer bspacing1">
<{foreach item=element from=$block.form_output.elements}>
    <{if 'html' != $element.ele_type}>
        <{if true != $element.hidden}>
            <{if 1 == $element.display_row}>
                <tr><td class="head width33">
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
<{foreach item=element from=$block.form_output.elements}>
    <{if true == $element.hidden}><{$element.body}><{/if}>
<{/foreach}>
<{if '' != $block.form_text_global}><div><{$block.form_text_global}></div><{/if}>
</form>
</div>
