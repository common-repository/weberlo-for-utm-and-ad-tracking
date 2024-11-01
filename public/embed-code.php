<script>
(function(d, s) {
    var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s);
    j.async = true;
    j.src = '<?php echo $embed_url; ?>';
    f.parentNode.insertBefore(j, f);
})(document, 'script');

var id = setInterval( WeberloSaveVisitorId, 3e3 );

function WeberloSaveVisitorId() {
    if( (document.cookie.match(/^(?:.*;)?\s*__wbr_uid\s*=\s*([^;]+)(?:.*)?$/)||[,null])[1] )
        clearInterval(id);
    if( window.dataLayer ) {
        window.dataLayer.forEach( function(ele) {
            if( ele.w_session_id ) { 
                document.cookie = `__wbr_uid=${ele.w_session_id}`;
                clearInterval(id);
            }
        });
    }
}
</script>