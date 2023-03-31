define([
    api_template + '/locales/ca.js',
    api_template + '/locales/de.js',
    api_template + '/locales/es.js',
    api_template + '/locales/fr.js',
    api_template + '/locales/nl.js',
    api_template + '/locales/pl.js',
    api_template + '/locales/pt_br.js',
    api_template + '/locales/ru.js',
    api_template + '/locales/zh.js',
    api_template + '/locales/zh_cn.js'
], function() {
    var langId = (navigator.language || navigator.userLanguage).toLowerCase().replace('-', '_');
    var language = langId.substr(0, 2);
    var locales = {};

    for (index in arguments) {
        for (property in arguments[index])
            locales[property] = arguments[index][property];
    }
    if ( ! locales['en'])
        locales['en'] = {};

    if ( ! locales[langId] && ! locales[language])
        language = 'en';

    var locale = (locales[langId] ? locales[langId] : locales[language]);

    function __(text) {
        var index = locale[text];
        if (index === undefined)
            return text;
        return index;
    };

    function setLanguage(language) {
        locale = locales[language];
    }

    return {
        __         : __,
        locales    : locales,
        locale     : locale,
        setLanguage: setLanguage
    };
});
