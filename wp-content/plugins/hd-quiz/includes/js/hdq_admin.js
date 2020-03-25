/*
	HD Quiz main admin script
*/

let hdq_has_changed_question_order = false;

jQuery(window).load(function() {
    console.log("HD Quiz initiated");
    hdq_start();
});

function hdq_start() {
    hdq_load_active_tab();
	if(jQuery("body").hasClass("post-type-post_type_questionna") && jQuery("body").hasClass("taxonomy-quiz")){
		// add warning to quiz taxonomy page
		let warning = '<div id = "hdq_quiz_tax_warning"><h2>WARNING</h2><p>Please note that deleting a quiz here will NOT delete any attached questions to it. You can delete questions in bulk by clicking the following button</p><a href = "./edit.php?post_type=post_type_questionna" class = "hdq_button4">DELETE QUESTIONS</a></div>';
		jQuery(".form-wrap").append(warning)
	}
}

// show the default tab on load
function hdq_load_active_tab() {
    var activeTab = jQuery("#hdq_tabs .hdq_active_tab").attr("data-hdq-content");
    jQuery("#" + activeTab).addClass("hdq_tab_active");
    jQuery(".hdq_tab_active").slideDown(500);
}

jQuery(".hdq_accordion h3").click(function() {
    jQuery(this).next("div").toggle(600);
});

/* Tab navigation
------------------------------------------------------- */
jQuery("#hdq_form_wrapper").on("click", "#hdq_tabs li", function(event) {
    jQuery('#hdq_tabs li').removeClass("hdq_active_tab");
    jQuery(this).addClass("hdq_active_tab");
    var hdqContent = jQuery(this).attr("data-hdq-content");
    jQuery(".hdq_tab_active").fadeOut();
    jQuery(".hdq_tab").removeClass("hdq_tab_active");
    jQuery("#" + hdqContent).delay(250).fadeIn();
    jQuery("#" + hdqContent).addClass("hdq_tab_active");
})


/* Show or hide answer images
------------------------------------------------------- */
jQuery("#hdq_form_wrapper").on("click", "#hdQue-post-class23", function(event) {	
    jQuery(".hdq_answer_as_image").toggleClass("hdq_use_image_as_answer");
});

/* Show or hide answers
------------------------------------------------------- */
jQuery("#hdq_form_wrapper").on("click", "#hdQue-post-class24", function(event) {		
    jQuery("#hdq_tab_wrapper").fadeToggle();
});

/* For now, only allow 1 correct answer at a time
------------------------------------------------------- */
jQuery("#hdq_form_wrapper").on("click", ".hdq_correct", function(event) {	
    jQuery(".hdq_correct").prop('checked', false);
    jQuery(this).prop('checked', true);
    //get index hdQue-post-class2
    let hdq_selected_val = jQuery(".hdq_correct").index(this) + 1;
    jQuery("#hdQue-post-class2").val(hdq_selected_val);
});

/* Upload a feature answer image
------------------------------------------------------- */
let hdq_file_frame_featured_image;
let hdq_file_frame_input = "";
jQuery("#hdq_form_wrapper").on("click", ".hdq_featured_image", function(event) {	

	let hdq_file_title = "Upload an image";
	let hdq_file_button = "SET IMAGE";
	
    // get the input to update
    hdq_file_frame_input = jQuery(this).next("input");
    hdq_file_frame_image = jQuery(".hdq_featured_image").index(this);
	
    // If the media frame already exists, reopen it.
    if (hdq_file_frame_featured_image) {
        hdq_file_frame_featured_image.open();
        return;
    }	
    // Create the media frame.
    hdq_file_frame_featured_image = wp.media.frames.file_frame = wp.media({
        title: hdq_file_title,
        button: {
            text: hdq_file_button,
        },
        multiple: false
    });

    // When an image is selected, run a callback.
    hdq_file_frame_featured_image.on('select', function() {
        attachment = hdq_file_frame_featured_image.state().get('selection').first().toJSON();
        imgURL = attachment.sizes.thumbnail.url;
		imgURLfull = attachment.sizes.full.url;
		image_to_update = jQuery('.hdq_featured_image img').eq(hdq_file_frame_image);
		if(jQuery(image_to_update).hasClass("hdq_question_featured")){
			jQuery(image_to_update).attr("src", imgURLfull);
		} else {
        	jQuery(image_to_update).attr("src", imgURL);
		}
		jQuery(hdq_file_frame_input).val(attachment.id);		
		if(jQuery("body").hasClass("toplevel_page_hdq_quizzes")){
			jQuery(hdq_file_frame_input).prev().attr("data-id", attachment.id)
		}
		if(typeof(jQuery(hdq_file_frame_input).attr("id")) === "undefined"){
			// looks like this is the featured image
			jQuery("#hdq_featured_image").attr("data-id", attachment.id)
		}
    });
    hdq_file_frame_featured_image.open();
});

function hdq_scroll_to_top(){
	jQuery('html').animate({
    	scrollTop: 0
	}, 'slow');	
}

// start loading stuff
function hdq_start_load(){
	jQuery("#hdq_message").fadeOut();
	jQuery("#hdq_loading ").fadeIn();
}
// after stuff has loaded
function hdq_after_load(editor = false){
	
	jQuery("#hdq_loading ").delay(600).fadeOut();	
	hdq_has_changed_question_order = false;
	hdq_load_active_tab();
	hdq_scroll_to_top();	
	hdf_start_sortable();
	if(editor){
		tinyMCE.execCommand('mceRemoveEditor', false, 'hdQue-post-class26');
		tinyMCE.execCommand('mceRemoveEditor', false, 'hd_quiz_term_meta_passText');
		tinyMCE.execCommand('mceRemoveEditor', false, 'hd_quiz_term_meta_failText');		
		setTimeout(function() {			
			// there HAS to be a better way... right?
			tinyMCE.init({
				mode: "textareas",
				theme: "modern",
				skin: "lightgray",
				language: "en",
				formats: {
					alignleft: [{
						selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
						styles: {
							textAlign: "left"
						}
					}, {
						selector: "img,table,dl.wp-caption",
						classes: "alignleft"
					}],
					aligncenter: [{
						selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
						styles: {
							textAlign: "center"
						}
					}, {
						selector: "img,table,dl.wp-caption",
						classes: "aligncenter"
					}],
					alignright: [{
						selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
						styles: {
							textAlign: "right"
						}
					}, {
						selector: "img,table,dl.wp-caption",
						classes: "alignright"
					}],
					strikethrough: {
						inline: "del"
					}
				},
				relative_urls: false,
				remove_script_host: false,
				convert_urls: false,
				browser_spellcheck: true,
				fix_list_elements: true,
				entities: "38,amp,60,lt,62,gt",
				entity_encoding: "raw",
				keep_styles: false,
				resize: true,
				menubar: false,
				branding: false,
				preview_styles: "font-family font-size font-weight font-style text-decoration text-transform",
				end_container_on_empty_block: true,
				wpeditimage_html5_captions: true,
				wp_lang_attr: "en-US",
				wp_keep_scroll_position: true,
				wp_shortcut_labels: {
					"Heading 1": "access1",
					"Heading 2": "access2",
					"Heading 3": "access3",
					"Heading 4": "access4",
					"Heading 5": "access5",
					"Heading 6": "access6",
					"Paragraph": "access7",
					"Blockquote": "accessQ",
					"Underline": "metaU",
					"Strikethrough": "accessD",
					"Bold": "metaB",
					"Italic": "metaI",
					"Code": "accessX",
					"Align center": "accessC",
					"Align right": "accessR",
					"Align left": "accessL",
					"Justify": "accessJ",
					"Cut": "metaX",
					"Copy": "metaC",
					"Paste": "metaV",
					"Select all": "metaA",
					"Undo": "metaZ",
					"Redo": "metaY",
					"Bullet list": "accessU",
					"Numbered list": "accessO",
					"Insert\/edit image": "accessM",
					"Remove link": "accessS",
					"Toolbar Toggle": "accessZ",
					"Insert Read More tag": "accessT",
					"Insert Page Break tag": "accessP",
					"Distraction-free writing mode": "accessW",
					"Keyboard Shortcuts": "accessH"
				},			
				plugins: "charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview",			
				wpautop: true,
				indent: false,
				toolbar1: "formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,dfw,wp_adv",
				toolbar2: "strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
				toolbar3: "",
				toolbar4: "",
				tabfocus_elements: "content-html,save-post",
				wp_autoresize_on: true,
				add_unload_trigger: false,
				block_formats: "Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Code=code"
			});				
		}, 1200); // give it time to re-init
	}
}

// show message box
function hdq_show_message(message){
		jQuery("#hdq_message").html(message);		
		jQuery("#hdq_message").fadeIn();	
}

// hide message
jQuery("#hdq_wrapper").on("click", "#hdq_message", function(event) {
	jQuery("#hdq_message").fadeOut();
});


function hdf_start_sortable() {
    jQuery("#hdq_quiz_question_list").sortable({
        placeholder: "sorting_placeholder",
        items: ".hdq_quiz_question",
        distance: 15, // sets the drag tolerance to something more acceptable
		update: function(event, ui) {
			hdq_has_changed_question_order = true;
		}
    });
}

// view quiz
jQuery("#hdq_form_wrapper").on("click", ".hdq_quiz_term, #hdq_back_to_quiz", function(event) {
	if (event.target !== this){
		// allow users to copy / paste shortcode
    	return;	
	}	
	hdq_start_load();
	let quiz_id = jQuery(this).attr("data-id");

    jQuery.ajax({
        type: 'POST',
        data: {
            action: "hdq_view_quiz",
            hdq_quizzes_nonce: jQuery("#hdq_quizzes_nonce").val(),
			quiz_id: quiz_id
        },
        url: ajaxurl,
        success: function(data) {
            jQuery("#hdq_form_wrapper").html(data);
        },
        error: function() {
            console.log("Permission denied");
        },
        complete: function() {
			hdq_after_load(true);
        }
    });
});


// update quiz
jQuery("#hdq_form_wrapper").on("click", "#hdq_save_quiz", function(event) {	
	hdq_start_load();
	
	let quiz_id = jQuery(this).attr("data-id");
	
	// get list of all questions and their menu_order
	let questions = [];
	let menu_number = 0;
	if(hdq_has_changed_question_order){
		jQuery(".hdq_quiz_question").each(function(){
			let question = [];
			let id = jQuery(this).attr("data-id");
			question = [id, menu_number];
			questions.push(question);
			menu_number = menu_number + 1;
		});	
	}
	questions = JSON.stringify(questions);
	
	// get settings data
	tinyMCE.triggerSave();
	let passPercent = jQuery("#hdq_quiz_pass_percent").val();
	let passText = jQuery("#hd_quiz_term_meta_passText").val();
	let failText = jQuery("#hd_quiz_term_meta_failText").val();
	let hdq_share_results = jQuery("#hdq_share_results").prop("checked");
	let hdq_results_position = jQuery("#hdq_results_position").prop("checked");
	let hdq_show_results = jQuery("#hdq_show_results").prop("checked");
	let hdq_show_results_correct = jQuery("#hdq_show_results_correct").prop("checked");
	let hdq_show_extra_text = jQuery("#hdq_show_extra_text").prop("checked");
	let hdq_quiz_timer = jQuery("#hdq_quiz_timer").val();
	let hdq_randomize_question_order = jQuery("#hdq_randomize_question_order").prop("checked");
	let hdq_randomize_answer_order = jQuery("#hdq_randomize_answer_order").prop("checked");
	let hdq_pool_of_questions = jQuery("#hdq_pool_of_questions").val();
	let hdq_wp_paginate = jQuery("#hdq_wp_paginate").val();
	let hdq_immediate_mark = jQuery("#hdq_immediate_mark").prop("checked");
	let hdq_stop_answer_reselect = jQuery("#hdq_stop_answer_reselect").prop("checked");
	
	// set defaults for checkboxes so older versions are compatible	
	if(hdq_share_results){
		hdq_share_results = "yes";
	} else {
		hdq_share_results = "no";
	}
	if(hdq_results_position){
		hdq_results_position = "yes";
	} else {
		hdq_results_position = "no";
	}
	if(hdq_show_results){
		hdq_show_results = "yes";
	} else {
		hdq_show_results = "no";
	}
	if(hdq_show_results_correct){
		hdq_show_results_correct = "yes";
	} else {
		hdq_show_results_correct = "no";
	}
	if(hdq_show_extra_text){
		hdq_show_extra_text = "yes";
	} else {
		hdq_show_extra_text = "no";
	}
	if(hdq_randomize_question_order){
		hdq_randomize_question_order = "yes";
	} else {
		hdq_randomize_question_order = "no";
	}
	if(hdq_randomize_answer_order){
		hdq_randomize_answer_order = "yes";
	} else {
		hdq_randomize_answer_order = "no";
	}	
	if(hdq_immediate_mark){
		hdq_immediate_mark = "yes";
	} else {
		hdq_immediate_mark = "no";
	}
	if(hdq_stop_answer_reselect){
		hdq_stop_answer_reselect = "yes";
	} else {
		hdq_stop_answer_reselect = "no";
	}
	
	
    jQuery.ajax({
        type: 'POST',
        data: {
            action: "hdq_save_quiz",
            hdq_quizzes_nonce: jQuery("#hdq_quizzes_nonce").val(),
			quiz_id: quiz_id,
			questions: questions,
			passPercent: passPercent,
			passText: passText,
			failText: failText,
			hdq_share_results: hdq_share_results,
			hdq_results_position: hdq_results_position,
			hdq_show_results: hdq_show_results,
			hdq_show_results_correct: hdq_show_results_correct,
			hdq_show_extra_text: hdq_show_extra_text,
			hdq_quiz_timer: hdq_quiz_timer,
			hdq_randomize_question_order: hdq_randomize_question_order,
			hdq_randomize_answer_order: hdq_randomize_answer_order,
			hdq_pool_of_questions: hdq_pool_of_questions,
			hdq_wp_paginate: hdq_wp_paginate,
			hdq_immediate_mark: hdq_immediate_mark,
			hdq_stop_answer_reselect:hdq_stop_answer_reselect
        },
        url: ajaxurl,
        success: function(data) {
            if(data == "done"){
				hdq_show_message("<p>This quiz has been successfully updated.</p>");
			} else {
				hdq_show_message("<p>There was an issue updating this quiz</p>");
			}
        },
        error: function() {
            console.log("Permission denied");
			hdq_show_message("<p>There was an issue updating this quiz</p>");
        },
        complete: function() {
			hdq_after_load();
        }
    });	
});

// view question
jQuery("#hdq_form_wrapper").on("click", ".hdq_quiz_question, #hdq_add_question", function(event) {	
	hdq_start_load();
	let question_id = jQuery(this).attr("data-id");
	let quiz_id = jQuery(this).attr("data-quiz-id");
    jQuery.ajax({
        type: 'POST',
        data: {
            action: "hdq_view_question",
            hdq_quizzes_nonce: jQuery("#hdq_quizzes_nonce").val(),
			question_id: question_id,
			quiz_id: quiz_id
        },
        url: ajaxurl,
        success: function(data) {
            jQuery("#hdq_form_wrapper").html(data);
        },
        error: function() {
            console.log("Permission denied");
        },
        complete: function() {
			hdq_after_load(true);
			let quiz_id = jQuery("#hdq_back_to_quiz").attr("data-id");
			jQuery('#term_' + quiz_id).prop("checked", true);
			
        }
    });
});

// add new quiz
let hdq_enter_notification = false;
jQuery("#hdq_form_wrapper").on("keyup", "#hdq_new_quiz_name", function(e) {
	e.preventDefault();
    if(e.which == 13) {
		hdq_enter_notification = false;
		hdq_start_load();
		let hdq_quiz_name = jQuery("#hdq_new_quiz_name").val();
		if(hdq_quiz_name.length > 1){
			jQuery(".hdq_input_notification").fadeOut();
			jQuery.ajax({
				type: 'POST',
				data: {
					action: "hdq_add_new_quiz",
					hdq_quizzes_nonce: jQuery("#hdq_quizzes_nonce").val(),
					hdq_new_quiz: hdq_quiz_name,
				},
				url: ajaxurl,
				success: function(data) {
					let new_quiz = '<div class="hdq_quiz_item hdq_quiz_term" data-id="'+data+'">'+hdq_quiz_name+' <code>[HDquiz quiz = "'+data+'"]</code></div>';
					jQuery("#hdq_list_quizzes").prepend(new_quiz);
					jQuery("#hdq_new_quiz_name").val("");
					hdq_show_message("<p>"+hdq_quiz_name+" has been added. Please select it below to edit quiz settings and add questions.</p>");
				},
				error: function() {
					console.log("Permission denied");
				},
				complete: function() {
					hdq_after_load(true);
				}
			});
		}
	} else {
		let content = jQuery(this).val();
		if (content != "" && content != null) {
			hdq_press_enter_notificiation("#hdq_new_quiz_name");
		} else {
			jQuery(".hdq_input_notification").fadeOut();
			hdq_enter_notification = false;
		}
	}
});


function hdq_press_enter_notificiation(elem) {
	if (!hdq_enter_notification) {
		hdq_enter_notification = true;
		setTimeout(function() {
			let content = jQuery(elem).val();
			if (content != "" && content != null) {
				jQuery(elem).next(".hdq_input_notification").fadeIn();
			}
		}, 3000);
	}
}

/* Save a question
------------------------------------------------------- */

// if save_current_question is clicked on
jQuery("#hdq_wrapper").on("click", "#hdq_save_question", function(){
	// check if a title has been entered
	if(jQuery("#hdq_question_title").val() != "" && jQuery("#hdq_question_title").val() != null){
		hdq_get_question_values(false);
	} else {
		alert("please enter a title before saving")
	}
});

// if hdq_delete_question is clicked on
jQuery("#hdq_wrapper").on("click", "#hdq_delete_question", function(){
	// check if a title has been entered
	let questionID = jQuery("#hdq_delete_question").attr("data-id");
	data = '<p><strong>WARNING</strong>: This will permanently delete this question.\
	<div class="hdq_button4" data-id="'+questionID+'" id="hdq_delete_question_final">\
			<span class="dashicons dashicons-sticky"></span> DELETE\
		</div>\
	</p>';
	hdq_show_message(data);
});
jQuery("#hdq_wrapper").on("click", "#hdq_delete_question_final", function(){
	let question_id = jQuery("#question_id").val();
	jQuery.ajax({
        type: 'POST',
        data: {
            action: "hdq_delete_question",
            hdq_quizzes_nonce: jQuery("#hdq_quizzes_nonce").val(),
			question_id: question_id
        },
        url: ajaxurl,
        success: function(data) {
			jQuery("#hdq_back_to_quiz").click();
        },
        error: function() {
            console.log("Permission denied");
        },
        complete: function() {
			hdq_after_load(true);
        }
    });	
});

// get array of attached quizzes
function get_quiz_ids(){
	let quiz_ids = [];
	jQuery("#hdq_category_list input").each(function(){
		if(jQuery(this).prop('checked')){
			quiz_ids.push(jQuery(this).attr("data-term"));
		}
	});
	return quiz_ids;
}	


// get all values of the question
function hdq_get_question_values(isNew){
	tinyMCE.triggerSave();
	let quiz_ids = get_quiz_ids();		
	let question_id = jQuery("#question_id").val();
	let title = jQuery("#hdq_question_title").val();
	let image_based_answers = jQuery("#hdQue-post-class23").prop("checked");	
	if(image_based_answers){
		image_based_answers = "yes";
	} else {
		image_based_answers = "no";
	}
	let question_as_title = jQuery("#hdQue-post-class24").prop("checked");
	if(question_as_title){
		question_as_title = "yes";
	} else {
		question_as_title = "no";
	}
	let paginate = jQuery("#hdQue-post-class25").prop("checked");
	if(paginate){
		paginate = "yes";
	} else {
		paginate = "no";
	}
	let answers = [];
	jQuery("#hdq_tab_content .hdq_table tr").each(function(){		
		let answer_id = jQuery(this).children("td").children(".hdq_input").attr("id");
		let answer_val = jQuery(this).children("td").children(".hdq_input").val();
		let answer_image_meta = "";
		let answer_image_val = "";
		if(image_based_answers === "yes"){
			answer_image_val = jQuery(this).children("td").children(".hdq_featured_image").attr("data-id");
			if(answer_image_val == 0){
				answer_image_val = jQuery(this).children("td.hdq_use_image_as_answer").children("input").val();
			}
			answer_image_meta = jQuery(this).children("td.hdq_use_image_as_answer").children("input").attr("id");
			if( typeof(answer_image_meta) === "undefined"){
				answer_image_meta = "";
			}
		}		
		answers.push([answer_id, answer_val, answer_image_meta, answer_image_val]);				
	});
	
	answers = JSON.stringify(answers);
	let answer_correct = jQuery("#hdQue-post-class2").val();
	let featured_image = jQuery("#hdq_featured_image").attr("data-id");
	let tooltip = jQuery("#hdQue-post-class12").val();
	let extra_text = jQuery("#hdQue-post-class26").val();	
	
	jQuery.ajax({
        type: 'POST',
        data: {
            action: "hdq_save_question",
            hdq_quizzes_nonce: jQuery("#hdq_quizzes_nonce").val(),
			question_id: question_id,
			quiz_ids: quiz_ids,
			title: title,
			image_based_answers: image_based_answers,
			question_as_title: question_as_title,
			paginate: paginate,
			answers: answers,
			answer_correct: answer_correct,
			featured_image: featured_image,
			tooltip: tooltip,
			extra_text: extra_text
        },
        url: ajaxurl,
        success: function(data) {
			let dataSuccess = data.split("|");
			if (typeof(dataSuccess) == "undefined"){
				console.log(data``)
			} else {
            	if(dataSuccess[0] == "updated"){
					data = '<p>This question has been successfully updated</p>';
					hdq_show_message(data);
					// set the question ID so saving again doesn't create a new question
					jQuery("#hdq_save_question").attr("data-id", dataSuccess[1]);
					jQuery("#question_id").val(dataSuccess[1]);
				} else {
					console.log(data);
				}
			}
        },
        error: function() {
            console.log("Permission denied");
        },
        complete: function() {
			hdq_after_load(true);
        }
    });
}