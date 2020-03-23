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
            <th style='text-align: center; border-bottom: 2px solid #000000; background: #ACACAC;'><{$smarty.const._AM_XFORMS_RPT_USER}></th>
            <th style='text-align: center; border-bottom: 2px solid #000000; background: #ACACAC;'><{$smarty.const._AM_XFORMS_RPT_DATETIME}></th>
            <th style='text-align: center; border-bottom: 2px solid #000000; background: #ACACAC;'><{$smarty.const._AM_XFORMS_RPT_IP}></th>
            <{foreach item=caption from=$captions}>
                <th style='text-align: center; border-bottom: 2px solid #000000; background: #ACACAC;'><{$caption}></th>
            <{/foreach}>
        </tr>
        </thead>
        <tbody>
        <!-- start of report item display -->
        <{foreach item=rptLine from=$rptArray}>
            <{cycle assign='bgitem' values='DEDEDE,FFFFFF'}>
            <tr>
                <td style='background: #<{$bgitem}>;' nowrap><{$rptLine.user}></td>
                <td style='background: #<{$bgitem}>;' nowrap><{$rptLine.time}></td>
                <td style='background: #<{$bgitem}>;' nowrap><{$rptLine.ip}></td>
                <{ foreach item=element from=$rptLine.elements}>
                <td style='background: #<{$bgitem}>; text-align: center;'><{$element}></td>
                <{ /foreach}>
            </tr>
        <{/foreach}>
        </tbody>
    </table>
</div>
</body>
</html>
