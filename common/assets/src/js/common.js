var tools = {
    redirect : function(url){
        location.href = url;
    },
    addUrlParamToUrl: function(param, val){
        var parts = _.split(location.href, '?');
        var res = {};
        res[param] = val;

        if(!_.isUndefined(parts[1])){
            var paramParts = _.split(parts[1], '&');

            _.forEach(paramParts, function(value) {
                var valParts = _.split(value, '=');
                if(!_.isUndefined(valParts[0]) && !_.isUndefined(valParts[1]) && _.isUndefined(res[valParts[0]])){
                    res[valParts[0]] = valParts[1];
                }
            });

        }

        var resParts = [];
        _.forEach(res, function(value, idx) {
            resParts.push(idx+'='+value);
        });

        return parts[0]+'?'+_.join(resParts, '&');

    },
    addUrlParamAndRedirect:function(param, val){
        this.redirect(this.addUrlParamToUrl(param, val));
    },
    initCheckBoxes: function(){
        if($('body').find('[data-icheck]').length > 0){
            $('[type=checkbox]').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '10%' /* optional */
            });
        }
    }
};

jQuery(function() {
    tools.initCheckBoxes();
});