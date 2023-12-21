jQuery(document).ready(function ($) {

    var $wdpTable           = $('.wdp_table ');
    var $dynamicPricing     = awdajaxobject.dynamicPricing;
    var $variablePricing    = awdajaxobject.variablePricing;

    // if ( typeof wp.hooks !== 'undefined' ) {
    //     wp.hooks.addFilter('wcpa_product_price', 'wcpa', (productPrice, product_id, variation_id) => {
    //         $.ajax({ url: awdajaxobject.url, data: {action: 'wdpDiscountedPrice', prodID: product_id, varID: variation_id} }).done(function(data) {
    //             productPrice = data ? data : productPrice;
    //             return productPrice;
    //         }).fail(function(data){
    //             return productPrice;
    //         });
    //     }, 10);
    //     document.dispatchEvent(new Event("wcpaTrigger", {bubbles: true}));
    //     $( '.variations_form' ).on( 'woocommerce_variation_select_change', function() {
    //         wp.hooks.addFilter('wcpa_product_price', 'wcpa', (productPrice, product_id, variation_id) => {
    //             $.ajax({ url: awdajaxobject.url, data: {action: 'wdpDiscountedPrice', prodID: product_id, varID: variation_id} }).done(function(data) {
    //                 productPrice = data ? data : productPrice;
    //                 return productPrice;
    //             }).fail(function(data){
    //                 return productPrice;
    //             });
    //         }, 10);
    //         document.dispatchEvent(new Event("wcpaTrigger", {bubbles: true}));
    //     });
    //     $('form.cart').find('[name=quantity]').on('change input', function(){ 
    //         let qn = $(this).parents('form.cart').find('input[name="quantity"]').val();
    //         wp.hooks.addFilter('wcpa_product_price', 'wcpa', (productPrice, product_id, variation_id) => {
    //             $.ajax({ url: awdajaxobject.url, data: {action: 'wdpDiscountedPrice', prodID: product_id, varID: variation_id} }).done(function(data) {
    //                 productPrice = data ? qn * data : qn * productPrice;
    //                 return productPrice;
    //             }).fail(function(data){
    //                 return qn * productPrice;
    //             });
    //         }, 10);
    //         document.dispatchEvent(new Event("wcpaTrigger", {bubbles: true}));
    //     });
    // }

    if ( $wdpTable.length > 0 ) {

        if ( $variablePricing ) {
            $( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
                let attributes = variation.attributes;
                let price = variation.display_price;
                let price_html = variation.price_html;
                let variation_id = variation.variation_id;
                let loader = '<div class="wdpLoader"><span></span><span></span><span></span></div>';
                $wdpTable.find('tbody td').html(loader);
                $('.wdpHiddenPrice').html(price_html);
                let varPrice = $('.wdpHiddenPrice del').length ? $('.wdpHiddenPrice ins .amount').text() : $('.wdpHiddenPrice .amount').text();
                varPrice = varPrice ? varPrice.replace(/[^\d\.]/g, '') : price;
                let data = {
                    'action': 'wdpAjax', 
                    'nonce': awdajaxobject.nonce, 
                    'type': 'change',
                    'attributes': attributes,
                    'price': varPrice,
                    'variation_id': variation_id,
                    'DisData': $wdpTable.attr('data-table'),
                    'Rule': $wdpTable.attr('data-rule'),
                }
                $.post(awdajaxobject.url, data, function(response) { 
                    if ( response ) { 
                        $wdpTable.attr('data-price', varPrice);
                        $wdpTable.find('tbody').html(response); 
                    }
                });
            });
        }

        if ( $dynamicPricing ) {
            let loader = '<div class="wdpLoader"><span></span><span></span><span></span></div>';
            $('form.cart').find('[name=quantity]').on('change input', function(){ 
                if ( $wdpTable.attr('data-product') === '' ) { return; } 
                let data = { 
                    'action': 'wdpAjax', 
                    'nonce': awdajaxobject.nonce, 
                    'type': 'update',
                    'ProdID': $wdpTable.attr('data-product'), 
                    'DisData': $wdpTable.attr('data-table'), 
                    'ProdPrice': $wdpTable.attr('data-price'), 
                    'ProdVarPrice': $wdpTable.attr('data-var-price'), 
                    'ProdQty': $(this).parents('form.cart').find('input[name="quantity"]').val() 
                };
                $(".wdpDynamicValue .wdpPrice").html(loader);
                $(".wdpDynamicValue .wdpTotal").html(loader);
                $.post(awdajaxobject.url, data, function(response) { 
                    if ( response ) { 
                        response = JSON.parse(response); 
                        // $(".wdpDynamicValue .wdpPrice").removeClass('wdpLoader');
                        // $(".wdpDynamicValue .wdpTotal").removeClass('wdpLoader');
                        $(".wdpDynamicValue .wdpPrice").html(response.currency + response.price); 
                        $(".wdpDynamicValue .wdpTotal").html(response.currency + response.total); 
                        $(".wdpDynamicValue").show();
                    }
                });
            });
        }
        
    }
  
});  