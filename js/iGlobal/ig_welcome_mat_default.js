// iGlobal Welcome Mat Script
// Authored by iGlobal Stores (www.iglobalstores.com)
// Copyright iGlobal Stores 2016

//
// Store specific settings
//

//default settings - these will be overwritten by any settings made in Magento
var ig_storeId = ig_vars.storeId || 3;
var ig_cookieDomain = window.location.hostname;// If you prefer, you can put your domain here, like so "yourdomain.com";
var ig_countries = ig_vars.servicedCountries || {"AL":"Albania","DZ":"Algeria","AS":"American Samoa","AD":"Andorra","AO":"Angola","AI":"Anguilla","AG":"Antigua","AR":"Argentina","AM":"Armenia","AW":"Aruba","AU":"Australia","AT":"Austria","AZ":"Azerbaijan","BS":"Bahamas","BH":"Bahrain","BD":"Bangladesh","BB":"Barbados","BE":"Belgium","BZ":"Belize","BJ":"Benin","BM":"Bermuda","BT":"Bhutan","BO":"Bolivia","BQ":"Bonaire, St. Eustatius & Saba","BA":"Bosnia & Herzegovina","BW":"Botswana","BR":"Brazil","BN":"Brunei","BG":"Bulgaria","BF":"Burkina Faso","BI":"Burundi","KH":"Cambodia","CM":"Cameroon","CA":"Canada","IC":"Canary Islands","CV":"Cape Verde","KY":"Cayman Islands","CF":"Central African Republic","TD":"Chad","CL":"Chile","CN":"China - People's Republic of","CO":"Colombia","KM":"Comoros","CG":"Congo","CK":"Cook Islands","CR":"Costa Rica","HR":"Croatia","CW":"Curaçao","CY":"Cyprus","CZ":"Czech Republic","DK":"Denmark","DJ":"Djibouti","DM":"Dominica","DO":"Dominican Republic","EC":"Ecuador","EG":"Egypt","SV":"El Salvador","GQ":"Equatorial Guinea","ER":"Eritrea","EE":"Estonia","ET":"Ethiopia","FK":"Falkland Islands","FO":"Faroe Islands (Denmark)","FJ":"Fiji","FI":"Finland","FR":"France","GF":"French Guiana","GA":"Gabon","GM":"Gambia","GE":"Georgia","DE":"Germany","GH":"Ghana","GI":"Gibraltar","GR":"Greece","GL":"Greenland (Denmark)","GD":"Grenada","GP":"Guadeloupe","GU":"Guam","GT":"Guatemala","GG":"Guernsey","GN":"Guinea","GW":"Guinea-Bissau","GY":"Guyana","HT":"Haiti","HN":"Honduras","HK":"Hong Kong","HU":"Hungary","IS":"Iceland","IN":"India","ID":"Indonesia","IE":"Ireland - Republic Of","IL":"Israel","IT":"Italy","CI":"Ivory Coast","JM":"Jamaica","JP":"Japan","JE":"Jersey","JO":"Jordan","KZ":"Kazakhstan","KE":"Kenya","KI":"Kiribati","KR":"Korea, Republic of (South Korea)","KW":"Kuwait","KG":"Kyrgyzstan","LA":"Laos","LV":"Latvia","LS":"Lesotho","LR":"Liberia","LI":"Liechtenstein","LT":"Lithuania","LU":"Luxembourg","MO":"Macau","MK":"Macedonia","MG":"Madagascar","MW":"Malawi","MY":"Malaysia","MV":"Maldives","ML":"Mali","MT":"Malta","MH":"Marshall Islands","MQ":"Martinique","MR":"Mauritania","MU":"Mauritius","YT":"Mayotte","MX":"Mexico","FM":"Micronesia - Federated States of","MD":"Moldova","MC":"Monaco","MN":"Mongolia","ME":"Montenegro","MS":"Montserrat","MA":"Morocco","MZ":"Mozambique","MM":"Myanmar","NA":"Namibia","NR":"Nauru, Republic of","NP":"Nepal","NL":"Netherlands (Holland)","NV":"Nevis","NC":"New Caledonia","NZ":"New Zealand","NI":"Nicaragua","NE":"Niger","NG":"Nigeria","NU":"Niue Island","NO":"Norway","OM":"Oman","PK":"Pakistan","PW":"Palau","PA":"Panama","PG":"Papua New Guinea","PY":"Paraguay","PE":"Peru","PH":"Philippines","PL":"Poland","PT":"Portugal","PR":"Puerto Rico","QA":"Qatar","RE":"Reunion","RO":"Romania","RU":"Russia","RW":"Rwanda","SM":"San Marino","ST":"Sao Tome & Principe","SA":"Saudi Arabia","SN":"Senegal","RS":"Serbia & Montenegro","SC":"Seychelles","SL":"Sierra Leone","SG":"Singapore","SK":"Slovakia","SI":"Slovenia","SB":"Solomon Islands","ZA":"South Africa","SS":"South Sudan","ES":"Spain","LK":"Sri Lanka","BL":"St. Barthelemy","EU":"St. Eustatius","KN":"St. Kitts and Nevis","LC":"St. Lucia","MF":"St. Maarten","VC":"St. Vincent","SR":"Suriname","SZ":"Swaziland","SE":"Sweden","CH":"Switzerland","PF":"Tahiti","TW":"Taiwan","TJ":"Tajikistan","TZ":"Tanzania","TH":"Thailand","TL":"Timor-Leste","TG":"Togo","TO":"Tonga","TT":"Trinidad and Tobago","TN":"Tunisia","TR":"Turkey","TM":"Turkmenistan","TC":"Turks and Caicos Islands","TV":"Tuvalu","UG":"Uganda","UA":"Ukraine","AE":"United Arab Emirates","GB":"United Kingdom","US":"United States","UY":"Uruguay","UZ":"Uzbekistan","VU":"Vanuatu","VE":"Venezuela","VN":"Vietnam","VG":"Virgin Islands (British)","VI":"Virgin Islands (U.S.)","WS":"Western Samoa","YE":"Yemen","ZM":"Zambia","ZW":"Zimbabwe"};
var ig_domesticCountryCodes = (ig_vars.domesticCountries || 'US').split(",");
var ig_logoUrl = ig_vars.storeLogo || "http://iglobalstores.com/images/iglobal-stores.png";
var ig_flagLocation = ig_vars.flag_parent || "body";
var ig_flagMethod = ig_vars.flag_method || "prepend";
var ig_flagCode = ig_vars.flag_code || '<div id="igFlag"></div>';
var ig_nestElementId = "igFlag";
//
// Set internal JQuery Variable
// Can set to existing $ on page, or can include Jquery here, and set igJq to jquery-no-conflict
//
igJq = jQuery;

igJq(function(){
	if (ig_flagMethod == "prepend"){
		igJq(ig_flagLocation).prepend(ig_flagCode);
	}
	else if (ig_flagMethod == "append"){
		igJq(ig_flagLocation).append(ig_flagCode);
	}
	else if (ig_flagMethod == "before"){
		igJq(ig_flagLocation).before(ig_flagCode);
	}
	else if (ig_flagMethod == "after"){
		igJq(ig_flagLocation).after(ig_flagCode);
	}
	else {
		igJq(ig_flagLocation).prepend(ig_flagCode);
	}
});

/**
 * jQuery JSONP Core Plugin 2.4.0 (2012-08-21)
 *
 * https://github.com/jaubourg/jquery-jsonp
 *
 * Copyright (c) 2012 Julian Aubourg
 *
 * This document is licensed as free software under the terms of the
 * MIT License: http://www.opensource.org/licenses/mit-license.php
 */
!function(e){function t(){}function n(e){r=[e]}function c(e,t,n){return e&&e.apply(t.context||t,n)}function i(e){return/\?/.test(e)?"&":"?"}function o(o){function m(e){V++||(W(),L&&(I[N]={s:[e]}),A&&(e=A.apply(o,[e])),c(R,o,[e,k,o]),c(z,o,[o,k]))}function F(e){V++||(W(),L&&e!=x&&(I[N]=e),c(U,o,[o,e]),c(z,o,[o,e]))}o=e.extend({},q,o);var S,$,_,J,P,R=o.success,U=o.error,z=o.complete,A=o.dataFilter,G=o.callbackParameter,H=o.callback,K=o.cache,L=o.pageCache,M=o.charset,N=o.url,O=o.data,Q=o.timeout,V=0,W=t;return w&&w(function(e){e.done(R).fail(U),R=e.resolve,U=e.reject}).promise(o),o.abort=function(){!V++&&W()},c(o.beforeSend,o,[o])===!1||V?o:(N=N||l,O=O?"string"==typeof O?O:e.param(O,o.traditional):l,N+=O?i(N)+O:l,G&&(N+=i(N)+encodeURIComponent(G)+"=?"),!K&&!L&&(N+=i(N)+"_"+(new Date).getTime()+"="),N=N.replace(/=\?(&|$)/,"="+H+"$1"),L&&(S=I[N])?S.s?m(S.s[0]):F(S):(C[H]=n,_=e(j)[0],_.id=f+T++,M&&(_[u]=M),B&&B.version()<11.6?(J=e(j)[0]).text="document.getElementById('"+_.id+"')."+h+"()":_[a]=a,D&&(_.htmlFor=_.id,_.event=p),_[y]=_[h]=_[g]=function(e){if(!_[v]||!/i/.test(_[v])){try{_[p]&&_[p]()}catch(t){}e=r,r=0,e?m(e[0]):F(s)}},_.src=N,W=function(e){P&&clearTimeout(P),_[g]=_[y]=_[h]=null,E[b](_),J&&E[b](J)},E[d](_,$=E.firstChild),J&&E[d](J,$),P=Q>0&&setTimeout(function(){F(x)},Q)),o)}var r,a="async",u="charset",l="",s="error",d="insertBefore",f="_jqjsp",m="on",p=m+"click",h=m+s,y=m+"load",g=m+"readystatechange",v="readyState",b="removeChild",j="<script>",k="success",x="timeout",C=window,w=e.Deferred,E=e("head")[0]||document.documentElement,I={},T=0,q={callback:f,url:location.href},B=C.opera,D=!!e("<div>").html("<!--[if IE]><i><![endif]-->").find("i").length;o.setup=function(t){e.extend(q,t)},e.jsonp=o}(igJq);

/**
 * easyModal.js v1.1.0
 * A minimal jQuery modal that works with your CSS.
 * Author: Flavius Matis - http://flaviusmatis.github.com/
 * URL: https://github.com/flaviusmatis/easyModal.js
 */
!function(o){var e={init:function(e){var n={top:"100",autoOpen:!1,overlayOpacity:.7,overlayColor:"#9aa0a3",overlayClose:!0,overlayParent:"body",closeOnEscape:!0,closeButtonClass:".close",onOpen:!1,onClose:!1};return e=o.extend(n,e),this.each(function(){var n=e,t=o('<div class="lean-overlay"></div>');t.css({display:"none",position:"fixed","z-index":2e3,top:0,left:0,height:"100%",width:"100%",background:n.overlayColor,opacity:n.overlayOpacity}).appendTo(n.overlayParent);var a=o(this);a.css({opacity:"0"}),a.bind("openModal",function(){o(this).css({opacity:"1"}),t.fadeIn(200,function(){n.onOpen&&"function"==typeof n.onOpen&&n.onOpen(a[0])})}),a.bind("closeModal",function(){o(this).css("opacity","0"),t.fadeOut(200,function(){n.onClose&&"function"==typeof n.onClose&&n.onClose(a[0])})}),t.click(function(){n.overlayClose&&a.trigger("closeModal")}),o(document).keydown(function(o){n.closeOnEscape&&27==o.keyCode&&a.trigger("closeModal")}),a.on("click",n.closeButtonClass,function(o){a.trigger("closeModal"),o.preventDefault()}),n.autoOpen&&a.trigger("openModal")})}};o.fn.easyModal=function(n){return e[n]?e[n].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof n&&n?void o.error("Method "+n+" does not exist on jQuery.easyModal"):e.init.apply(this,arguments)}}(igJq);

/**
 * Begin embedded jquery cookie plugin, for readying and writing cookies easily
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):e(igJq)}(function(e){function n(e){return e}function o(e){return decodeURIComponent(e.replace(t," "))}function i(e){0===e.indexOf('"')&&(e=e.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{return r.json?JSON.parse(e):e}catch(n){}}var t=/\+/g,r=e.cookie=function(t,c,a){if(void 0!==c){if(a=e.extend({},r.defaults,a),"number"==typeof a.expires){var f=a.expires,u=a.expires=new Date;u.setDate(u.getDate()+f)}return c=r.json?JSON.stringify(c):String(c),document.cookie=[r.raw?t:encodeURIComponent(t),"=",r.raw?c:encodeURIComponent(c),a.expires?"; expires="+a.expires.toUTCString():"",a.path?"; path="+a.path:"",a.domain?"; domain="+a.domain:"",a.secure?"; secure":""].join("")}for(var d=r.raw?n:o,p=document.cookie.split("; "),s=t?void 0:{},m=0,x=p.length;x>m;m++){var g=p[m].split("="),l=d(g.shift()),v=d(g.join("="));if(t&&t===l){s=i(v);break}t||(s[l]=i(v))}return s};r.defaults={},e.removeCookie=function(n,o){return void 0!==e.cookie(n)?(e.cookie(n,"",e.extend({},o,{expires:-1})),!0):!1}});



//
// Begin iGlobal Stores Splash code
//

function ig_getParameterByName(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function ig_createSplashHtml() {
    var ig_splashHtml = '<div id="igSplashElement" class="modal-open" style="opacity: 0;">';
    ig_splashHtml += ig_createSplashContentsHtml();
    ig_splashHtml += '</div><!--/#igSplashElement -->';
    return ig_splashHtml;
}

function ig_createSplashContentsHtml() { // Feel free to edit the HTML below to match your site
    var ig_splashHtml = '' +
        '<div class="igClose">' +
		    '<i class="material-icons">&#xE14C;</i>' +
        '</div>' +
		'<div class="igModalHeader">' +
			'<div class="logoWrapper">' +
	      '<img class="modalLogo" src="'+ig_logoUrl+'" alt="" />' +
			'</div><!--/.logoWrapper -->' +
			'<div class="messageWrapper">' +
				'<p class="headerZero">Hello!</p>' +
				'<p class="countryP">Select your country</p> ' +
				'<div class="countryDropDownWrapper">' +
                    '<img src="https://d1vyngmisxigjx.cloudfront.net/images/flags/96x64/'+((ig_country)?ig_country.toUpperCase():'undefined')+'.png" alt="Flag of '+ig_countries[ig_country]+'" class="headerFlag">' +
                    '<i class="material-icons">&#xE313;</i>' +
                    '<select id="countrySelect" class=".coreUISelect" onchange="ig_countrySelected();"> ' +
						'<option value="">Select your country</option>';

    				for(var countryCode in ig_countries){
						ig_splashHtml += '<option '+((countryCode===ig_country)?'selected="selected" ':'')+'value="'+countryCode+'">'+ig_countries[countryCode]+'</option>';
    				}

    				ig_splashHtml += '' +
					'</select>' +
				'</div><!--/.countryDropDownWrapper -->' +
			'</div><!--/.messageWrapper -->' +
		'</div><!--/.igModalHeader -->' +
		'<div class="igModalBody">';

    if (ig_isDomesticCountry()) {
        //Don't show international shopping features

    } else {
        //Show international shopping features
        ig_splashHtml += ''+
            '<ul class="featureList">' +
                '<div class="igFeatureHeader">We offer the following services to shoppers in ' +ig_countries[ig_country]+'.</div>' +
                '<li><i class="material-icons">&#xE263;</i> See totals and pay in your currency' +
                '</li>' +
                '<li><i class="material-icons">&#xE870;</i> Multiple payment methods available' +
                '</li>' +
                '<li><i class="material-icons">&#xE065;</i> Option to prepay duties and taxes' +
                '</li>' +
                '<li><i class="material-icons">&#xE539;</i> Multiple shipping options available' +
                '</li>' +
            '</ul>';
    }

    ig_splashHtml += '' +
		'<div class="igWelcomeCTAButton">' +
				'<button class="igClose">Start Shopping</button>' +
			'</div><!--/.igWelcomeCTAButton -->' +
		'</div><!--/.igModalBody -->' +
		'<div class="igModalFooter">' +
		'</div><!--/.igModalFooter-->';

    return ig_splashHtml;
}

function ig_countrySelected() {
    var countryCode = igJq("select#countrySelect").val();
    ig_setCountry(ig_validateCountryCode(countryCode));
    igJq("#igSplashElement").html(ig_createSplashContentsHtml());
    ig_alertIceOfCountryChange();
}

//Called by auto popup logic for first time non domestic country customers.  Also called by ALL customers clicking the nested flag on the page
function ig_showTheSplash() {
    //Construct the modal
    igJq("body").append(ig_createSplashHtml());

    //init easyModal.js modal, after modal content was placed on the page (line above)
    igJq("#igSplashElement").easyModal({
        closeButtonClass: '.igClose',
        onClose: function(myModal){
            //on close, let's remove the modal contents and the modal smokescreen created by easyModal.js
            igJq("#igSplashElement").remove();
            igJq(".lean-overlay").remove();
            igJq("body").removeClass("welcome-mat-blur welcome-mat-open");
            igJq("#igSplashElement").removeClass("modal-open");
        }
    });

    //Fire the modal!
    igJq("#igSplashElement").trigger('openModal');
    igJq("body").addClass("welcome-mat-blur welcome-mat-open");
    igJq("#igSplashElement").addClass("modal-open");

    //Set cookie for Splash shown
    if (ig_validateCountryCode(igJq.cookie("igCountry"))) { // Only set the splashShown cookie, if there is a valid countryCookie
        igJq.cookie('igSplash', 'igSplash', { expires: 7, path: '/', domain: ig_cookieDomain });
    }
}
function ig_createNestContents() {
    return '<img onclick="ig_showTheSplash();" src="https://d1vyngmisxigjx.cloudfront.net/images/flags/96x64/'+((ig_country)?ig_country.toUpperCase():'undefined')+'.png" class="igWelcomeFlagHeader" alt="Select your country." />';
}

function ig_placeNestHtml() {
    igJq(function(){
        if (igJq("#"+ig_nestElementId)) {
            igJq("#"+ig_nestElementId).html(ig_createNestContents());
        }
    });
}

function ig_setCountry(country) {
    ig_country = country;
    if (ig_country) {
        //Set country cookie
        igJq.cookie('igCountry', ig_country, { expires: 365, path: '/', domain: ig_cookieDomain });
    }
    ig_placeNestHtml();
}

function ig_alertIceOfCountryChange() {
    try {
	 ig_ice_countryChanged(); // let the ICE script know that the country has changed, if there is an ICE script
    } catch (err) {
	 //do nothing
    }
}

function ig_validateCountryCode(countryCode) {
    //Return the country code if valid, return null if not valid
    var countryDisplayName = ig_countries[countryCode];
    if (typeof countryDisplayName !== 'undefined' && countryDisplayName) {
        return countryCode;
    } else {
        return null;
    }
}

function ig_isDomesticCountry() {
    return ig_country && igJq.inArray(ig_country, ig_domesticCountryCodes) >= 0;
}

function ig_detectCountryCallback(countryCode) {
    ig_setCountry(ig_validateCountryCode(countryCode));
    ig_finishLoading();
}

function ig_detectCountryCallbackError() { // Error handling method for when the jsonp call to get the countryCode fails, if it will get called?
    console.log("Couldn't detect country");
    ig_finishLoading();
}

function ig_detectCountry() {
    igJq.jsonp({
        url: 'https://iprecon.iglobalstores.com/iGlobalIp.js?p=igcCallback',
        callback:'igcCallback',
        success: function(json, textStatus, xOptions){ig_detectCountryCallback(json);},
        error: function(){ig_detectCountryCallbackError();}
    });
}

function ig_pingIglobal() {
  try { // Don't break if this doesn't work
    if (!ig_countryParam) {//Only ping iGlobal for real visitors, not url parameter testing
      igJq.ajax({//we do not need to trap errors like 503's, for this call
        dataType: "jsonp",
        url: 'https://iprecon.iglobalstores.com/ping.js?s='+ig_storeId+'&c='+((ig_country)?ig_country:'')
      });
    }
  } catch (err) {
    // do nothing
  }
}

function ig_finishLoading() {
    ig_placeNestHtml();
    if (!ig_isDomesticCountry() && (!ig_splashCookie || !ig_country || ig_countryParam)) {
        igJq(ig_showTheSplash); //Schedule Showing the Splash
    }
    ig_pingIglobal();
}

var ig_country = null;
var ig_countryCookie = ig_validateCountryCode(igJq.cookie("igCountry"));
var ig_countryParam = ig_validateCountryCode(ig_getParameterByName("igCountry"));
var ig_splashCookie = igJq.cookie("igSplash");

//set country to URL parameter igCountry
if (!ig_country && ig_countryParam) {
    ig_country = ig_countryParam;
}

//else set country to countryCookie
if (!ig_country && ig_countryCookie) {
    ig_country = ig_countryCookie;
}

//else set country to countryIP from iGlobal's IP Recognition Service
if (!ig_country) {
    ig_detectCountry();
} else { // else go with whatever country we have, even no country
    ig_finishLoading();
}

// Redirect to int'l checkout if country is changed on domestic page
function ig_countryRedirect(selectedCountry){
  if(ig_domesticCountryCodes.indexOf(selectedCountry) == -1){
    ig_setCountry(selectedCountry);
    window.location.pathname = window.location.pathname.replace("/checkout/onepage/", "/iglobal/checkout/");
  }
}

// check for change in shipping country on domestic checkout page.
igJq(document).ready(function(){
	billing_select = igJq("select[id *= 'billing:country_id']");
	shipping_select = igJq("select[id *= 'shipping:country_id']");
	rdo_same_as_billing = igJq("input[id *= 'billing:use_for_shipping_yes']");

	// check for redirect if country selected and billing is same as shipping
	igJq("select[id *= 'billing:country_id']").change(function(){
		if(igJq(this).val() && rdo_same_as_billing.checked === true){
			ig_countryRedirect(igJq(this).val());
		}
	});

	//change in shipping country
	igJq("select[id *= 'shipping:country_id']").change(function(){
		if(igJq(this).val()){
			ig_countryRedirect(igJq(this).val());
		}
	});

  // same as shipping button is checked
	igJq(rdo_same_as_billing).change(function(){
		if(this.checked === true && billing_select.val()){
		  ig_countryRedirect(billing_select.val());
		}
	});
});
