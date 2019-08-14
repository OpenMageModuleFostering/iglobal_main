$igc = jQuery;

// These are the Key variables used in customizing this script.
// By changing these, many Magento themes will operate correctly with iGlobal technology.
//  Some themes require further customization by defining the item details below.
var ig_storeId = ig_vars.storeId || 3;
var igSubdomain = ig_vars.subdomain || "checkout";
var igCartUrl = ig_vars.cartUrl || "/checkout/cart";

function igcCheckout() {

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
  var itemRows = $igc(".igItemDetails");//products rows

  $igc(itemRows).each(function () {
    var qty = $igc(this).find('.igQty').text();
    var price = $igc(this).find('.igPrice').text();
    var imgURL = $igc(this).find('.igImage').text();
    var itemURL = $igc(this).find('.igUrl').text();
    var descTxt = '<span class="itemDescription">' + $igc(this).find('.igName').text() + '</span>';// + $igc(this).find('.igItemOptions').html();
    var sku = $igc(this).find('.igSku').text();
    var pid = $igc(this).find('.igID').text();
    if ($igc(this).find('.ig_itemWeight').text()) {
      var weight = $igc(this).find('.ig_itemWeight').text();
    } else {
      var weight = $igc(this).find('.MageWeight').text();
    }
    var weight = $igc(this).find('.ig_itemWeight').text();
    var length = $igc(this).find('.ig_itemLength').text();
    var width = $igc(this).find('.ig_itemWidth').text();
    var height = $igc(this).find('.ig_itemHeight').text();


    if (qty) {
      items.push({
        "itemDescription": $igc.trim(descTxt),
        "itemQuantity": $igc.trim(qty),
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

function ig_domesticActions() {
  //show the shipping estimate for domestic visitors
  $igc(".shipping").show();
  $igc('a[title="Checkout with Multiple Addresses"]').show();
}


function ig_internationalActions() {
  //hide the shipping estimate for int'l visitors
  $igc(".shipping").hide();
  $igc('a[title="Checkout with Multiple Addresses"]').hide();
}

//for when the country is changed
function ig_ice_countryChanged() {
  if (window.location.href.indexOf("cart") != -1) {
    if (!ig_isDomesticCountry()) {
      ig_internationalActions();
    } else {
      ig_domesticActions();
    }
  }
}


$igc(document).ready(function () {
  //button logic
  if (!$igc("#welcome_mat_deactivated").length) { //welcome mat active, take button or set country
    if (ig_country) {
      ig_recordOnClick();
      if (!ig_isDomesticCountry()) {
        ig_internationalActions();
      }
    }
  } else {
    //add additional button b/c no welcome mat
    var igButton = $igc('<br /><img>').attr("src", "https://checkout.iglobalstores.com/images/iglobal-button2.png").attr("class", "igButton").css({cursor: "pointer"});
    $igc(".totals .checkout-types").append($igc("<li>").append(igButton));
    $igc(igButton).click(function () {
      igcCheckout();
    });
  }
});
