<script>
    document.domain = '{{env('APP_DOMAIN')}}';
    var opener = window.opener;
        console.log( opener);
    if (opener) {
        var oDom = opener.document;

        var event = new MouseEvent('dblclick', {
            'view': opener,
            'bubbles': true,
            'cancelable': true
        });
        event.token = '<?php echo $accessToken; ?>';
        var elem = oDom.getElementById("google_login").dispatchEvent(event);
    }
    window.close();


</script>
