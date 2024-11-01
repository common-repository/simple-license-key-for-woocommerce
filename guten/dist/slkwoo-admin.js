!function(){"use strict";var e={n:function(t){var a=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(a,{a:a}),a},d:function(t,a){for(var l in a)e.o(a,l)&&!e.o(t,l)&&Object.defineProperty(t,l,{enumerable:!0,get:a[l]})},o:function(e,t){return Object.prototype.hasOwnProperty.call(e,t)}},t=window.wp.element,a=window.wp.apiFetch,l=e.n(a),n=window.wp.components,r=()=>(0,t.createElement)("details",null,(0,t.createElement)("summary",null,(0,t.createElement)("strong",null,credit.links)),(0,t.createElement)("span",{className:"span1Style"},(0,t.createElement)("div",null,credit.plugin_version," |",(0,t.createElement)("a",{className:"aStyle",href:credit.faq,target:"_blank",rel:"noopener noreferrer"},"FAQ")," | ",(0,t.createElement)("a",{className:"aStyle",href:credit.support,target:"_blank",rel:"noopener noreferrer"},"Support Forums")," | ",(0,t.createElement)("a",{className:"aStyle",href:credit.review,target:"_blank",rel:"noopener noreferrer"},"Reviews")),(0,t.createElement)("div",null,(0,t.createElement)("a",{className:"aStyle",href:credit.translate,target:"_blank",rel:"noopener noreferrer"},credit.translate_text)," | ",(0,t.createElement)("a",{className:"aStyle",href:credit.facebook,target:"_blank",rel:"noopener noreferrer"},(0,t.createElement)("span",{class:"dashicons dashicons-facebook"}))," | ",(0,t.createElement)("a",{className:"aStyle",href:credit.twitter,target:"_blank",rel:"noopener noreferrer"},(0,t.createElement)("span",{class:"dashicons dashicons-twitter"}))," | ",(0,t.createElement)("a",{className:"aStyle",href:credit.youtube,target:"_blank",rel:"noopener noreferrer"},(0,t.createElement)("span",{class:"dashicons dashicons-video-alt3"}))),(0,t.createElement)("div",{className:"boxStyle"},(0,t.createElement)("h3",null,credit.donate_text),(0,t.createElement)("div",{className:"divStyle"},(0,t.createElement)("span",{className:"span2Style"},"Plugin Author")," ",(0,t.createElement)("span",{className:"span1Style"},"Katsushi Kawamori")),(0,t.createElement)(n.Button,{className:"button button-large",href:credit.donate,target:"_blank"},credit.donate_button))));(0,t.render)((0,t.createElement)((()=>{const[e,a]=(0,t.useState)(slkwoo_data.slkwoo_passphrase),[o,s]=(0,t.useState)(Boolean(slkwoo_data.slkwoo_passphrase_lock)),c=JSON.parse(slkwoo_data.slkwoo_logs),[m,d]=(0,t.useState)(c);(0,t.useEffect)((()=>{l()({path:"rf/slkwoo-admin_api/token",method:"POST",data:{text:e,lock:o,logs:m}}).then((e=>{}))}),[e,o,m]);const i=[];Object.keys(c).map((e=>{if(c.hasOwnProperty(e)){let a=[],l=[];Object.keys(c[e]).map((r=>{"date_expiry"==r?l.push((0,t.createElement)("td",null,(0,t.createElement)(n.PanelBody,{title:m[e][r],icon:"",initialOpen:!1,className:"date_expiry_color"},(0,t.createElement)(n.PanelRow,null,(0,t.createElement)(n.DateTimePicker,{currentDate:m[e][r],onChange:t=>{let a=t.replace("T"," ");m[e][r]=a;let l=Object.assign({},m);d(l)}}))))):"expiry_stamp"!==r&&a.push((0,t.createElement)("td",null,c[e][r]))})),i.push((0,t.createElement)("tr",null,a,l))}}));const p=[];return o?p.push((0,t.createElement)(t.Fragment,null,(0,t.createElement)("span",{className:"pass_text"},e),"    ")):p.push((0,t.createElement)(t.Fragment,null,(0,t.createElement)(n.TextControl,{value:e,onChange:e=>a(e)}),"    ",(0,t.createElement)(n.Button,{className:"button button-large",onClick:()=>{let e="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!#$%&=~/*-+",t="";for(var l=0;l<12;l++)t+=e.charAt(Math.floor(Math.random()*e.length));a(t)}},slkwoo_data.generate),"    ")),p.push((0,t.createElement)(n.ToggleControl,{label:slkwoo_data.lock,help:o?slkwoo_data.lock_text:slkwoo_data.unlock_text,checked:o,onChange:()=>{s((e=>!e))}})),(0,t.createElement)("div",{className:"wrap"},(0,t.createElement)("h2",null,"Simple License Key for WooCommerce"),(0,t.createElement)(r,null),(0,t.createElement)("div",{className:"wrap"},(0,t.createElement)("hr",null),(0,t.createElement)("h3",null,slkwoo_data.passphrase_text),(0,t.createElement)("div",{className:"pass_button_line"},p),(0,t.createElement)("hr",null),(0,t.createElement)("h3",null,slkwoo_data.decrypt_text),(0,t.createElement)("p",{className:"description"},slkwoo_data.decrypt_description),(0,t.createElement)("div",null,"PHP : ",(0,t.createElement)("code",null,"openssl_decrypt( $encrypt_data, 'aes-256-cfb', "),(0,t.createElement)("span",{className:"pass_code_text"},"'"+e+"'"),(0,t.createElement)("code",null,", 0, openssl_cipher_iv_length( 'aes-256-cfb' ) );")),(0,t.createElement)("hr",null),(0,t.createElement)("h3",null,"REST API URL"),(0,t.createElement)("p",{className:"description"},slkwoo_data.rest_api_description),(0,t.createElement)("div",null,(0,t.createElement)("a",{href:slkwoo_data.apiurl,target:"_blank",rel:"noopener noreferrer"},slkwoo_data.apiurl)),(0,t.createElement)("hr",null),(0,t.createElement)("h3",null,slkwoo_data.logs),(0,t.createElement)("p",{className:"description"},slkwoo_data.logs_description),(0,t.createElement)("table",{border:"1",cellspacing:"0",cellpadding:"5",bordercolor:"#000000",className:"tableStyle"},(0,t.createElement)("tr",null,(0,t.createElement)("td",null,slkwoo_data.product_id),(0,t.createElement)("td",null,slkwoo_data.product_name),(0,t.createElement)("td",null,slkwoo_data.passphrase),(0,t.createElement)("td",null,slkwoo_data.encrypt_data),(0,t.createElement)("td",null,slkwoo_data.name),(0,t.createElement)("td",null,slkwoo_data.mail),(0,t.createElement)("td",null,slkwoo_data.date),(0,t.createElement)("td",null,slkwoo_data.expiry_date)),i)))}),null),document.getElementById("slkwooadmin"))}();