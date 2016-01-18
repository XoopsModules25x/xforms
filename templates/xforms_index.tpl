<div id="xforms">
    <h1 style="text-align: center;"><{$default_title}></h1>
    <{if $forms}>
        <p><{$forms_intro}></p>
        <{foreach item=form from=$forms}>
            <h4><a href="index.php?form_id=<{$form.id}>"><{$form.title}></a></h4>
            <p><{$form.desc}></p>
            <br/>
        <{/foreach}>
    <{else}>
        <h4 style="text-align: center;"><{$noform}></h4>
    <{/if}>
</div>
