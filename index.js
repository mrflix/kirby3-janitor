(function () {var b={name:"Janitor",props:{label:String,progress:String,job:String,cooldown:Number,status:String,data:String,pageURI:String,clipboard:Boolean,unsaved:Boolean,intab:Boolean,icon:[Boolean,String]},data:function(){return{oldlabel:"",downloadRequest:"",clipboardRequest:"",urlRequest:""}},computed:{pageHasChanges:function(){return this.$store.getters["content/hasChanges"]()},currentIcon:function(){return this.status?"doing-job"==this.status?"janitorLoader":"is-success"==this.status?"check":"has-error"==this.status?"alert":void 0:this.icon}},methods:{janitor:function(){if(!0===this.clipboard){this.clipboardRequest=this.data,this.oldlabel=this.label,this.label=this.progress,this.status="is-success";var t=this;return setTimeout(function(){t.label=t.oldlabel,t.status=""},this.cooldown),void this.$nextTick(function(){t.copyToClipboard(t.$refs.clipboard)})}if(""!==this.clipboardRequest)return this.copyToClipboard(this.$refs.clipboard),this.label=this.oldlabel,this.status="",void(this.clipboardRequest="");if(void 0===this.status||""===this.status){var e=this.job;if(e=e+"/"+encodeURIComponent(this.pageURI),null!=this.data){var o=this.data;e=e+"/"+encodeURIComponent(o)}this.getRequest(e)}},getRequest:function(t){var e=this,o=this;this.oldlabel=this.label,this.label=null!=this.progress&&this.progress.length>0?this.progress:this.label+"...",this.status="doing-job",this.$api.get(t).then(function(t){if(void 0!==t.label&&(o.label=t.label),void 0!==t.status?o.status=200==t.status?"is-success":"has-error":o.status="has-response",void 0!==t.reload&&!0===t.reload&&location.reload(),void 0!==t.href)if(e.intab){e.urlRequest=t.href;var s=e;e.$nextTick(function(){s.simulateClick(s.$refs.openintab)})}else location.href=t.href;if(void 0!==t.download){e.downloadRequest=t.download;var i=e;e.$nextTick(function(){i.simulateClick(i.$refs.download)})}void 0!==t.clipboard?e.clipboardRequest=t.clipboard:setTimeout(function(){o.label=o.oldlabel,o.status=""},o.cooldown)})},simulateClick:function(t){var e=new MouseEvent("click",{bubbles:!0,cancelable:!0,view:window});t.dispatchEvent(e)},copyToClipboard:function(t){var e,o,s,i,a,n;i=void 0,s=void 0,(o="INPUT"===t.tagName||"TEXTAREA"===t.tagName)?(n=t,i=t.selectionStart,s=t.selectionEnd):((n=document.getElementById("_hiddenCopyText_"))||((n=document.createElement("textarea")).style.position="absolute",n.style.left="-9999px",n.style.top="0",n.id="_hiddenCopyText_",document.body.appendChild(n)),n.textContent=t.textContent),e=document.activeElement,n.focus(),n.setSelectionRange(0,n.value.length),a=void 0;try{a=document.execCommand("copy")}catch(l){l,a=!1}return e&&"function"==typeof e.focus&&e.focus(),o?t.setSelectionRange(i,s):n.textContent="",a}}};if(typeof b==="function"){b=b.options}Object.assign(b,function(){var render=function(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",{staticClass:"janitor-wrapper"},[_c("k-button",{staticClass:"janitor",class:_vm.status,attrs:{"icon":_vm.currentIcon,"job":_vm.job,"disabled":!this.unsaved&&this.pageHasChanges},on:{"click":function($event){return _vm.janitor()}}},[_vm._v(_vm._s(_vm.label)+" ")]),_vm._v(" "),_c("a",{ref:"download",staticClass:"hidden",attrs:{"href":_vm.downloadRequest,"download":""}}),_vm._v(" "),_c("a",{ref:"openintab",staticClass:"hidden",attrs:{"href":_vm.urlRequest,"target":"_blank"}}),_vm._v(" "),_c("textarea",{ref:"clipboard",staticClass:"hidden"},[_vm._v(_vm._s(_vm.clipboardRequest))])],1)};var staticRenderFns=[];return{render:render,staticRenderFns:staticRenderFns,_compiled:true,_scopeId:null,functional:undefined}}());panel.plugin("bnomei/janitor",{fields:{janitor:b},icons:{janitorLoader:"<g fill=\"none\" fill-rule=\"evenodd\"><g transform=\"translate(1 1)\" stroke-width=\"1.75\"><circle cx=\"7\" cy=\"7\" r=\"7.2\" stroke=\"#000\" stroke-opacity=\".2\"/><path d=\"M14.2,7c0-4-3.2-7.2-7.2-7.2\" stroke=\"#000\"><animateTransform attributeName=\"transform\" type=\"rotate\" from=\"0 7 7\" to=\"360 7 7\" dur=\"1s\" repeatCount=\"indefinite\"/></path></g></g>"}});})();