(()=>{(function(){"use strict";var b=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"janitor-wrapper"},[i("k-button",{staticClass:"janitor",class:t.btnClass,attrs:{id:t.id,icon:t.currentIcon,job:t.job,disabled:!t.isUnsaved&&t.pageHasChanges},on:{click:t.runJanitor}},[t._v(" "+t._s(t.btnLabel||t.label)+" ")]),i("a",{directives:[{name:"show",rawName:"v-show",value:t.downloadRequest,expression:"downloadRequest"}],ref:"downloadAnchor",staticClass:"visually-hidden",attrs:{href:t.downloadRequest,download:""}}),i("a",{directives:[{name:"show",rawName:"v-show",value:t.urlRequest,expression:"urlRequest"}],ref:"tabAnchor",staticClass:"visually-hidden",attrs:{href:t.urlRequest,target:"_blank"}})],1)},p=[],k="";function v(t,e,i,r,n,l,u,c){var s=typeof t=="function"?t.options:t;e&&(s.render=e,s.staticRenderFns=i,s._compiled=!0),r&&(s.functional=!0),l&&(s._scopeId="data-v-"+l);var o;if(u?(o=function(a){a=a||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext,!a&&typeof __VUE_SSR_CONTEXT__!="undefined"&&(a=__VUE_SSR_CONTEXT__),n&&n.call(this,a),a&&a._registeredComponents&&a._registeredComponents.add(u)},s._ssrRegister=o):n&&(o=c?function(){n.call(this,(s.functional?this.parent:this).$root.$options.shadowRoot)}:n),o)if(s.functional){s._injectStyles=o;var w=s.render;s.render=function(R,f){return o.call(f),w(R,f)}}else{var d=s.beforeCreate;s.beforeCreate=d?[].concat(d,o):[o]}return{exports:t,options:s}}const g={props:{label:String,progress:String,job:String,cooldown:Number,status:String,data:String,pageURI:String,clipboard:Boolean,unsaved:Boolean,autosave:Boolean,intab:Boolean,confirm:String,icon:{type:[Boolean,String],default:!1}},data(){return{btnLabel:null,btnClass:null,downloadRequest:null,clipboardRequest:null,urlRequest:null,isUnsaved:!1,icons:{"is-running":"janitorLoader","is-success":"check","has-error":"alert"}}},computed:{id(){var t;return"janitor-"+this.hashCode(this.job+((t=this.btnLabel)!=null?t:"")+this.pageURI)},pageHasChanges(){return this.$store.getters["content/hasChanges"]()},currentIcon(){var t;return(t=this.icons[this.status])!=null?t:this.icon}},created(){this.$events.$on("model.update",()=>sessionStorage.getItem("clickAfterAutosave")&&location.reload()),this.clickAfterAutosave()},methods:{hashCode(t){let e=0;if(t.length===0)return e;for(let i=0;i<t.length;i++){let r=t.charCodeAt(i);e=(e<<5)-e+r,e=e&e}return e},clickAfterAutosave(){sessionStorage.getItem("clickAfterAutosave")===this.id&&(sessionStorage.removeItem("clickAfterAutosave"),this.runJanitor())},async runJanitor(){if(this.confirm&&!window.confirm(this.confirm))return;if(this.autosave&&this.pageHasChanges){const e=document.querySelector(".k-panel .k-form-buttons .k-view").lastChild;if(e){this.isUnsaved=!1,sessionStorage.setItem("clickAfterAutosave",this.id),this.simulateClick(e);return}}if(this.clipboard){this.clipboardRequest=this.data,this.btnLabel=this.progress,this.btnClass="is-success",setTimeout(()=>{this.btnLabel=null,this.btnClass=null},this.cooldown),this.$nextTick(()=>{this.copyToClipboard(this.data)});return}if(this.clipboardRequest){await this.copyToClipboard(this.clipboardRequest),this.btnLabel=null,this.btnClass=null,this.clipboardRequest=null;return}if(this.status)return;let t=this.job+"/"+encodeURIComponent(this.pageURI);this.data&&(t=t+"/"+encodeURIComponent(this.data)),this.getRequest(t)},async getRequest(t){var c;this.btnLabel=(c=this.progress)!=null?c:`${this.label} \u2026`,this.btnClass="is-running";const{label:e,status:i,reload:r,href:n,download:l,clipboard:u}=await this.$api.get(t);e&&(this.btnLabel=e),i?this.btnClass=i===200?"is-success":"has-error":this.btnClass="has-response",r&&location.reload(),n&&(this.intab?(this.urlRequest=n,this.$nextTick(()=>{this.simulateClick(this.$refs.tabAnchor)})):location.href=n),l&&(this.downloadRequest=l,this.$nextTick(()=>{this.simulateClick(this.$refs.downloadAnchor)})),u?this.clipboardRequest=u:setTimeout(()=>{this.btnLabel=null,this.btnClass=null},this.cooldown)},simulateClick(t){const e=new MouseEvent("click",{bubbles:!0,cancelable:!0,view:window});t.dispatchEvent(e)},async copyToClipboard(t){try{await navigator.clipboard.writeText(t)}catch{console.error("navigator.clipboard is not available")}}}},h={};var m=v(g,b,p,!1,C,null,null,null);function C(t){for(let e in h)this[e]=h[e]}var _=function(){return m.exports}();window.panel.plugin("bnomei/janitor",{fields:{janitor:_},icons:{janitorLoader:'<g fill="none" fill-rule="evenodd"><g transform="translate(1 1)" stroke-width="1.75"><circle cx="7" cy="7" r="7.2" stroke="#000" stroke-opacity=".2"/><path d="M14.2,7c0-4-3.2-7.2-7.2-7.2" stroke="#000"><animateTransform attributeName="transform" type="rotate" from="0 7 7" to="360 7 7" dur="1s" repeatCount="indefinite"/></path></g></g>'}})})();})();
