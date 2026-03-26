const positionCalloutContainer = (leave = false) => {
    const calloutCtn = window.document.querySelectorAll('.sb-callout-ctn[data-type="side-menu"]');
    if (calloutCtn[0]) {
        const calloutCtnRect = calloutCtn[0].getBoundingClientRect();
        let calloutCtnRectY = calloutCtnRect.y,
            positionY = calloutCtnRectY + calloutCtnRect.height >= window.innerHeight;


        if (positionY && !leave) {
            calloutCtn[0].style.marginTop = -1 * ((calloutCtnRectY + calloutCtnRect.height) - window.innerHeight) + "px"
            //calloutCtn[0].setAttribute("data-position", "bottom")
        } else {
            calloutCtn[0].style.marginTop = "0px"
            //calloutCtn[0].removeAttribute("data-position")
        }

    }
}
window.onload = () => {
    positionCalloutContainer()
    window.addEventListener("resize", (event) => {
        positionCalloutContainer()
    });

    if (document.getElementById("toplevel_page_sb-instagram-feed")) {
        document.getElementById("toplevel_page_sb-instagram-feed").addEventListener("mouseenter", (event) => {
            if (!document.body.classList.contains('index-php')) {
                positionCalloutContainer()
            }
        });
        document.getElementById("toplevel_page_sb-instagram-feed").addEventListener("mouseleave", (event) => {
            if (!document.body.classList.contains('index-php')) {
                positionCalloutContainer(true)
            }
        });
    }
}