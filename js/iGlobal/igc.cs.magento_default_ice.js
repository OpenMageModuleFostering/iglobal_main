$igc =jQuery


//These are the Key variables used in customizing this script.  By changing these, many Magento themes will operate correctly with iGlobal technology.  Some themes require further customization by defining the item details below.
	//identify the store
	var igStoreId = "3"; //this can be set by updating the "iGlobal Store ID Number" in Magento conficuration options
	var igSubdomain = "checkout"; //this can be set by updating the "iGlobal Hosted Checkout Subdomain" in Magento conficuration options
	var igCartUrl = "/magento/index.php/checkout/cart";
	var ajaxPath = window.location.href.replace(window.location.origin, '')
	ajaxPath = ajaxPath.substring(0, ajaxPath.indexOf("/",1)) + '/iglobal/ajax/icedata'; //parses the uri to figure out how to get to the right controller
	//ajaxPath = ajaxPath.substring(0, ajaxPath.indexOf("index.php")+9) + '/iglobal/ajax/icedata'; //parses the url to figure out how to get to the right controller


$igc.post (
	ajaxPath,
	//'/magento/iglobal/ajax/icedata',
	function(data){
	
		//console.log(data);
		var result = eval("(" + data + ")");
		
		if (result.storeId){
			 // store ID is set
			 igStoreId = result.storeId;
		}
		
		if (result.subdomain){
			 // subdomain is set
			igSubdomain = result.subdomain;
		}
		
		if (result.cartUrl){
			 // subdomain is set
			igCartUrl = result.cartUrl;
		}
	
	}
);
//}

function igcCheckout() {
//TODO: reset to store 3
    igcGoToCheckout(igStoreId);
}

function getSelectedCountry() { 
	return ig_country; 
}

function getSubDomain() {
    return igSubdomain;
} 

function igcGetItems() {

    var items = new Array();
    var itemRows = $igc("#shopping-cart-table > tbody > tr");//products rows	
	
    $igc(itemRows).each(function() {
		var tds = $igc(this).find('td');//different columns of each row
        var qty    = $igc(tds).find('.qty').val();
	   var price   = parseFloat($igc(tds).find(".ig_itemPrice").text().replace('$','')); //price is passed in USD as itemProductPrice[]
        var imgURL   = $igc(tds).eq(0).find('img').attr("src");
        var itemURL   = $igc(tds).eq(0).find('a').attr("href");
        var descTxt  = $igc(tds).eq(1).find('.product-name').text();
			if($igc(tds).eq(1).find('.item-options').length>0){//if this product has options
				$igc(tds).eq(1).find(".item-options dt").each(function(){
				descTxt  += "<br />"+$igc(this).text()+" : "+$igc(this).next().text()
				});
			}
		descTxt  += "<br />Size : "+$igc(tds).eq(2).text();
		var sku = $igc(tds).find(".ig_itemSku").text();
		var pid = $igc(tds).find(".ig_itemProductId").text();
		var weight = $igc(tds).find(".ig_itemWeight").text();
		var length = $igc(tds).find(".ig_itemLength").text();;
		var width = $igc(tds).find(".ig_itemWidth").text();
		var height = $igc(tds).find(".ig_itemHeight").text();
        
		
		if(qty){
          items.push({
              "itemDescription":$igc.trim(descTxt),
              "itemQuantity":$igc.trim(qty),
              "itemUnitPrice": $igc.trim(price),
              "itemURL": itemURL,
              "itemImageURL": imgURL,            
              "itemSku": sku,             
              "itemProductId": pid,
              "itemWeight": weight,
              "itemLength": length,
              "itemWidth": width,
              "itemHeight": height
          });		 
        }		
    });
    return items;
}

var oldButton = "";
var oldOnClick = "";

function ig_recordOnClick () {
	//record click actions
	oldButton = $igc(':button[title="Proceed to Checkout"]'); //this is the jQuery selector for your checkout button
	oldOnClick = oldButton.attr('onclick'); //this is the attribute or click function that moves from the cart to checkout. defining this lets us move international customers to your iGlobal hosted checkout automatically.
	//console.log(oldOnClick);
}

//domestic configuration

	function ig_domesticActions () {
		$igc(oldButton).off(); // remove event handler if it was added by ig_internationalActions()
		//replace old onclick attr
		$igc(oldButton).attr('onclick',oldOnClick);

		//hide the shipping estimate and discount codes for int'l visitors 
		$igc(".shipping").show();
		$igc(".discount").show();
	}


//international configuration

	function ig_internationalActions () {

		//take over button
		$igc(oldButton).attr('onclick','');
		$igc(oldButton).click(function(){
			igcCheckout();
		});	
		//hide the shipping estimate and discount codes for int'l visitors 
		$igc(".shipping").hide();
		$igc(".discount").hide();
		
	}

//for when the country is changed
	function ig_ice_countryChanged() {

		if (window.location.href.indexOf("cart") != -1) {
			
			if ( !ig_isDomesticCountry() ){

				ig_internationalActions ();	

			}  else {
			
				ig_domesticActions ();
			
			}		
		}
		
		if ((window.location.href.indexOf("checkout") != -1)  && (window.location.href.indexOf("cart") == -1)) {
			
			if ( !ig_isDomesticCountry() ){

				alert('You are using the domestic checkout for an international order.  Please return to your cart and checkout again.');
				window.location.replace(igCartUrl);

			} 	
		}
	}


$igc(document).ready(function(){


	if ((window.location.href.indexOf("checkout") != -1)  && (window.location.href.indexOf("cart") == -1)) {
		
		if ( !ig_isDomesticCountry() ){

			alert('You are using the domestic checkout for an international order.  Please return to your cart and checkout again.');
			window.location.replace(igCartUrl);

		} 	
	}

	//button logic
	if(!$igc("#welcome_mat_deactivated").length){ //welcome mat active, take button or set country
			if(ig_country){
			
				ig_recordOnClick();
				
				if(!ig_isDomesticCountry()){
				
						
					//hide the shipping estimate and discount codes for int'l visitors 
					//$igc(".shipping").hide();
					//$igc(".discount").hide();
		

					ig_internationalActions ();	
					
				} 
			} else {
				alert("Please select your country from the list, and click the Checkout button again.");
				ig_showTheSplash();
				return false;			
			}

	} else {
		//add additional button b/c no welcome mat
		var igButton = $igc('<br /><img>').attr("src","https://checkout.iglobalstores.com/images/iglobal-button2.png").attr("class","igButton").css({cursor:"pointer"}); 		

		$igc(".totals .checkout-types").append($igc("<li>").append(igButton)); 
		
		$igc(igButton).click(function() {
			igcCheckout(); 
		});
		
	}
});
