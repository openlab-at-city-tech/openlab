const $ = window.jQuery;


class Display {
    constructor(config, helper) {
        this.config = config;
        this.helper = helper;
    }
    changeImage(imageNum) {
        if (this.config.inprogress == false) {
            this.config.inprogress = true;
            this.config.activeImage = imageNum;
            // hide elements during transition
            const loading = document.getElementById('loading');
            const lightboxImage = document.getElementById('lightboxImage');
            const hoverNav = document.getElementById('hoverNav');
            const prevLink = document.getElementById('prevLink');
            const nextLink = document.getElementById('nextLink');
            if (loading) {
                this.helper.show(loading);
            }
            if (lightboxImage) {
                this.helper.hide(lightboxImage);
            }
            if (hoverNav) {
                this.helper.hide(hoverNav);
            }
            if (prevLink) {
                this.helper.hide(prevLink);
            }
            if (nextLink) {
                this.helper.hide(nextLink);
            }
            this.doChangeImage();
        }
    };


    doChangeImage() {
        this.config.imgPreloader = new Image();
        this.config.imgPreloader.onload = _ => {
            const lightboxImage = document.getElementById('lightboxImage');
            if (lightboxImage) {
                lightboxImage.src = this.config.imageArray[this.config.activeImage][0];
            }
            this.doScale();  // once image is preloaded, resize image container
            this.preloadNeighborImages();    
        };
        this.config.imgPreloader.src = this.config.imageArray[this.config.activeImage][0];
    };

    doScale() {
        this.updateDetails(); //Kevin: moved updateDetails() here, seems to work fine.    
        const overlay = document.getElementById('overlay');
        if (!overlay || !this.config.imgPreloader) {
            return;
        }
        var newWidth = this.config.imgPreloader.width;
        var newHeight = this.config.imgPreloader.height;
        var arrayPageSize = this.helper.getPageSize();
        var noScrollWidth = (arrayPageSize.pageWindowWidth < arrayPageSize.pageDocumentWidth) ? arrayPageSize.pageDocumentWidth : arrayPageSize.pageWindowWidth; //if viewport is smaller than page, use page width.
        overlay.style.width = noScrollWidth + 'px';
        overlay.style.height = arrayPageSize.pageDocumentHeight + 'px';
        const imageDataContainer = document.getElementById('imageDataContainer');
        var maxHeight = (arrayPageSize.viewportHeight) - (imageDataContainer.offsetHeight + (2 * this.config.borderSize));
        var maxWidth = (arrayPageSize.pageWindowWidth) - (2 * this.config.borderSize);
        if (this.config.fitToScreen) {
            var displayHeight = maxHeight - this.config.marginSize;
            var displayWidth = maxWidth - this.config.marginSize;
            var ratio = 1;
            if (newHeight > displayHeight) {
                ratio = displayHeight / newHeight; //ex. 600/1024 = 0.58					
            }
            newWidth = newWidth * ratio;
            newHeight = newHeight * ratio;
            ratio = 1;
            if (newWidth > displayWidth) {
                ratio = displayWidth / newWidth; //ex. 800/1280 == 0.62					
            }
            newWidth = Math.round(newWidth * ratio);
            newHeight = Math.round(newHeight * ratio);
        }
        var arrayPageScroll = this.helper.getPageScroll();
        var centerY = arrayPageScroll.yScroll + (maxHeight * 0.5);
        var newTop = centerY - newHeight * 0.5;
        var newLeft = arrayPageScroll.xScroll;
        const lightbox = document.getElementById('lightboxImage');
        lightbox.style.width = newWidth;
        lightbox.style.height = newHeight;
        this.resizeImageContainer(newWidth, newHeight, newTop, newLeft);
    }
    /*2.28.4 -  Compatible with wordpress 3.6.*/
    resizeImageContainer(imgWidth, imgHeight, lightboxTop, lightboxLeft) {
        const outerImageContainer = document.getElementById("outerImageContainer");
        const imageDataContainer = document.getElementById("imageDataContainer");
        if (!outerImageContainer || !imageDataContainer) {
            return;
        }
        this.config.widthCurrent = outerImageContainer.offsetWidth;
        this.config.heightCurrent = outerImageContainer.offsetHeight;
        var widthNew = Math.max(350, imgWidth + ((this.config.borderSize || 0) * 2));
        var heightNew = (imgHeight + ((this.config.borderSize || 0) * 2));
        // scalars based on change from old to new
        this.config.xScale = (widthNew / this.config.widthCurrent) * 100;
        this.config.yScale = (heightNew / this.config.heightCurrent) * 100;
        this.helper.setLightBoxPos(lightboxTop, lightboxLeft);
        
        $('#imageDataContainer').animate({ width: widthNew }, this.config.resizeSpeed, 'linear');
        $('#outerImageContainer').animate({ width: widthNew }, this.config.resizeSpeed, 'linear', _ => {
            $('#outerImageContainer').animate({ height: heightNew }, this.config.resizeSpeed, 'linear', _=> {
                this.showImage();
            });
        });

        this.showNavigationElements();
        if (document.getElementById("prevLink"))
            document.getElementById("prevLink").style.height = imgHeight;
        if (document.getElementById("nextLink"))
            document.getElementById("nextLink").style.height = imgHeight;
    };

    showImage() {
        //assumes updateDetails have been called earlier!
        this.helper.show(document.getElementById("imageData"));
        this.helper.show(document.getElementById('caption'));
        //$('#imageDataContainer').slideDown(400);
        //$("#imageDetails").hide().fadeIn(400);	
        this.helper.hide(document.getElementById("loading"));
        if (this.config.resizeSpeed > 0) {
            $('#lightboxImage').fadeIn("fast");
        } else {
            this.helper.show(document.getElementById("lightboxImage"));
        }
        this.config.inprogress = false;
    };

    preloadNeighborImages() {
        if (this.config.loopImages && this.config.imageArray.length > 1) {
            let preloadNextImage = new Image();
            preloadNextImage.src = this.config.imageArray[(this.config.activeImage == (this.config.imageArray.length - 1)) ? 0 : this.config.activeImage + 1][0]
            let preloadPrevImage = new Image();
            preloadPrevImage.src = this.config.imageArray[(this.config.activeImage == 0) ? (this.config.imageArray.length - 1) : this.config.activeImage - 1][0]
        } else {
            if ((this.config.imageArray.length - 1) > this.config.activeImage) {
                let preloadNextImage = new Image();
                preloadNextImage.src = this.config.imageArray[this.config.activeImage + 1][0];
            }
            if (this.config.activeImage > 0) {
                let preloadPrevImage = new Image();
                preloadPrevImage.src = this.config.imageArray[this.config.activeImage - 1][0];
            }
        }
    };


    updateDetails() {
        const numberDisplay = document.getElementById('numberDisplay');
        if (numberDisplay) {
            numberDisplay.innerHTML = '';
        }
        const caption = document.getElementById('caption');
        if (caption) {
            caption.innerHTML = '';
            this.helper.hide(caption);
            if (this.config.imageArray[this.config.activeImage][1]) {
                caption.innerHTML = this.config.imageArray[this.config.activeImage][1];
                this.helper.show();
            }
        }
        var nav_html = '';
        var prev = '';
        var pos = (this.config.imageArray.length > 1) ? this.config.strings.image + (this.config.activeImage + 1) + this.config.strings.of + this.config.imageArray.length : '';
        var link = (this.config.displayDownloadLink) ? '<a href="' + this.config.imageArray[this.config.activeImage][0] + '" id="downloadLink" target="' + this.config.linkTarget + '">' + this.config.strings.download + '</a>' : '';
        var next = '';
        if (this.config.imageArray.length > 1 && !this.config.disableNavbarLinks) {	 // display previous / next text links   			           
            if ((this.config.activeImage) > 0 || this.config.loopImages) {
                prev = '<a title="' + this.config.strings.prevLinkTitle + '" href="#" id="prevLinkText">' + this.config.strings.prevLinkText + "</a>";
            }
            if (((this.config.activeImage + 1) < this.config.imageArray.length) || this.config.loopImages) {
                next += '<a title="' + this.config.strings.nextLinkTitle + '" href="#" id="nextLinkText">' + this.config.strings.nextLinkText + "</a>";
            }
        }
        nav_html = prev + nav_html + pos + link + next;
        if (nav_html != '') {
            if (document.getElementById("numberDisplay")) {
                document.getElementById("numberDisplay").innerHTML = nav_html;
                this.helper.show(document.getElementById("numberDisplay"));
            }
        }
    };


    showNavigationElements() {
        const prevLink = document.getElementById("prevLink");
        const prevLinkText = document.getElementById("prevLinkText");
        const nextLink = document.getElementById("nextLink");
        const nextLinkText = document.getElementById("nextLinkText");
        if (this.config.imageArray.length > 1) {
            this.helper.show(document.getElementById("hoverNav"));
            // if loopImages is true, always show next and prev image buttons 
            if (this.config.loopImages) {
                this.helper.show(prevLink);
                this.helper.show(prevLinkText);
                this.helper.show(nextLink);
                this.helper.show(nextLinkText);

            } else {
                // if not first image in set, display prev image button
                if (this.config.activeImage != 0) {
                    this.helper.show(prevLink);
                    this.helper.show(prevLinkText);
                }
                // if not last image in set, display next image button
                if (this.config.activeImage != (this.config.imageArray.length - 1)) {
                    this.helper.show(nextLink);
                    this.helper.show(nextLink);
                }
            }
            //this.enableKeyboardNav();
        }
    };
}

module.exports = Display;