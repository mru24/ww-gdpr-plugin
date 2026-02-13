(function ($, root, undefined) {

  $(function () {

    'use strict';

var app = {
  async init() {
    this.jq = jQuery;
    console.log("WW GDPR CLASS ADMIN SCRIPT OK",this);

    this.nonEssentialCookiesCheckbox = this.jq('#nncookies');
    this.nonEssentialCookiesCheckbox.on('click',(e)=>{this.initNonEssentialCookiesCheckbox(e);});

    this.form = this.jq('.wwgcbar-content.admin form');
    this.form.on('keyup',()=>{ this.activateSubmit(); });
    this.jq('input[type="checkbox"]',this.form).on('change',()=>{ this.activateSubmit(); });
    this.jq('input[type="number"]',this.form).on('change',()=>{ this.activateSubmit(); });

  },
  async initNonEssentialCookiesCheckbox(e) {
    console.log(this.jq(e.currentTarget),e.currentTarget,e);
    if(e.currentTarget.checked) {
      console.log("CHECKBOX TRUE");
      // this.jq('.wwgcbar-checkbox-text').html("Enabled");
      // e.currentTarget.value = "true";
      // document.querySelector('.wwgcbar-checkbox-text').innerHTML = 'Enabled';
    } else {
      console.log("CHECKBOX FALSE");
      // this.jq('.wwgcbar-checkbox-text').html("Disabled");
      // e.currentTarget.value = "false";
      // document.querySelector('.wwgcbar-checkbox-text').innerHTML = 'Disabled';
    }
  },
  async activateSubmit() {
    this.jq('input[type="submit"]',this.form).removeClass('disabled');
  }
}

app.init();

  });

})(jQuery, this);
