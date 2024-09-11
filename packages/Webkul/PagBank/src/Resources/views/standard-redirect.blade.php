<?php $pagbankStandard = app('Webkul\PagBank\Payment\Standard') ?>

<body data-gr-c-s-loaded="true" cz-shortcut-listen="true">
    You will be redirected to the PagBank website in a few seconds.


    <form action="{{ $pagbankStandard->getPagBankUrl() }}" id="pagbank_standard_checkout" method="POST">
        <input value="Click here if you are not redirected within 10 seconds..." type="submit">

        @foreach ($pagbankStandard->getFormFields() as $name => $value)

            <input
                type="hidden"
                name="{{ $name }}"
                value="{{ $value }}"
            />

        @endforeach
    </form>

    <script type="text/javascript">
        document.getElementById("pagbank_standard_checkout").submit();
    </script>
</body>
