let current_fs, next_fs, previous_fs; //fieldsets
let left, opacity, scale; //fieldset properties which we will animate
let animating, percentAnimate; //flag to prevent quick multi-click glitches  

function aysAnimateStep(animation, current_fs, next_fs){
    
    if(typeof next_fs !== "undefined"){
        switch(animation){
            case "lswing":
                current_fs.parents('.ays-questions-container').css({
                    perspective: '800px',
                });
                
                current_fs.addClass('swing-out-right-bck');
                current_fs.css({
                    'pointer-events': 'none'
                });
                setTimeout(function(){
                    current_fs.css({
                        'position': 'absolute',
                    });
                    next_fs.css('display', 'flex');
                    next_fs.addClass('swing-in-left-fwd');
                },400);
                setTimeout(function(){
                    current_fs.hide();
                    current_fs.css({
                        'pointer-events': 'auto',
                        'position': 'static'
                    });
                    next_fs.css({
                        'position':'relative',
                        'pointer-events': 'auto'
                    });
                    current_fs.removeClass('swing-out-right-bck');                    
                    next_fs.removeClass('swing-in-left-fwd');
                    animating = false;
                },1000);
            break;
            case "rswing":
                current_fs.parents('.ays-questions-container').css({
                    perspective: '800px',
                });
                
                current_fs.addClass('swing-out-left-bck');
                current_fs.css({
                    'pointer-events': 'none'
                });
                setTimeout(function(){
                    current_fs.css({
                        'position': 'absolute',
                    });
                    next_fs.css('display', 'flex');
                    next_fs.addClass('swing-in-right-fwd');
                },400);
                setTimeout(function(){
                    current_fs.hide();
                    current_fs.css({
                        'pointer-events': 'auto',
                        'position': 'static'
                    });
                    next_fs.css({
                        'position':'relative',
                        'pointer-events': 'auto'
                    });
                    current_fs.removeClass('swing-out-left-bck');                    
                    next_fs.removeClass('swing-in-right-fwd');
                    animating = false;
                },1000);
            break;
            case "shake":
                current_fs.animate({opacity: 0}, {
                    step: function (now, mx) {
                        scale = 1 - (1 - now) * 0.2;
                        left = (now * 50) + "%";
                        opacity = 1 - now;
                        current_fs.css({
                            'transform': 'scale(' + scale + ')',
                            'position': 'absolute',
                            'top':0,
                            'opacity': 1,
                            'pointer-events': 'none'
                        });
                        next_fs.css({
                            'left': left, 
                            'opacity': opacity,
                            'display':'flex',
                            'position':'relative',
                            'pointer-events': 'none'
                        });
                    },
                    duration: 800,
                    complete: function () {
                        current_fs.hide();
                        current_fs.css({                        
                            'pointer-events': 'auto',
                            'opacity': 1,
                            'position': 'static'
                        });
                        next_fs.css({
                            'display':'flex',
                            'position':'relative',
                            'transform':'scale(1)',
                            'opacity': 1,
                            'pointer-events': 'auto'
                        });
                        animating = false;
                    },
                    easing: 'easeInOutBack'
                });
            break;
            case "fade":
                current_fs.animate({opacity: 0}, {
                    step: function (now, mx) {
                        opacity = 1 - now;
                        current_fs.css({
                            'position': 'absolute',
                            'pointer-events': 'none'
                        });
                        next_fs.css({
                            'opacity': opacity,
                            'position':'relative',
                            'display':'flex',
                            'pointer-events': 'none'
                        });
                    },
                    duration: 500,
                    complete: function () {
                        current_fs.hide();
                        current_fs.css({                        
                            'pointer-events': 'auto',
                            'position': 'static'
                        });
                        next_fs.css({
                            'display':'flex',
                            'position':'relative',
                            'transform':'scale(1)',
                            'opacity': 1,
                            'pointer-events': 'auto'
                        });
                        animating = false;
                    }
                });
            break;
            default:            
                current_fs.animate({}, {
                    step: function (now, mx) {
                        current_fs.css({
                            'pointer-events': 'none'
                        });
                        next_fs.css({
                            'position':'relative',
                            'pointer-events': 'none'
                        });
                    },
                    duration: 0,
                    complete: function () {
                        current_fs.hide();
                        current_fs.css({                        
                            'pointer-events': 'auto'
                        });
                        next_fs.css({
                            'display':'flex',
                            'position':'relative',
                            'transform':'scale(1)',
                            'pointer-events': 'auto'
                        });
                        animating = false;
                    }
                });
            break;
        }
    }else{
        switch(animation){
            case "lswing":
                current_fs.parents('.ays-questions-container').css({
                    perspective: '800px',
                });                
                current_fs.addClass('swing-out-right-bck');
                current_fs.css({
                    'pointer-events': 'none'
                });
                setTimeout(function(){
                    current_fs.css({
                        'position': 'absolute',
                    });
                },400);
                setTimeout(function(){
                    current_fs.hide();
                    current_fs.css({
                        'pointer-events': 'auto',
                        'position': 'static'
                    });
                    current_fs.removeClass('swing-out-right-bck');  
                    animating = false;
                },1000);
            break;
            case "rswing":
                current_fs.parents('.ays-questions-container').css({
                    perspective: '800px',
                });                
                current_fs.addClass('swing-out-left-bck');
                current_fs.css({
                    'pointer-events': 'none'
                });
                setTimeout(function(){
                    current_fs.css({
                        'position': 'absolute',
                    });
                },400);
                setTimeout(function(){
                    current_fs.hide();
                    current_fs.css({
                        'pointer-events': 'auto',
                        'position': 'static'
                    });
                    current_fs.removeClass('swing-out-left-bck');
                    animating = false;
                },1000);
            case "shake":
                current_fs.animate({opacity: 0}, {
                    step: function (now, mx) {
                        scale = 1 - (1 - now) * 0.2;
                        left = (now * 50) + "%";
                        opacity = 1 - now;
                        current_fs.css({
                            'transform': 'scale(' + scale + ')',
                        });
                    },
                    duration: 800,
                    complete: function () {
                        current_fs.hide();
                        animating = false;
                    },
                    easing: 'easeInOutBack'
                });
            break;
            case "fade":
                current_fs.animate({opacity: 0}, {
                    step: function (now, mx) {
                        opacity = 1 - now;
                    },
                    duration: 500,
                    complete: function () {
                        current_fs.hide();
                        animating = false;
                    },
                    easing: 'easeInOutBack'
                });
            break;
            default:
                current_fs.animate({}, {
                    step: function (now, mx) {
                        
                    },
                    duration: 0,
                    complete: function () {
                        current_fs.hide();
                        animating = false;
                    }
                });
            break;
        }
    }
}



/**
 * @return {string}
 */
function GetFullDateTime(){
    let now = new Date();
    return [[now.getFullYear(), AddZero(now.getMonth() + 1), AddZero(now.getDate())].join("-"), [AddZero(now.getHours()), AddZero(now.getMinutes()), AddZero(now.getSeconds())].join(":")].join(" ");
}

/**
 * @return {string}
 */
function AddZero(num) {
    return (num >= 0 && num < 10) ? "0" + num : num + "";
}

/**
 * @return {string}
 */
function aysEscapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>\"']/g, function(m) { return map[m]; });
}


function audioVolumeIn(q){
    if(q.volume){
        var InT = 0;
        var setVolume = 1; // Target volume level for new song
        var speed = 0.05; // Rate of increase
        q.volume = InT;
        var eAudio = setInterval(function(){
            InT += speed;
            q.volume = InT.toFixed(1);
            if(InT.toFixed(1) >= setVolume){
                q.volume = 1;
                clearInterval(eAudio);
                //alert('clearInterval eAudio'+ InT.toFixed(1));
            };
        },50);
    };
};

function audioVolumeOut(q){
    if(q.volume){
        var InT = 1;
        var setVolume = 0;  // Target volume level for old song 
        var speed = 0.05;  // Rate of volume decrease
        q.volume = InT;
        var fAudio = setInterval(function(){
            InT -= speed;
            q.volume = InT.toFixed(1);
            if(InT.toFixed(1) <= setVolume){
                clearInterval(fAudio);
                //alert('clearInterval fAudio'+ InT.toFixed(1));
            };
        },50);
    };
};

function isPlaying(audelem) {
    return !audelem.paused; 
}

function resetPlaying(audelems) {
    for(var i = 0; i < audelems.length; i++){
        audelems[i].pause();
        audelems[i].currentTime = 0;
    }
    // return !audelem.paused;
}
