(function(){
    var gt = window.gtranslateSettings || {};
    gt = gt[document.currentScript.getAttribute('data-gt-widget-id')] || gt;

    var lang_array_english = {"af":"Afrikaans","sq":"Albanian","am":"Amharic","ar":"Arabic","hy":"Armenian","az":"Azerbaijani","eu":"Basque","be":"Belarusian","bn":"Bengali","bs":"Bosnian","bg":"Bulgarian","ca":"Catalan","ceb":"Cebuano","ny":"Chichewa","zh-CN":"Chinese (Simplified)","zh-TW":"Chinese (Traditional)","co":"Corsican","hr":"Croatian","cs":"Czech","da":"Danish","nl":"Dutch","en":"English","eo":"Esperanto","et":"Estonian","tl":"Filipino","fi":"Finnish","fr":"French","fy":"Frisian","gl":"Galician","ka":"Georgian","de":"German","el":"Greek","gu":"Gujarati","ht":"Haitian Creole","ha":"Hausa","haw":"Hawaiian","iw":"Hebrew","hi":"Hindi","hmn":"Hmong","hu":"Hungarian","is":"Icelandic","ig":"Igbo","id":"Indonesian","ga":"Irish","it":"Italian","ja":"Japanese","jw":"Javanese","kn":"Kannada","kk":"Kazakh","km":"Khmer","ko":"Korean","ku":"Kurdish (Kurmanji)","ky":"Kyrgyz","lo":"Lao","la":"Latin","lv":"Latvian","lt":"Lithuanian","lb":"Luxembourgish","mk":"Macedonian","mg":"Malagasy","ms":"Malay","ml":"Malayalam","mt":"Maltese","mi":"Maori","mr":"Marathi","mn":"Mongolian","my":"Myanmar (Burmese)","ne":"Nepali","no":"Norwegian","ps":"Pashto","fa":"Persian","pl":"Polish","pt":"Portuguese","pa":"Punjabi","ro":"Romanian","ru":"Russian","sm":"Samoan","gd":"Scottish Gaelic","sr":"Serbian","st":"Sesotho","sn":"Shona","sd":"Sindhi","si":"Sinhala","sk":"Slovak","sl":"Slovenian","so":"Somali","es":"Spanish","su":"Sundanese","sw":"Swahili","sv":"Swedish","tg":"Tajik","ta":"Tamil","te":"Telugu","th":"Thai","tr":"Turkish","uk":"Ukrainian","ur":"Urdu","uz":"Uzbek","vi":"Vietnamese","cy":"Welsh","xh":"Xhosa","yi":"Yiddish","yo":"Yoruba","zu":"Zulu"};
    var lang_array_native = {"af":"Afrikaans","sq":"Shqip","am":"\u12a0\u121b\u122d\u129b","ar":"\u0627\u0644\u0639\u0631\u0628\u064a\u0629","hy":"\u0540\u0561\u0575\u0565\u0580\u0565\u0576","az":"Az\u0259rbaycan dili","eu":"Euskara","be":"\u0411\u0435\u043b\u0430\u0440\u0443\u0441\u043a\u0430\u044f \u043c\u043e\u0432\u0430","bn":"\u09ac\u09be\u0982\u09b2\u09be","bs":"Bosanski","bg":"\u0411\u044a\u043b\u0433\u0430\u0440\u0441\u043a\u0438","ca":"Catal\u00e0","ceb":"Cebuano","ny":"Chichewa","zh-CN":"\u7b80\u4f53\u4e2d\u6587","zh-TW":"\u7e41\u9ad4\u4e2d\u6587","co":"Corsu","hr":"Hrvatski","cs":"\u010ce\u0161tina\u200e","da":"Dansk","nl":"Nederlands","en":"English","eo":"Esperanto","et":"Eesti","tl":"Filipino","fi":"Suomi","fr":"Fran\u00e7ais","fy":"Frysk","gl":"Galego","ka":"\u10e5\u10d0\u10e0\u10d7\u10e3\u10da\u10d8","de":"Deutsch","el":"\u0395\u03bb\u03bb\u03b7\u03bd\u03b9\u03ba\u03ac","gu":"\u0a97\u0ac1\u0a9c\u0ab0\u0abe\u0aa4\u0ac0","ht":"Kreyol ayisyen","ha":"Harshen Hausa","haw":"\u014clelo Hawai\u02bbi","iw":"\u05e2\u05b4\u05d1\u05b0\u05e8\u05b4\u05d9\u05ea","hi":"\u0939\u093f\u0928\u094d\u0926\u0940","hmn":"Hmong","hu":"Magyar","is":"\u00cdslenska","ig":"Igbo","id":"Bahasa Indonesia","ga":"Gaeilge","it":"Italiano","ja":"\u65e5\u672c\u8a9e","jw":"Basa Jawa","kn":"\u0c95\u0ca8\u0ccd\u0ca8\u0ca1","kk":"\u049a\u0430\u0437\u0430\u049b \u0442\u0456\u043b\u0456","km":"\u1797\u17b6\u179f\u17b6\u1781\u17d2\u1798\u17c2\u179a","ko":"\ud55c\uad6d\uc5b4","ku":"\u0643\u0648\u0631\u062f\u06cc\u200e","ky":"\u041a\u044b\u0440\u0433\u044b\u0437\u0447\u0430","lo":"\u0e9e\u0eb2\u0eaa\u0eb2\u0ea5\u0eb2\u0ea7","la":"Latin","lv":"Latvie\u0161u valoda","lt":"Lietuvi\u0173 kalba","lb":"L\u00ebtzebuergesch","mk":"\u041c\u0430\u043a\u0435\u0434\u043e\u043d\u0441\u043a\u0438 \u0458\u0430\u0437\u0438\u043a","mg":"Malagasy","ms":"Bahasa Melayu","ml":"\u0d2e\u0d32\u0d2f\u0d3e\u0d33\u0d02","mt":"Maltese","mi":"Te Reo M\u0101ori","mr":"\u092e\u0930\u093e\u0920\u0940","mn":"\u041c\u043e\u043d\u0433\u043e\u043b","my":"\u1017\u1019\u102c\u1005\u102c","ne":"\u0928\u0947\u092a\u093e\u0932\u0940","no":"Norsk bokm\u00e5l","ps":"\u067e\u069a\u062a\u0648","fa":"\u0641\u0627\u0631\u0633\u06cc","pl":"Polski","pt":"Portugu\u00eas","pa":"\u0a2a\u0a70\u0a1c\u0a3e\u0a2c\u0a40","ro":"Rom\u00e2n\u0103","ru":"\u0420\u0443\u0441\u0441\u043a\u0438\u0439","sm":"Samoan","gd":"G\u00e0idhlig","sr":"\u0421\u0440\u043f\u0441\u043a\u0438 \u0458\u0435\u0437\u0438\u043a","st":"Sesotho","sn":"Shona","sd":"\u0633\u0646\u068c\u064a","si":"\u0dc3\u0dd2\u0d82\u0dc4\u0dbd","sk":"Sloven\u010dina","sl":"Sloven\u0161\u010dina","so":"Afsoomaali","es":"Espa\u00f1ol","su":"Basa Sunda","sw":"Kiswahili","sv":"Svenska","tg":"\u0422\u043e\u04b7\u0438\u043a\u04e3","ta":"\u0ba4\u0bae\u0bbf\u0bb4\u0bcd","te":"\u0c24\u0c46\u0c32\u0c41\u0c17\u0c41","th":"\u0e44\u0e17\u0e22","tr":"T\u00fcrk\u00e7e","uk":"\u0423\u043a\u0440\u0430\u0457\u043d\u0441\u044c\u043a\u0430","ur":"\u0627\u0631\u062f\u0648","uz":"O\u2018zbekcha","vi":"Ti\u1ebfng Vi\u1ec7t","cy":"Cymraeg","xh":"isiXhosa","yi":"\u05d9\u05d9\u05d3\u05d9\u05e9","yo":"Yor\u00f9b\u00e1","zu":"Zulu"};

    var default_language = gt.default_language||'auto';
    var languages = gt.languages||Object.keys(lang_array_english);
    var alt_flags = gt.alt_flags||{};
    var flag_size = gt.flag_size||32;
    var flags_location = gt.flags_location||'https://cdn.gtranslate.net/flags/svg/';
    var globe_size = gt.globe_size||60;
    var globe_color = gt.globe_color||'#66aaff';
    var url_structure = gt.url_structure||'none';
    var custom_domains = gt.custom_domains||{};

    var horizontal_position = gt.horizontal_position||'inline';
    var vertical_position = gt.vertical_position||null;

    var native_language_names = gt.native_language_names||false;
    var detect_browser_language = gt.detect_browser_language||false;
    var wrapper_selector = gt.wrapper_selector||'.gtranslate_wrapper';

    var custom_css = gt.custom_css||'';
    var lang_array = native_language_names && lang_array_native || lang_array_english;

    var u_class = '.gt_container-'+Array.from('globe'+wrapper_selector).reduce(function(h,c){return 0|(31*h+c.charCodeAt(0))},0).toString(36);

    var widget_code = '<!-- GTranslate: https://gtranslate.com -->';
    var widget_css = '';

    // helper functions
    function get_flag_src(lang) {
        if(!alt_flags[lang])
            return flags_location+lang+'.svg';
        else if(alt_flags[lang] == 'usa')
            return flags_location+'en-us.svg';
        else if(alt_flags[lang] == 'canada')
            return flags_location+'en-ca.svg';
        else if(alt_flags[lang] == 'brazil')
            return flags_location+'pt-br.svg';
        else if(alt_flags[lang] == 'mexico')
            return flags_location+'es-mx.svg';
        else if(alt_flags[lang] == 'argentina')
            return flags_location+'es-ar.svg';
        else if(alt_flags[lang] == 'colombia')
            return flags_location+'es-co.svg';
        else if(alt_flags[lang] == 'quebec')
            return flags_location+'fr-qc.svg';
        else
            return alt_flags[lang];
    }

    function get_lang_href(lang) {
        var href = '#';

        if(url_structure == 'sub_directory') {
            var gt_request_uri = (document.currentScript.getAttribute('data-gt-orig-url') || (location.pathname.startsWith('/'+current_lang+'/') && '/'+location.pathname.split('/').slice(2).join('/') || location.pathname)) + location.search + location.hash;
            href = (lang == default_language) && location.protocol+'//'+location.hostname+gt_request_uri || location.protocol+'//'+location.hostname+'/'+lang+gt_request_uri;
        } else if(url_structure == 'sub_domain') {
            var gt_request_uri = (document.currentScript.getAttribute('data-gt-orig-url') || location.pathname) + location.search + location.hash;
            var domain = document.currentScript.getAttribute('data-gt-orig-domain') || location.hostname;
            if(typeof custom_domains == 'object' && custom_domains[lang])
                href = (lang == default_language) && location.protocol+'//'+domain+gt_request_uri || location.protocol+'//'+custom_domains[lang]+gt_request_uri;
            else
                href = (lang == default_language) && location.protocol+'//'+domain+gt_request_uri || location.protocol+'//'+lang+'.'+domain.replace(/^www\./, '')+gt_request_uri;
        }

        return href;
    }

    var current_lang = document.querySelector('html').getAttribute('lang')||default_language;
    if(url_structure == 'none') {
        var googtrans_matches = document.cookie.match('(^|;) ?googtrans=([^;]*)(;|$)');
        current_lang = googtrans_matches && googtrans_matches[2].split('/')[2] || current_lang;
    }

    if(!lang_array[current_lang])
        current_lang = default_language;

    widget_code += '<span class="gsatelites">';
    languages.forEach(function(lang){
        var el_a = document.createElement('a');
        el_a.href = get_lang_href(lang);
        el_a.classList.add('nturl', 'gsatelite');
        current_lang == lang && el_a.classList.add('gt-current-lang');
        el_a.setAttribute('data-gt-lang', lang);
        el_a.title = lang_array[lang];
        el_a.style.width = el_a.style.height = flag_size + 'px';

        var el_img = document.createElement('img');
        el_img.setAttribute('data-gt-lazy-src', get_flag_src(lang));

        el_a.appendChild(el_img);

        widget_code += el_a.outerHTML;
    });
    widget_code += '</span><span class="gglobe"></span>';

    widget_css += '.gglobe{background-image:url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2032%2032%22%3E%3Cpath%20fill%3D%22'+encodeURIComponent(globe_color)+'%22%20d%3D%22M15.624%2C1.028c-7.811%2C0-14.167%2C6.355-14.167%2C14.167c0%2C7.812%2C6.356%2C14.167%2C14.167%2C14.167%20c7.812%2C0%2C14.168-6.354%2C14.168-14.167C29.792%2C7.383%2C23.436%2C1.028%2C15.624%2C1.028z%20M28.567%2C15.195c0%2C0.248-0.022%2C0.49-0.037%2C0.735%20c-0.091-0.23-0.229-0.53-0.262-0.659c-0.048-0.196-0.341-0.879-0.341-0.879s-0.293-0.39-0.488-0.488%20c-0.194-0.098-0.341-0.342-0.683-0.536c-0.342-0.196-0.487-0.293-0.779-0.293c-0.294%2C0-0.585-0.391-0.928-0.586%20c-0.342-0.194-0.39-0.097-0.39-0.097s0.39%2C0.585%2C0.39%2C0.731c0%2C0.146%2C0.438%2C0.39%2C0.879%2C0.292c0%2C0%2C0.292%2C0.537%2C0.438%2C0.683%20c0.146%2C0.146-0.049%2C0.293-0.341%2C0.488c-0.293%2C0.194-0.244%2C0.146-0.392%2C0.292c-0.146%2C0.146-0.633%2C0.392-0.78%2C0.488%20c-0.146%2C0.097-0.731%2C0.39-1.023%2C0.097c-0.143-0.141-0.099-0.438-0.195-0.634c-0.098-0.195-1.122-1.707-1.61-2.389%20c-0.085-0.12-0.293-0.49-0.438-0.585c-0.146-0.099%2C0.342-0.099%2C0.342-0.099s0-0.342-0.049-0.585%20c-0.05-0.244%2C0.049-0.585%2C0.049-0.585s-0.488%2C0.292-0.636%2C0.39c-0.145%2C0.098-0.292-0.194-0.486-0.439%20c-0.195-0.243-0.391-0.537-0.439-0.781c-0.049-0.243%2C0.244-0.341%2C0.244-0.341l0.438-0.243c0%2C0%2C0.537-0.097%2C0.879-0.049%20c0.341%2C0.049%2C0.877%2C0.098%2C0.877%2C0.098s0.146-0.342-0.049-0.488c-0.194-0.146-0.635-0.39-0.83-0.341%20c-0.194%2C0.048%2C0.097-0.244%2C0.34-0.439l-0.54-0.098c0%2C0-0.491%2C0.244-0.638%2C0.293c-0.146%2C0.048-0.4%2C0.146-0.596%2C0.39%20c-0.194%2C0.244%2C0.078%2C0.585-0.117%2C0.683c-0.194%2C0.098-0.326%2C0.146-0.473%2C0.194c-0.146%2C0.049-0.61%2C0-0.61%2C0%20c-0.504%2C0-0.181%2C0.46-0.05%2C0.623l-0.39-0.476L18.564%2C8.88c0%2C0-0.416-0.292-0.611-0.389c-0.195-0.098-0.796-0.439-0.796-0.439%20l0.042%2C0.439l0.565%2C0.572l0.05%2C0.013l0.294%2C0.39l-0.649%2C0.049V9.129c-0.612-0.148-0.452-0.3-0.521-0.347%20c-0.145-0.097-0.484-0.342-0.484-0.342s-0.574%2C0.098-0.721%2C0.147c-0.147%2C0.049-0.188%2C0.195-0.479%2C0.292%20c-0.294%2C0.098-0.426%2C0.244-0.523%2C0.39s-0.415%2C0.585-0.608%2C0.78c-0.196%2C0.196-0.558%2C0.146-0.704%2C0.146%20c-0.147%2C0-0.851-0.195-0.851-0.195V9.173c0%2C0%2C0.095-0.464%2C0.047-0.61l0.427-0.072l0.713-0.147l0.209-0.147l0.3-0.39%20c0%2C0-0.337-0.244-0.094-0.585c0.117-0.164%2C0.538-0.195%2C0.733-0.341c0.194-0.146%2C0.489-0.244%2C0.489-0.244s0.342-0.292%2C0.683-0.634%20c0%2C0%2C0.244-0.147%2C0.536-0.245c0%2C0%2C0.83%2C0.732%2C0.977%2C0.732s0.683-0.341%2C0.683-0.341s0.146-0.438%2C0.098-0.585%20c-0.049-0.146-0.293-0.634-0.293-0.634s-0.146%2C0.244-0.292%2C0.439s-0.244%2C0.439-0.244%2C0.439s-0.683-0.047-0.731-0.193%20c-0.05-0.147-0.146-0.388-0.196-0.533c-0.047-0.147-0.438-0.142-0.729-0.044c-0.294%2C0.098%2C0.047-0.526%2C0.047-0.526%20s0.294-0.368%2C0.488-0.368s0.635-0.25%2C0.828-0.298c0.196-0.049%2C0.783-0.272%2C1.025-0.272c0.244%2C0%2C0.537%2C0.105%2C0.684%2C0.105%20s0.731%2C0%2C0.731%2C0l1.023-0.082c0%2C0%2C0.879%2C0.325%2C0.585%2C0.521c0%2C0%2C0.343%2C0.211%2C0.489%2C0.357c0.137%2C0.138%2C0.491-0.127%2C0.694-0.24%20C26.127%2C6.525%2C28.567%2C10.576%2C28.567%2C15.195z%20M5.296%2C7.563c0%2C0.195-0.266%2C0.242%2C0%2C0.732c0.34%2C0.634%2C0.048%2C0.927%2C0.048%2C0.927%20s-0.83%2C0.585-0.976%2C0.683c-0.146%2C0.098-0.536%2C0.634-0.293%2C0.487c0.244-0.146%2C0.536-0.292%2C0.293%2C0.098%20c-0.244%2C0.391-0.683%2C1.024-0.78%2C1.269s-0.585%2C0.829-0.585%2C1.122c0%2C0.293-0.195%2C0.879-0.146%2C1.123%20c0.033%2C0.17-0.075%2C0.671-0.16%2C0.877c0.066-2.742%2C0.989-5.269%2C2.513-7.336C5.26%2C7.55%2C5.296%2C7.563%2C5.296%2C7.563z%20M6.863%2C5.693%20c1.193-1.101%2C2.591-1.979%2C4.133-2.573c-0.152%2C0.195-0.336%2C0.395-0.336%2C0.395s-0.341-0.001-0.976%2C0.683%20C9.051%2C4.881%2C9.197%2C4.686%2C9.051%2C4.88S8.953%2C5.124%2C8.611%2C5.369C8.271%2C5.612%2C8.124%2C5.905%2C8.124%2C5.905L7.587%2C6.1L7.149%2C5.905%20c0%2C0-0.392%2C0.147-0.343-0.049C6.82%2C5.804%2C6.841%2C5.75%2C6.863%2C5.693z%20M12.709%2C6.831l-0.194-0.292L12.709%2C6.1l0.47%2C0.188V5.417%20l0.449-0.243l0.373%2C0.536l0.574%2C0.635l-0.381%2C0.292l-1.016%2C0.195V6.315L12.709%2C6.831z%20M19.051%2C11.416%20c0.114-0.09%2C0.487%2C0.146%2C0.487%2C0.146s1.219%2C0.244%2C1.414%2C0.39c0.196%2C0.147%2C0.537%2C0.245%2C0.635%2C0.392%20c0.098%2C0.146%2C0.438%2C0.585%2C0.486%2C0.731c0.05%2C0.146%2C0.294%2C0.684%2C0.343%2C0.878c0.049%2C0.195%2C0.195%2C0.683%2C0.341%2C0.927%20c0.146%2C0.245%2C0.976%2C1.317%2C1.268%2C1.805l0.88-0.146c0%2C0-0.099%2C0.438-0.196%2C0.585c-0.097%2C0.146-0.39%2C0.536-0.536%2C0.731%20c-0.147%2C0.195-0.341%2C0.488-0.634%2C0.731c-0.292%2C0.243-0.294%2C0.487-0.439%2C0.683c-0.146%2C0.195-0.342%2C0.634-0.342%2C0.634%20s0.098%2C0.976%2C0.146%2C1.171s-0.341%2C0.731-0.341%2C0.731l-0.44%2C0.44l-0.588%2C0.779l0.048%2C0.731c0%2C0-0.444%2C0.343-0.689%2C0.537%20c-0.242%2C0.194-0.204%2C0.341-0.399%2C0.537c-0.194%2C0.194-0.957%2C0.536-1.152%2C0.585s-1.271%2C0.195-1.271%2C0.195v-0.438l-0.022-0.488%20c0%2C0-0.148-0.585-0.295-0.78s-0.083-0.489-0.327-0.732c-0.244-0.244-0.334-0.438-0.383-0.586c-0.049-0.146%2C0.053-0.584%2C0.053-0.584%20s0.197-0.537%2C0.294-0.732c0.098-0.195%2C0.001-0.487-0.097-0.683s-0.145-0.684-0.145-0.829c0-0.146-0.392-0.391-0.538-0.537%20c-0.146-0.146-0.097-0.342-0.097-0.535c0-0.197-0.146-0.635-0.098-0.977c0.049-0.341-0.438-0.098-0.731%2C0%20c-0.293%2C0.098-0.487-0.098-0.487-0.391s-0.536-0.048-0.878%2C0.146c-0.343%2C0.195-0.732%2C0.195-1.124%2C0.342%20c-0.389%2C0.146-0.583-0.146-0.583-0.146s-0.343-0.292-0.585-0.439c-0.245-0.146-0.489-0.438-0.685-0.682%20c-0.194-0.245-0.683-0.977-0.73-1.268c-0.049-0.294%2C0-0.49%2C0-0.831s0-0.536%2C0.048-0.78c0.049-0.244%2C0.195-0.537%2C0.342-0.781%20c0.146-0.244%2C0.683-0.536%2C0.828-0.634c0.146-0.097%2C0.488-0.389%2C0.488-0.585c0-0.195%2C0.196-0.292%2C0.292-0.488%20c0.099-0.195%2C0.44-0.682%2C0.879-0.487c0%2C0%2C0.389-0.048%2C0.535-0.097s0.536-0.194%2C0.729-0.292c0.195-0.098%2C0.681-0.144%2C0.681-0.144%20s0.384%2C0.153%2C0.53%2C0.153s0.622-0.085%2C0.622-0.085s0.22%2C0.707%2C0.22%2C0.854s0.146%2C0.292%2C0.391%2C0.39%20C17.44%2C11.562%2C18.563%2C11.807%2C19.051%2C11.416z%20M24.66%2C20.977c0%2C0.146-0.049%2C0.537-0.098%2C0.732c-0.051%2C0.195-0.147%2C0.537-0.195%2C0.73%20c-0.049%2C0.196-0.293%2C0.586-0.438%2C0.684c-0.146%2C0.098-0.391%2C0.391-0.536%2C0.439c-0.146%2C0.049-0.245-0.342-0.196-0.537%20c0.05-0.195%2C0.293-0.731%2C0.293-0.731s0.049-0.292%2C0.097-0.488c0.05-0.194%2C0.635-0.438%2C0.635-0.438l0.391-0.732%20C24.611%2C20.635%2C24.66%2C20.832%2C24.66%2C20.977z%20M3.015%2C18.071c0.063%2C0.016%2C0.153%2C0.062%2C0.28%2C0.175c0.184%2C0.16%2C0.293%2C0.242%2C0.537%2C0.341%20c0.243%2C0.099%2C0.341%2C0.243%2C0.634%2C0.39c0.293%2C0.147%2C0.196%2C0.05%2C0.585%2C0.488c0.391%2C0.438%2C0.342%2C0.438%2C0.439%2C0.683%20s0.244%2C0.487%2C0.342%2C0.635c0.098%2C0.146%2C0.39%2C0.243%2C0.536%2C0.341s0.39%2C0.195%2C0.536%2C0.195c0.147%2C0%2C0.586%2C0.439%2C0.83%2C0.487%20c0.244%2C0.05%2C0.244%2C0.538%2C0.244%2C0.538l-0.244%2C0.682l-0.196%2C0.731l0.196%2C0.585c0%2C0-0.294%2C0.245-0.487%2C0.245%20c-0.18%2C0-0.241%2C0.114-0.438%2C0.06C4.949%2C22.91%2C3.6%2C20.638%2C3.015%2C18.071z%22%2F%3E%3C%2Fsvg%3E");opacity:0.8;border-radius:50%;height:'+globe_size+'px;width:'+globe_size+'px;cursor:pointer;display:block;transition:all 0.3s}';
    widget_css += '.gglobe:hover{opacity:1;transform:scale(1.2)}';
    widget_css += '.gsatelite{text-decoration:none;position:absolute;z-index:100000;display:none;line-height:0}';
    widget_css += '.gsatelite img{border-radius:50%;opacity:0.9;transition:all 0.3s;width:'+flag_size+'px;height:'+flag_size+'px;object-fit:cover}';
    widget_css += '.gsatelite:hover img{opacity:1;transform:scale(1.3)}';
    widget_css += '.gsatelite.gt-current-lang img{opacity:1;box-shadow:rgba(0,0,0,0.5) 0 0 35px 20px;transform:scale(1.3)}';

    if(url_structure == 'none') {
        widget_code += '<div id="google_translate_element2"></div>';

        widget_css += "div.skiptranslate,#google_translate_element2{display:none!important}";
        widget_css += "body{top:0!important}";
        widget_css += "font font{background-color:transparent!important;box-shadow:none!important;position:initial!important}";
    }

    if(horizontal_position != 'inline')
        widget_code = '<div class="gt_switcher_wrapper" style="position:fixed;'+vertical_position+':15px;'+horizontal_position+':15px;z-index:999999;">' + widget_code + '</div>';

    var add_css = document.createElement('style');
    add_css.classList.add('gtranslate_css');
    add_css.textContent = widget_css;
    document.head.appendChild(add_css);

    document.querySelectorAll(wrapper_selector).forEach(function(e){e.classList.add(u_class.substring(1));e.innerHTML+=widget_code});

    var gt_globe_open = false;
    function gt_render_satelites(el) {
        gt_globe_open = true;
        el.querySelectorAll('.gsatelite img:not([src])').forEach(function(img){img.setAttribute('src', img.getAttribute('data-gt-lazy-src'));});
        var gglobe = el.querySelector('.gglobe');
        var gsatelites = el.querySelector('.gsatelites');
        if(gglobe.parentNode.style.position != 'fixed')
            gglobe.parentNode.style.position='relative';
        var gs_pos = gsatelites.parentNode.style.position == 'fixed' && {top: gsatelites.offsetParent.offsetTop, left: gsatelites.offsetParent.offsetLeft} || {top: gsatelites.getBoundingClientRect().top, left: gsatelites.getBoundingClientRect().left};
        var c_pos = {top: gglobe.offsetTop + gglobe.clientHeight / 2, left: gglobe.offsetLeft + gglobe.clientWidth / 2};
        var count = languages.length, r0 = (1.375 * globe_size), r = r0, dist = (1.4 * flag_size), r_num = 0, pi = Math.PI, j = 0, angle = 0.75 * pi, orbit_count = Math.floor(2*pi*r/dist), orbit_i = 0, delta = 2*pi/orbit_count;
        el.querySelectorAll('.gsatelite').forEach(function(gs, i) {
            do {
                if(orbit_i >= orbit_count){r_num++;r += r0;orbit_i=0;orbit_count=Math.floor(2*pi*r/dist);delta=2*pi/orbit_count;}
                angle += delta;var x = c_pos.left + Math.cos(angle) * r - flag_size/2, y = c_pos.top + Math.sin(angle) * r - flag_size/2;
                var vpHeight = window.innerHeight, vpWidth = Math.floor(document.body.getBoundingClientRect().width), tpViz = gs_pos.top + y >= 0 && gs_pos.top + y < vpHeight, btViz = gs_pos.top + y + flag_size > 0 && gs_pos.top + y + flag_size <= vpHeight, ltViz = gs_pos.left + x >= 0 && gs_pos.left + x < vpWidth,rtViz = gs_pos.left + x + flag_size > 0 && gs_pos.left + x + flag_size <= vpWidth, vVisible = tpViz && btViz, hVisible = ltViz && rtViz;
                if(vVisible && hVisible)
                    break;
                j++;
                orbit_i++;
            } while(j - i < 10 * count);
            gs.style.left = x + 'px';gs.style.top = y + 'px';j++;orbit_i++;setTimeout(function() {gs.style.display = 'inline'}, (i + 1) * 10);
        });
    }
    function gt_hide_satelites() {gt_globe_open=false;var gsatelite = document.querySelectorAll('.gsatelite');gsatelite.forEach(function(e, i) {setTimeout(function() {e.style.display = 'none';}, (gsatelite.length - i - 1) * 10);});}

    document.addEventListener('click',function(){if(gt_globe_open)gt_hide_satelites()});

    document.querySelectorAll(u_class+' .gglobe').forEach(function(e){
        e.addEventListener('click', function(evt) {evt.stopPropagation();if(gt_globe_open)gt_hide_satelites();else gt_render_satelites(e.parentNode);});
        e.addEventListener('pointerenter', function(evt) {evt.target.parentNode.querySelectorAll('.gsatelite img:not([src])').forEach(function(img){img.setAttribute('src', img.getAttribute('data-gt-lazy-src'))})});
    });

    if(url_structure == 'none') {
        function get_current_lang() {var keyValue = document.cookie.match('(^|;) ?googtrans=([^;]*)(;|$)');return keyValue ? keyValue[2].split('/')[2] : null;}
        function fire_event(element,event){try{if(document.createEventObject){var evt=document.createEventObject();element.fireEvent('on'+event,evt)}else{var evt=document.createEvent('HTMLEvents');evt.initEvent(event,true,true);element.dispatchEvent(evt)}}catch(e){}}
        function load_tlib(){if(!window.gt_translate_script){window.gt_translate_script=document.createElement('script');gt_translate_script.src='https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2';document.body.appendChild(gt_translate_script);}}
        window.doGTranslate = function(lang_pair){if(lang_pair.value)lang_pair=lang_pair.value;if(lang_pair=='')return;var lang=lang_pair.split('|')[1];if(get_current_lang() == null && lang == lang_pair.split('|')[0])return;var teCombo;var sel=document.getElementsByTagName('select');for(var i=0;i<sel.length;i++)if(sel[i].className.indexOf('goog-te-combo')!=-1){teCombo=sel[i];break;}if(document.getElementById('google_translate_element2')==null||document.getElementById('google_translate_element2').innerHTML.length==0||teCombo.length==0||teCombo.innerHTML.length==0){setTimeout(function(){doGTranslate(lang_pair)},500)}else{teCombo.value=lang;fire_event(teCombo,'change');fire_event(teCombo,'change')}}
        window.googleTranslateElementInit2=function(){new google.translate.TranslateElement({pageLanguage:default_language,autoDisplay:false},'google_translate_element2')};

        if(current_lang != default_language)
            load_tlib();
        else
            document.querySelectorAll(u_class).forEach(function(e){e.addEventListener('pointerenter',load_tlib)});

        document.querySelectorAll(u_class + ' a[data-gt-lang]').forEach(function(e){e.addEventListener('click', function(evt) {
            evt.preventDefault();
            document.querySelectorAll(u_class + ' a.gt-current-lang').forEach(function(e){e.classList.remove('gt-current-lang')});
            e.classList.add('gt-current-lang');
            doGTranslate(default_language+'|'+e.getAttribute('data-gt-lang'));
        })});
    }

    if(detect_browser_language && window.sessionStorage && window.navigator && sessionStorage.getItem('gt_autoswitch') == null && !/bot|spider|slurp|facebook/i.test(navigator.userAgent)) {
        var accept_language = (navigator.language||navigator.userLanguage).toLowerCase();
        switch(accept_language) {
            case 'zh':
            case 'zh-cn':var preferred_language = 'zh-CN';break;
            case 'zh-tw':
            case 'zh-hk':var preferred_language = 'zh-TW';break;
            case 'he':var preferred_language = 'iw';break;
            default:var preferred_language = accept_language.substr(0,2);break;
        }

        if(current_lang == default_language && preferred_language != default_language && languages.includes(preferred_language)) {
            if(url_structure == 'none') {
                load_tlib();
                window.gt_translate_script.onload=function(){
                    doGTranslate(default_language+'|'+preferred_language);
                    document.querySelectorAll(u_class+' a.gt-current-lang').forEach(function(e){e.classList.remove('gt-current-lang')});
                    document.querySelector(u_class+' a[data-gt-lang="'+preferred_language+'"]').classList.add('gt-current-lang');
                };
            } else
                document.querySelectorAll(u_class+' a[data-gt-lang="'+preferred_language+'"]').forEach(function(e){location.href=e.href});
        }

        sessionStorage.setItem('gt_autoswitch', 1);
    }
})();
