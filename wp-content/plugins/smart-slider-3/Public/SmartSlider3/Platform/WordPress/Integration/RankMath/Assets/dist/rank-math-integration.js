(function () {
    var cachedSlidersHTML = {};

    function request(data, done, fail) {

        var request = new XMLHttpRequest();
        request.open('POST', window.SmartSlider3RankMath.adminAjaxUrl, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

        request.onerror = fail;
        request.onreadystatechange = function () {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                done(request.responseText);
            } else {
                fail();
            }
        };

        request.send(data);

        return request;
    }

    function refresh() {
        rankMathEditor.refresh('content');
    }

    function loadSlideByAlias(alias) {
        request('alias=' + alias, function (responseText) {
            cachedSlidersHTML[alias] = responseText;
            refresh();
        }, function () {
            cachedSlidersHTML[alias] = '';
            refresh();
        });
    }

    function loadSliderByID(id) {
        request('sliderID=' + id, function (responseText) {
            cachedSlidersHTML[id] = responseText;
            refresh();
        }, function () {
            cachedSlidersHTML[id] = '';
            refresh();
        });
    }

    wp.hooks.addFilter('rank_math_content', 'plugin-name', function (content) {

        if (content !== undefined) {
            var matches = content.match(/\[smartslider3[^\]]+?\]/gi);
            if (matches && matches.length) {
                for (var i = 0; i < matches.length; i++) {

                    var aliasMatch = matches[i].match(/alias=['"]([^'"]+)['"]/);
                    if (aliasMatch) {
                        var alias = aliasMatch[1];
                        if (typeof cachedSlidersHTML[alias] === 'string') {
                            content = content.replace(matches[i], cachedSlidersHTML[alias]);
                        } else if (cachedSlidersHTML[alias] === undefined) {
                            loadSlideByAlias(alias);
                        }
                    }

                    var idMatch = matches[i].match(/slider=['"]([^'"]+)['"]/);
                    if (idMatch) {
                        var id = idMatch[1];
                        if (typeof cachedSlidersHTML[id] === 'string') {
                            content = content.replace(matches[i], cachedSlidersHTML[id]);
                        } else if (cachedSlidersHTML[id] === undefined) {
                            loadSliderByID(id);
                        }
                    }
                }
            }
        }

        return content;
    }, 11);
})();