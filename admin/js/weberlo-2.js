jQuery(function($)
{
    $('input[name="weberlo_for_wc"]').on( 'change', function()
    {
        if( this.checked )
        {
            $('.weberlo__field.weberlo_api_key').show();
            $('input[name="weberlo_api_key"]').prop( 'required', true );
        }
        else
        {
            $('.weberlo__field.weberlo_api_key').hide();
            $('input[name="weberlo_api_key"]').prop( 'required', false );
        }
    });
})