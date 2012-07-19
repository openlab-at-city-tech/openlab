function onYouTubePlayerReady(playerId) {
	var player = document.getElementById(playerId);
	player.addEventListener("onStateChange", "cp_youtube_" + playerId + "_fn" );
}

function cp_youtube_updateState(uuid, state){
	if(state==0){
		jQuery.ajax({
		  type: 'POST',
		  url: cp_youtube.ajax_url,
		  data: { action: "cp_youtube", uuid: uuid }
		});
	}
}