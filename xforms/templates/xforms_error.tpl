<div id="xforms">
    <h4><{$error_heading}></h4>

    <div class="errorMsg">
        <{section name=err loop=$errors}>
            <{$errors[err]}>
            <br/>
        <{/section}>
        <a href="<{$xforms_url}>" onclick="ReturnToForm();"><{$go_back}></a>
    </div>

    <script type="text/javascript">
        function ReturnToForm() {
            history.go(-1);
        }
        window.setTimeout("ReturnToForm()", 10000);
    </script>
</div>
