
// Progress Bar color selection JS
window.addEventListener(
    'load', function () {

        bsf_rt_onloadCheck();
    }
);

function bsf_rt_onloadCheck()
{ 
    if (document.getElementById("bsf_rt_position_of_progress_bar") !== null) {

          document.getElementById("bsf_rt_position_of_progress_bar").addEventListener('change',bsf_rt_Progressbarpositioncheck);
  
    }
    if (document.getElementById("bsf_rt_progress_bar_styles") !== null) {

          document.getElementById("bsf_rt_progress_bar_styles").addEventListener('change',bsf_rt_ColorSelectCheck_two);
  
    }
    if (document.getElementById("bsf_rt_position_of_read_time") !== null) {

          document.getElementById("bsf_rt_position_of_read_time").addEventListener('change',bsf_rt_readtimepositioncheck);
  
    }
   
}

function bsf_rt_ColorSelectCheck_two()
{

    if(this) {

        if('Gradient' == this.value) {

            document.getElementById("gradiant-wrap2").style.display = "table-row";
        }
        else{

            document.getElementById("gradiant-wrap2").style.display = "none";
        }
    }
}

function bsf_rt_Progressbarpositioncheck()
{

    if(this) {

        if(this.value !== 'none') {

            document.getElementById("bsf-rt-progress-bar-options").style.display = "block";

        }
        else{

            document.getElementById("bsf-rt-progress-bar-options").style.display = "none";

        }
    }
}

function bsf_rt_readtimepositioncheck()
{
 
    if(this) {

        if(this.value !== 'none') {

            document.getElementById("bsf_rt_read_time_option").style.display = "block";

        }
        else{

            document.getElementById("bsf_rt_read_time_option").style.display = "none";

        }
    }

}