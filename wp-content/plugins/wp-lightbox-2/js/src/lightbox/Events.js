class Events {
    constructor(config, parent) {
        this.config = config;
        this.parent = parent;
    }

    bindNavigationButtons(callback) {
        const prevLink = document.getElementById("prevLink");
        const prevLinkText = document.getElementById("prevLinkText");
        const nextLink = document.getElementById("nextLink");
        const nextLinkText = document.getElementById("nextLinkText");
        if(prevLink) {
            prevLink.addEventListener('click', this.onNavigationButtonClick.bind(this, callback, 'prev'));
        }
        if(prevLinkText) {
            prevLinkText.addEventListener('click', this.onNavigationButtonClick.bind(this, callback, 'prev'));
        }
        if(nextLink) {
            nextLink.addEventListener('click', this.onNavigationButtonClick.bind(this, callback, 'next'));
        }
        if(nextLinkText) {
            nextLinkText.addEventListener('click', this.onNavigationButtonClick.bind(this, callback, 'next'));
        }
    }

    onNavigationButtonClick(callback, direction, e) {
        e.stopPropagation()
        e.preventDefault();
        if(direction === 'prev') {
            const index = (this.config.activeImage == 0) ? (this.config.imageArray.length - 1) : this.config.activeImage - 1;
            callback(index);
        }
        if(direction === 'next') {
            const index = (this.config.activeImage == (this.config.imageArray.length - 1)) ? 0 : this.config.activeImage + 1;
            callback(index);
        }
    }


    enableKeyboardNav(callback) {
        document.addEventListener('keydown', this.keyboardAction.bind(this, callback));
    }

    keyboardAction(callback, e) {
        var config = this.config;
        var keycode = e.keyCode;
        var escapeKey = 27;
        var key = String.fromCharCode(keycode).toLowerCase();
        if ((key == 'x') || (key == 'o') || (key == 'c') || (keycode == escapeKey)) { // close lightbox
            this.parent.end();
        } else if ((key == 'p') || (keycode == 37)) { // display previous image
            if (config.loopImages) {
                callback((config.activeImage == 0) ? (config.imageArray.length - 1) : config.activeImage - 1);
            }
            else if (config.activeImage != 0) {
                callback(config.activeImage - 1);
            }
        } else if ((key == 'n') || (keycode == 39)) { // display next image
            if (this.config.loopImages) {
                callback((config.activeImage == (config.imageArray.length - 1)) ? 0 : config.activeImage + 1);
            }
            else if (config.activeImage != (config.imageArray.length - 1)) {
                callback(config.activeImage + 1);
            }
        }
        return false;
    };

}

module.exports = Events;