<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
</head>
<body>
<div style='margin-left: auto; margin-right: auto;'><h1 style='text-align: center;'><{$smarty.const._AM_XFORMS_REPORT_FORM}>: <{$form_title}></h1></div>
<div style='margin-left: auto; margin-right: auto;'>
    <table style='border-spacing: 1px; margin: 0 auto;'>
        <thead>
        <tr>
            <th style='text-align: center; border-bottom: 2px solid #000000; background: #ACACAC;'><{$smarty.const._AM_XFORMS_NO}></th>
            <th style='text-align: center; border-bottom: 2px solid #000000; background: #ACACAC;'><{$smarty.const._AM_XFORMS_RPT_USER}></th>
            <th style='text-align: center; border-bottom: 2px solid #000000; background: #ACACAC;'><{$smarty.const._AM_XFORMS_RPT_DATETIME}></th>
            <th style='text-align: center; border-bottom: 2px solid #000000; background: #ACACAC;'><{$smarty.const._AM_XFORMS_RPT_IP}></th>
            <th style='text-align: center; border-bottom: 2px solid #000000; background: #ACACAC;'><{$smarty.const._AM_XFORMS_RPT_QUESTION}></th>
            <th style='text-align: center; border-bottom: 2px solid #000000; background: #ACACAC;'><{$smarty.const._AM_XFORMS_RPT_ANSWER}></th>
        </tr>
        </thead>
        <tbody>
        <{foreach item=element from=$elements}>
            <{if '' != $element.ucount}><{cycle assign='bgitem' values='DEDEDE,FFFFFF'}><{/if}>
            <tr>
                <td style='background: #<{$bgitem}>;<{if !empty($element.border)}> <{$element.border}><{/if}> text-align: center;' nowrap><{$element.ucount}></td>
                <td style='background: #<{$bgitem}>;<{if !empty($element.border)}> <{$element.border}><{/if}>' nowrap><{$element.uname}></td>
                <td style='background: #<{$bgitem}>;<{if !empty($element.border)}> <{$element.border}><{/if}>' nowrap><{$element.datet}></td>
                <td style='background: #<{$bgitem}>;<{if !empty($element.border)}> <{$element.border}><{/if}>'><{$element.uip}></td>
                <td style='background: #<{$bgitem}>;<{if !empty($element.border)}> <{$element.border}><{/if}>'><{$element.ele_caption}></td>
                <td style='background: #<{$bgitem}>;<{if !empty($element.border)}> <{$element.border}><{/if}>'><{$element.ele_value}></td>
            </tr>
        <{/foreach}>
        </tbody>
    </table>
</div>
</body>
</html>
