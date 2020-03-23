<{if !empty($block)}>
    <div class="xforms-blk-list">
        <ul>
            <{foreach from=$block key=id item=txt}>
                <li>
                    <a href="<{$xoops_url}>/modules/xforms/index.php?form_id=<{$id}>" class="tooltip" title="<{$txt.desc}>"><{$txt.title}></a>
                </li>
            <{/foreach}>
        </ul>
    </div>
<{/if}>
