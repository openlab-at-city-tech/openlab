// TODO: This file can be removed once the minimum Pro API version is 4.0
EasyCookie=(function(){var EPOCH='Thu, 01-Jan-1970 00:00:01 GMT',RATIO=1000*60*60*24,KEYS=['expires','path','domain'],esc=escape,un=unescape,doc=document,me;var get_now=function(){var r=new Date();r.setTime(r.getTime());return r;}
    var cookify=function(c_key,c_val){var i,key,val,r=[],opt=(arguments.length>2)?arguments[2]:{};r.push(esc(c_key)+'='+esc(c_val));for(i=0;i<KEYS.length;i++){key=KEYS[i];if(val=opt[key])
        r.push(key+'='+val);}
        if(opt.secure)
            r.push('secure');return r.join('; ');}
    var alive=function(){var k='__EC_TEST__',v=new Date();v=v.toGMTString();this.set(k,v);this.enabled=(this.remove(k)==v);return this.enabled;}
    me={set:function(key,val){var opt=(arguments.length>2)?arguments[2]:{},now=get_now(),expire_at,cfg={};if(opt.expires){opt.expires*=RATIO;cfg.expires=new Date(now.getTime()+opt.expires);cfg.expires=cfg.expires.toGMTString();}
        var keys=['path','domain','secure'];for(i=0;i<keys.length;i++)
            if(opt[keys[i]])
                cfg[keys[i]]=opt[keys[i]];var r=cookify(key,val,cfg);doc.cookie=r;return val;},has:function(key){key=esc(key);var c=doc.cookie,ofs=c.indexOf(key+'='),len=ofs+key.length+1,sub=c.substring(0,key.length);return((!ofs&&key!=sub)||ofs<0)?false:true;},get:function(key){key=esc(key);var c=doc.cookie,ofs=c.indexOf(key+'='),len=ofs+key.length+1,sub=c.substring(0,key.length),end;if((!ofs&&key!=sub)||ofs<0)
        return null;end=c.indexOf(';',len);if(end<0)
        end=c.length;return un(c.substring(len,end));},remove:function(k){var r=me.get(k),opt={expires:EPOCH};doc.cookie=cookify(k,'',opt);return r;},keys:function(){var c=doc.cookie,ps=c.split('; '),i,p,r=[];for(i=0;i<ps.length;i++){p=ps[i].split('=');r.push(un(p[0]));}
        return r;},all:function(){var c=doc.cookie,ps=c.split('; '),i,p,r=[];for(i=0;i<ps.length;i++){p=ps[i].split('=');r.push([un(p[0]),un(p[1])]);}
        return r;},version:'0.2.1',enabled:false};me.enabled=alive.call(me);return me;}());

window.Ngg_Store = {

    get: function(key){
        return EasyCookie.get(key);
    },

    set: function(key, value){
        if (typeof(value) == 'object') {
            value = JSON.stringify(value);
        }
        return EasyCookie.set(key, value, {
            expires: 10,
            path: '/',
            secure: false
        });
    },

    del: function(key){
        EasyCookie.remove(key);
        return !this.has(key);
    },

    has: function(key){
        var value = this.get(key);
        return typeof(value) != 'undefined' && value != null;
    },

    save: function(){
        return true;
    }
};

jQuery(function($){
    $(window).on('unload', function(){
        Ngg_Store.save();
    })
});
