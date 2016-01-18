<div id="xforms">
    <h1 style="text-align: center;"><{$form_output.title}></h1>

    <{if $form_is_hidden != ""}><p><b><{$form_is_hidden}></b></p><{/if}>
    <{if $form_intro != ""}><p><{$form_intro}></p><{/if}>

    <{$form_output.javascript}>
    <form name="<{$form_output.name}>" id="<{$form_output.name}>" action="<{$form_output.action}>" method="<{$form_output.method}>" class="xforms" <{$form_output.extra}>>
        <table cellspacing="1" class="outer">
            <tr>
                <th colspan="2"><{$form_output.title}></th>
            </tr>
            <{foreach item=element from=$form_output.elements}>
                <{if $element.ele_type != "html"}>
                    <{if $element.hidden != true}>
                        <{if $element.display_row == 1}>
                            <tr>
                                <td class="head" width="35%"><{if $element.caption == "" }>&nbsp;<{/if}><{if $element.required == 1}><{$form_req_prefix}><{/if}>
                                    <{$element.caption}>
                                    <{if $element.required == 1}><{$form_req_suffix}><{/if}>
                                </td>
                                <td class="<{cycle values="even,odd"}>" width="65%"><{$element.body}></td>
                            </tr>
                        <{else}>
                            <tr>
                                <td class="head" colspan="2"><{if $element.caption == "" }>&nbsp;<{/if}><{if $element.required == 1}><{$form_req_prefix}><{/if}>
                                    <{$element.caption}>
                                    <{if $element.required == 1}><{$form_req_suffix}><{/if}>
                                </td>
                            </tr>
                            <tr>
                                <td class="<{cycle values="even,odd"}>" colspan="2"><{$element.body}></td>
                            </tr>
                        <{/if}>
                    <{/if}>
                <{else}>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="outer" style="text-align: center; padding: 5px; font-weight: bold;"><{$element.body}></td>
                    </tr>
                <{/if}>
            <{/foreach}>
        </table>
        <{foreach item=element from=$form_output.elements}>
            <{if $element.hidden == true}>
                <{$element.body}>
            <{/if}>
        <{/foreach}>
        <{if $form_text_global != ""}>
            <div><{$form_text_global}></div><{/if}>
    </form>
</div>
