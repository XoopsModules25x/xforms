<div id="xforms">
    <h1 class="center"><{$default_title}></h1>
    <{if $forms}>
        <p><{$forms_intro}></p>
        <{ foreach item=form from=$forms}>
        <div>
            <h4><a href="index.php?form_id=<{$form.id}>"><{$form.title}></a>
                <a href="<{$form.form_edit_link.location}>" class="middle inline" style="padding-left: 1em;"
                   target="<{$form.form_edit_link.target}>"><img src="<{$form.form_edit_link.icon_location}>"
                                                                 alt="<{$form.form_edit_link.icon_alt}>"
                                                                 title="<{$form.form_edit_link.icon_title}>"></a></h4>
        </div>
        <p><{$form.desc}></p>
        <br>
        <{  /foreach}>
    <{else}>
        <h4 class="center"><{$noform}></h4>
    <{/if}>
</div>
