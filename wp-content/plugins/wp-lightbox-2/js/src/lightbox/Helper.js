const $ = window.jQuery;

class Helper {
    constructor(config) {
        this.config = config;
    }
    hide(element) {
        if (element) {
            element.style.display = 'none';
        }
    };
    show(element) {
        if (element) {
            element.style.display = 'block';
        }
    };
    getPageSize() {
        const pageDocumentHeight = document.documentElement.scrollHeight;
        const pageDocumentWidth = document.documentElement.scrollWidth;
        if (this.config.isIE8 && pageDocumentHeight > 4096) {
            pageDocumentHeight = 4096;
        }
        var viewportHeight = document.documentElement.clientHeight - this.config.adminBarHeight;
        var pageWindowWidth = document.documentElement.clientWidth;
        const pageSize = {
            pageDocumentWidth: pageDocumentWidth,
            pageDocumentHeight: pageDocumentHeight,
            pageWindowWidth: pageWindowWidth,
            viewportHeight: viewportHeight,
            documentScrollHeight: document.documentElement.scrollHeight
        };
        return pageSize;
    };
    isIE8() {
        var isBuggy = false;
        if (document.createElement) {
            var el = document.createElement("div");
            if (el && el.querySelectorAll) {
                el.innerHTML = "<object><param name=\"\"></object>";
                isBuggy = el.querySelectorAll("param").length != 1;
            }
            el = null;
        }
        return isBuggy;
    };
    getPageScroll() {
        var xScroll = 0; var yScroll = 0;
        if (self.pageYOffset) {
            yScroll = self.pageYOffset;
            xScroll = self.pageXOffset;
        } else if (document.documentElement && document.documentElement.scrollTop) {  // Explorer 6 Strict
            yScroll = document.documentElement.scrollTop;
            xScroll = document.documentElement.scrollLeft;
        } else if (document.body) {// all other Explorers
            yScroll = document.body.scrollTop;
            xScroll = document.body.scrollLeft;
        }
        const wpadminbar = document.getElementById("wpadminbar");
        if (wpadminbar) {
            const style = window.getComputedStyle(wpadminbar);
            const top = style.getPropertyValue('top');
            if (this.config.adminBarHeight && parseInt(top, 10) === 0) {
                yScroll += this.config.adminBarHeight;
            }
        }
        return {xScroll, yScroll};
    };
    setLightBoxPos(newTop, newLeft) {
        if (this.config.resizeSpeed > 0) {
            $('#lightbox').animate({ top: newTop }, 250, 'linear');
            return $('#lightbox').animate({ left: newLeft }, 250, 'linear').show();
        }
        return $('#lightbox').css({ top: newTop + 'px', left: newLeft + 'px' }).show();
    }
}

module.exports = Helper;