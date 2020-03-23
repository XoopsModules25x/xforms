"<{$smarty.const._AM_XFORMS_NO}>"<{$delim}>"<{$smarty.const._AM_XFORMS_RPT_USER}>"<{$delim}>"<{$smarty.const._AM_XFORMS_RPT_DATETIME}>"<{$delim}>"<{$smarty.const._AM_XFORMS_RPT_IP}>"<{$delim}>"<{$smarty.const._AM_XFORMS_RPT_QUESTION}>"<{$delim}>"<{$smarty.const._AM_XFORMS_RPT_ANSWER}>"
<{foreach item=element from=$elements}>
    <{if '' != $element.ucount}>"<{$element.ucount}>"<{else}>""<{/if}><{$delim}>"<{$element.uname}>"<{$delim}>"<{$element.datet}>"<{$delim}>"<{$element.uip}>"<{$delim}>"<{$element.ele_caption}>"<{$delim}>"<{$element.ele_value}>"
<{/foreach}>
