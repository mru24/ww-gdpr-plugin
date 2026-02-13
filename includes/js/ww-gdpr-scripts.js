(function ($, root, undefined) {
	$(function () {
		'use strict';
		var app = {
			async init() {
				this.jq = $;
				console.log("WW GDPR front OK");

				this.consentPopup = this.jq('#wwgcbar');
			    this.acceptBtn = this.jq('#acceptBtn');
			    this.settingsModal = this.jq('#wwgcbar-modal');
			    this.settingsBtn = this.jq('#settingsBtn');
			    this.showMore = this.jq('.wwgcbar-show-description');
			    this.closeModal = this.jq('#wwgcbar-modal-close');

			    this.cookieCollapsedByID = this.jq('#wwgcbar-collapsed');
			    this.cookieCollapsedByClass = this.jq('.wwgcbar-collapsed');

			    this.cookieValid = this.jq('#wwgcbarCookieValid');
			    if(this.cookieValid.length>0) {
			      this.cookieValidTime = this.cookieValid.val();
			    }
			    this.cookiesNonEssential = this.jq('#wwgcbarCookiesNonEssential');
			    this.cookiesNonEssential.change((e)=>{
					this.jq(e.currentTarget).prop('checked')?
						this.jq(e.currentTarget).prop('checked',true) :
						this.jq(e.currentTarget).prop('checked',false);
		    	});

			    this.cookieCollapsedByID.on('click',(e)=>{
			      e.preventDefault();
			      this.consentPopup.addClass('active');
			    })
			    this.cookieCollapsedByClass.on('click',(e)=>{
			      e.preventDefault();
			      this.consentPopup.addClass('active');
			    })
			    this.settingsBtn.on('click',()=>{this.showSettings();});
			    this.closeModal.on('click',()=>{this.hideSettings();});
			    this.showMore.on('click',(e)=>{this.initDescription(e);});
				},
				async showSettings() {
			  		this.settingsModal.removeClass('hidden');
				  	// set non essential cookies to true by default //
				  	if(localStorage['non_essential_cookies']=='false') {
				  		this.cookiesNonEssential.prop('checked',false);
				  	} else {
				  		this.cookiesNonEssential.prop('checked',true);
				  	}
				},
				async hideSettings() {
		    		this.settingsModal.addClass('hidden');
				},
				async initDescription(el) {
			  		this.jq(el.currentTarget).prev('p.wwgcbar-description').slideToggle();
			  		this.jq(el.currentTarget).html()=="Show more"?this.jq(el.currentTarget).html('Hide'):this.jq(el.currentTarget).html('Show more');
				}
			}
			app.init();
		});
	})(jQuery, this);
