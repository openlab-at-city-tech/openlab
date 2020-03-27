/*
	HD Quiz main script
*/

hdq_local_vars = JSON.parse(hdq_local_vars);
let hdq_pass_percent = hdq_local_vars.hdq_pass_percent;
let hdq_quiz_id = hdq_local_vars.hdq_quiz_id;
let hdq_quiz_name = hdq_local_vars.hdq_quiz_name;
let hdq_quiz_permalink = hdq_local_vars.hdq_quiz_permalink;
let hdq_share_results = hdq_local_vars.hdq_share_results;
let hdq_show_answer_text = hdq_local_vars.hdq_show_answer_text;
let hdq_show_correct = hdq_local_vars.hdq_show_correct;
let hdq_show_what_answers_were_right_wrong = hdq_local_vars.hdq_show_what_answers_were_right_wrong;
let hdq_timer = hdq_local_vars.hdq_timer;
let hdq_twitter_handle = hdq_local_vars.hdq_twitter_handle;
let hdq_immediate_mark = hdq_local_vars.hdq_immediate_mark;
let hdq_stop_answer_reselect = hdq_local_vars.hdq_stop_answer_reselect;
let hdq_featured_image = hdq_local_vars.hdq_featured_image;
let hdq_score = [];

let jPage = 0;
let hdq_nextlink = "";
let hdq_active_timer = false;
let hdq_top = 0;
let hdq_pass_or_fail = "";

jQuery(window).load(function() {
	console.log("HD Quiz initiated");
	hdq_start();
});

function hdq_start() {
	jQuery(".hdq_results_wrapper").hide();
	let firstjpage = jQuery(".hdq_jPaginate")[0];
	if (!jQuery(firstjpage).hasClass("hdq_finish")) {
		jQuery(".hdq_finish").hide();
		jQuery(".hdq_jPaginate")
			.first()
			.fadeIn();
		jQuery(".hdq_jPaginate")
			.nextAll(".hdq_question")
			.hide();
	} else {
		jQuery(".hdq_finish").show();
	}
	if (hdq_timer > 3) {
		hdq_active_timer = true;
		hdq_start_timer();
	}
}

/* Quiz Timer
------------------------------------------------------- */
function hdq_start_timer() {
	function hdq_decrease_timer() {
		if (hdq_timer > 0 && hdq_active_timer == true) {
			let minutes = parseInt(hdq_timer / 60);
			minutes = minutes < 10 ? "0" + minutes : minutes;
			let seconds = hdq_timer % 60;
			seconds = seconds < 10 ? "0" + seconds : seconds;
			let t = minutes + ":" + seconds;
			jQuery(".hdq_timer").html(t);
			if (hdq_timer > 10 && hdq_timer < 30) {
				jQuery(".hdq_timer").addClass("hdq_timer_warning");
			} else if (hdq_timer <= 10) {
				jQuery(".hdq_timer").removeClass("hdq_timer_warning");
				jQuery(".hdq_timer").addClass("hdq_timer_danger");
			}
			hdq_timer = hdq_timer - 1;
			setTimeout(hdq_decrease_timer, 1000);
		} else {
			if (hdq_active_timer == true) {
				// uh oh! Out of time
				jQuery(".hdq_timer").html("0");
				jQuery(".hdq_timer").removeClass("hdq_timer_danger");
				jQuery(".hdq_finsh_button").click();
				hdq_active_timer = false;
			} else {
				// user finished in time
				jQuery(".hdq_timer").removeClass("hdq_timer_danger");
				jQuery(".hdq_timer").removeClass("hdq_timer_warning");
			}
		}
	}
	hdq_decrease_timer();
}

/* 0nly allow 1 correct answer for a question
------------------------------------------------------- */
jQuery(".hdq_label_answer").click(function() {
	// get the parent id
	hdq_question_id = jQuery(this).attr("data-id");
	if (
		jQuery("#" + hdq_question_id + " .hdq_label_answer")
			.children(".hdq-options-check")
			.children(".hdq_check_input")
			.is(":enabled")
	) {
		jQuery("#" + hdq_question_id + " .hdq_label_answer")
			.children(".hdq-options-check")
			.children(".hdq_check_input")
			.prop("checked", false);
		jQuery(this)
			.children(".hdq-options-check")
			.children(".hdq_check_input")
			.prop("checked", true);

		if (hdq_stop_answer_reselect === "yes" || hdq_immediate_mark === "yes") {
			// disable all inputs
			jQuery(
				jQuery("#" + hdq_question_id + " .hdq_label_answer")
					.children(".hdq-options-check")
					.children(".hdq_check_input")
			).each(function() {
				this.disabled = true;
			});
		}

		if (hdq_immediate_mark === "yes") {
			let inp = null;
			if (jQuery("#" + hdq_question_id).hasClass("hdq_question_images")) {
				inp = jQuery(jQuery(this)[0]).find(".hdq_check_input")[0];
			} else {
				inp = jQuery(this)[0].children[0].children[0];
			}
			if (inp.value == 1) {
				jQuery(this).addClass("hdq_correct");
			} else {
				jQuery(this).addClass("hdq_wrong");
			}

			if (hdq_show_what_answers_were_right_wrong === "yes") {
				jQuery(
					jQuery("#" + hdq_question_id + " .hdq_label_answer")
						.children(".hdq-options-check")
						.children(".hdq_check_input")
				).each(function() {
					if (this.value == 1 && !this.parentNode.parentNode.classList.contains("hdq_correct")) {
						this.parentNode.parentNode.classList.add("hdq_correct_not_selected");
					}
				});
			}

			jQuery(jQuery("#" + hdq_question_id))
				.find(".hdq_question_after_text")
				.fadeIn();
		}
	}
});

jQuery(window).load(function() {
	// when an answer is selected
	jQuery(".hdq_label_answer2").click(function() {
		// check if this question has already been answered
		let p = jQuery(this)
			.parent()
			.parent();
		if (jQuery(p).hasClass("hdq_answered")) {
			return false;
		} else {
			jQuery(p).addClass("hdq_answered");
			// check to see if answer was right
			if (jQuery(this)[0].children[0].children[0].value == 1) {
				jQuery(p).addClass("hdq_answered_correct");
				jQuery(this).addClass("hdq_correct");
			} else {
				jQuery(p).addClass("hdq_answered_incorrect");
				jQuery(this).addClass("hdq_wrong");
			}
			jQuery(p)
				.find(".hdq_question_after_text")
				.fadeIn();

			let inp = jQuery(p).find(".hdq_check_input");
			for (let i = 0; i < inp.length; i++) {
				inp[i].disabled = true;
				if (inp[i].value == 1 && !inp[i].classList.contains("hdq_correct")) {
					inp[i].parentNode.parentNode.classList.add("hdq_correct_not_selected");
				}
			}
		}
	});
});

/* WP-Pagination
------------------------------------------------------- */
jQuery(".hdq_next_page_button").click(function(e) {
	jQuery(this).fadeOut();
	// update the next page link and attributes
	let hdq_current_score = jQuery("#hdq_current_score").val(); // get page load values
	let hdq_total_questions = jQuery("#hdq_total_questions").val(); // get page load values
	if (!hdq_current_score) {
		hdq_current_score = 0;
	}
	if (!hdq_total_questions) {
		hdq_total_questions = 0;
	}
	// add any correct answer to score
	jQuery(".hdq_option").each(function() {
		if (jQuery(this).val() == 1 && jQuery(this).prop("checked")) {
			hdq_current_score = parseInt(hdq_current_score) + 1;
		}
	});

	// get how many new questions are on this page, excluding titles
	let total_questions_on_page = jQuery(".hdq_question").length - jQuery(".hdq_question_title").length - 1;
	hdq_total_questions = parseInt(hdq_total_questions) + parseInt(total_questions_on_page);

	let hdq_nextlink = jQuery(".hdq_next_page_button").attr("href");
	jQuery(".hdq_next_page_button").attr(
		"href",
		hdq_nextlink + hdq_current_score + "&totalQuestions=" + hdq_total_questions
	);
});

/* jPagination
------------------------------------------------------- */
jQuery(".hdq_jPaginate .hdq_next_button").click(function() {
	let hdq_form_id = jQuery(this).attr("data-id");
	jQuery(".hdq_jPaginate .hdq_next_button").removeClass("hdq_next_selected");
	jQuery(this).addClass("hdq_next_selected");

	jQuery("#hdq_" + hdq_form_id + " .hdq_jPaginate:visible")
		.prevAll("#hdq_" + hdq_form_id + " .hdq_question")
		.hide();
	jQuery("#hdq_" + hdq_form_id + " .hdq_jPaginate:eq(" + parseInt(jPage) + ")")
		.nextUntil("#hdq_" + hdq_form_id + " .hdq_jPaginate ")
		.show();
	jPage = parseInt(jPage + 1);

	jQuery(this)
		.parent()
		.hide();
	jQuery("#hdq_" + hdq_form_id + " .hdq_jPaginate:eq(" + parseInt(jPage) + ")").show();
	setTimeout(function() {
		let hdq_quiz_container = document.querySelector("#hdq_" + hdq_form_id);
		hdq_quiz_container = jQuery(hdq_get_quiz_parent_container(hdq_quiz_container));

		if (hdq_quiz_container[0].tagName === "DIV") {
			hdq_top =
				jQuery(hdq_quiz_container).scrollTop() +
				jQuery(".hdq_question:visible").offset().top -
				jQuery(".hdq_question:visible").height() / 2 -
				40;
			jQuery(hdq_quiz_container).animate(
				{
					scrollTop: hdq_top
				},
				550
			);
		} else {
			let overflowH = jQuery("html").css("overflow");
			let overflowB = jQuery("body").css("overflow");
			let rest = false;
			if (overflowH.indexOf("hidden") >= 0 || overflowB.indexOf("hidden") >= 0) {
				rest = true;
			}

			jQuery("html,body").css("overflow", "initial");

			jQuery("html,body").animate(
				{
					scrollTop: jQuery(".hdq_question:visible").offset().top - 40
				},
				550
			);

			if (rest) {
				setTimeout(function() {
					jQuery("html").css("overflow", overflowH);
					jQuery("body").css("overflow", overflowB);
				}, 550);
			}
		}
	}, 50);
});

function hdq_get_quiz_parent_container(element, includeHidden) {
	var style = getComputedStyle(element);
	var excludeStaticParent = style.position === "absolute";
	var overflowRegex = includeHidden ? /(auto|scroll|hidden)/ : /(auto|scroll)/;

	if (style.position === "fixed") return document.body;
	for (var parent = element; (parent = parent.parentElement); ) {
		style = getComputedStyle(parent);
		if (excludeStaticParent && style.position === "static") {
			continue;
		}
		if (overflowRegex.test(style.overflow + style.overflowY + style.overflowX)) return parent;
	}
	return document.body;
}

/* FINISH
------------------------------------------------------- */
jQuery(".hdq_finsh_button").click(function() {
	hdq_active_timer = false;
	let hdq_form_id = jQuery(this).attr("data-id");
	jQuery(this).fadeOut();
	jQuery(".hdq_loading_bar").addClass("hdq_animate");

	function hdq_calculate_total(hdq_form_id) {
		// first, calculate the total
		let total_score = jQuery("#hdq_current_score").val(); // get page load values
		let total_questions = jQuery("#hdq_total_questions").val(); // get page load values
		if (!total_score) {
			total_score = 0;
		}
		if (!total_questions) {
			total_questions = 0;
		}
		// get score
		jQuery("#hdq_" + hdq_form_id + " .hdq_option").each(function() {
			if (jQuery(this).val() == 1 && jQuery(this).prop("checked")) {
				total_score = parseInt(total_score) + 1;
			}
		});

		// get total questions
		total_questions =
			parseInt(jQuery("#hdq_" + hdq_form_id + " .hdq_question").length) -
			parseInt(jQuery("#hdq_" + hdq_form_id + " .hdq_question_title").length) -
			1 +
			parseInt(total_questions);

		let data = total_score + " / " + total_questions;
		hdq_score = [total_score, total_questions];

		if (jQuery("#hdq_" + hdq_form_id + " .hdq_results_inner .hdq_result .hdq_result_percent")[0]) {
			let hdq_results_percent = (parseFloat(total_score) / parseFloat(total_questions)) * 100;
			hdq_results_percent = Math.ceil(hdq_results_percent);
			data =
				'<span class = "hdq_result_fraction">' +
				data +
				'</span> - <span class = "hdq_result_percent">' +
				hdq_results_percent +
				"%</span>";
		}

		jQuery("#hdq_" + hdq_form_id + " .hdq_results_inner .hdq_result").html(data);
		let pass_percent = 0;
		pass_percent = total_score / total_questions;
		pass_percent = pass_percent * 100;
		if (pass_percent >= hdq_pass_percent) {
			jQuery("#hdq_" + hdq_form_id + " .hdq_result_pass").show();
			hdq_pass_or_fail = "pass";
		} else {
			jQuery("#hdq_" + hdq_form_id + " .hdq_result_fail").show();
			hdq_pass_or_fail = "fail";
		}

		if (hdq_share_results === "yes") {
			hdq_create_share_link(hdq_form_id, total_score, total_questions);
		}

		jQuery("#hdq_" + hdq_form_id + " .hdq_results_wrapper").fadeIn();
		if (hdq_show_what_answers_were_right_wrong === "yes" || hdq_show_correct === "yes") {
			hdq_show_all_questions(hdq_form_id);
			if (hdq_show_what_answers_were_right_wrong === "yes") {
				hdq_set_correct_incorrect(hdq_form_id);
			}
		} else {
			hdq_scroll_to_results(hdq_form_id);
		}

		hdq_show_extra_question_info(hdq_form_id);

		// Action onSubmit Quiz: TODO: Async Await this for better compatibility
		if (typeof hdq_local_vars.hdq_submit != undefined && hdq_local_vars.hdq_submit != null) {
			for (let i = 0; i < hdq_local_vars.hdq_submit.length; i++) {
				hdq_onSubmit(hdq_local_vars.hdq_submit[i]);
			}
		}
	}

	function hdq_create_share_link(hdq_form_id, total_score, total_questions) {
		function hdq_create_twitter_share(hdq_form_id, total_score, total_questions) {
			let baseURL = "https://twitter.com/intent/tweet?screen_name=";
			let shareText =
				total_score + "/" + total_questions + " on the " + hdq_quiz_name + " quiz. Can you beat me? ";
			shareText = encodeURI(shareText);
			let shareLink = baseURL + hdq_twitter_handle + "&text=" + shareText + encodeURI(hdq_quiz_permalink);
			jQuery("#hdq_" + hdq_form_id + " .hdq_twitter").attr("href", shareLink);
		}
		hdq_create_twitter_share(hdq_form_id, total_score, total_questions);
	}

	function hdq_show_extra_question_info(hdq_form_id) {
		// only run if there is a question with extra info
		if (jQuery("#hdq_" + hdq_form_id + " .hdq_question_after_text")[0]) {
			if (hdq_show_answer_text === "yes") {
				jQuery("#hdq_" + hdq_form_id + " .hdq_question_after_text").fadeIn();
			} else {
				jQuery("#hdq_" + hdq_form_id + " .hdq_option").each(function() {
					if (jQuery(this).prop("checked") && jQuery(this).val() != 1) {
						let hdq_parent_question = jQuery(this).closest(".hdq_question");
						if (jQuery(hdq_parent_question).children(".hdq_question_after_text")[0]) {
							jQuery(hdq_parent_question)
								.children(".hdq_question_after_text")
								.fadeIn();
						}
					}
				});
			}
		}
	}

	function hdq_set_correct_incorrect(hdq_form_id) {
		jQuery("#hdq_" + hdq_form_id + " .hdq_option").each(function() {
			if (jQuery(this).prop("checked")) {
				if (jQuery(this).val() == 1) {
					jQuery(this)
						.closest(".hdq_label_answer")
						.addClass("hdq_correct");
				} else {
					jQuery(this)
						.closest(".hdq_label_answer")
						.addClass("hdq_wrong");
				}
			} else {
				if (hdq_show_correct === "yes") {
					if (jQuery(this).val() == 1) {
						jQuery(this)
							.closest(".hdq_label_answer")
							.addClass("hdq_correct_not_selected");
					}
				}
			}
		});
	}

	function hdq_show_all_questions(hdq_form_id) {
		jQuery("#hdq_" + hdq_form_id + " .hdq_question").fadeIn();
		setTimeout(function() {
			hdq_scroll_to_results(hdq_form_id);
		}, 1000);
	}

	function hdq_scroll_to_results(hdq_form_id) {
		console.log("hdq_scroll_to_results called");
		// this is super not accurate, but covers most themes.
		setTimeout(function() {
			let hdq_quiz_container = document.querySelector("#hdq_" + hdq_form_id);
			hdq_quiz_container = jQuery(hdq_get_quiz_parent_container(hdq_quiz_container));
			console.log("container:");
			console.log(hdq_quiz_container);

			if (hdq_quiz_container[0].tagName === "DIV") {
				hdq_top =
					jQuery(hdq_quiz_container).scrollTop() +
					jQuery(".hdq_results_wrapper").offset().top -
					jQuery(".hdq_results_wrapper").height() / 2 -
					80;
				console.log("hdq_top: " + hdq_top);
				jQuery(hdq_quiz_container).animate(
					{
						scrollTop: hdq_top
					},
					550
				);
				jQuery("html,body").animate(
					{
						scrollTop: hdq_top
					},
					550
				);
			} else {
				let overflowH = jQuery("html").css("overflow");
				let overflowB = jQuery("body").css("overflow");
				let rest = false;
				if (overflowH.indexOf("hidden") >= 0 || overflowB.indexOf("hidden") >= 0) {
					rest = true;
				}

				jQuery("html,body").css("overflow", "initial");

				jQuery("html,body").animate(
					{
						scrollTop: jQuery(".hdq_question:visible").offset().top - 40
					},
					550
				);

				if (rest) {
					setTimeout(function() {
						jQuery("html").css("overflow", overflowH);
						jQuery("body").css("overflow", overflowB);
					}, 550);
				}
			}
		}, 50);
	}

	hdq_calculate_total(hdq_form_id);

	function hdq_onSubmit(action) {
		console.log(action);
		let data = {};
		// if this is also a JS function, store data
		if (typeof window[action] !== "undefined") {
			data.extra = window[action]();
		}

		// small delay since this isn't syncronous
		setTimeout(function() {
			data.quizID = hdq_form_id;
			data.score = hdq_score;
			// send data to admin-ajax
			jQuery.ajax({
				type: "POST",
				data: {
					action: action,
					data: data
				},
				url: hdq_local_vars.hdq_ajax,
				success: function(res) {
					console.log(res);
				}
			});
		}, 50);
	}
});

/* FB APP */
jQuery("#hdq_fb_sharer").click(function() {
	let hdq_score = jQuery(".hdq_result").text();
	// check if there was an image added to pass or fail text

	/*
	// Facebook no longer allows us to send custom images :()
	let hdq_share_image = jQuery(".hdq_result_" + hdq_pass_or_fail)
		.find("img")
		.attr("src");
	if (hdq_share_image != "" && hdq_share_image != null) {
		// for things like jetpack proton images
		if (hdq_share_image.startsWith("//")) {
			console.log("image starts with // : fixing");
			hdq_share_image = "http:" + hdq_share_image;
		}
	} else {
		// no images in success or fail area
		hdq_share_image = hdq_featured_image;
	}
	*/

	FB.ui(
		{
			method: "share",
			href: hdq_quiz_permalink,
			hashtag: "#hdquiz",
			quote: "I scored " + hdq_score + " on " + hdq_quiz_name + ". Can you beat me?"
		},
		function(res) {}
	);
});
