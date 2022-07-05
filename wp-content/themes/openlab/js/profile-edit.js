jQuery(document).ready(function($) {
    $editFrom = $('#profile-edit-form');
    $submitFrom = $('#profile-group-edit-submit');

    $editFrom.parsley({
        trigger: 'change'
    }).on('field:error', function() {
        $editFrom.find('.error-container').addClass('error');

        $submitFrom.addClass('btn-disabled')
            .val('Please Complete Required Fields');
    }).on('field:success', function() {
        if ( ! this.parent.isValid() ) {
            return false;
        }

        $editFrom.find('.error-container').removeClass('error');

        $submitFrom.removeClass('btn-disabled')
            .val('Save Changes');
    });

    
    var startButton = $('button#recordPronunciation');
    var stopButton = $('button#stopPronunciation');
    var recordingStatus = $('p.recordingStatus');
    var recordedAudio = $('#recordedAudio')
    const mediaRequestType = {
        audio: true
    };
    const mimeType = 'audio/webm';
    var audioStream;
    var recorder;
    var chunks = [];
    var recordingInterval;

    startButton.on( 'click', function() {
        // Toggle the state of the control buttons
        toggleRecordingButtons('recording');

        // Request permission to use user's media input
        navigator.mediaDevices.getUserMedia(mediaRequestType).then( function(stream) {
            audioStream = stream;
            recorder = new MediaRecorder(stream, {
                type: mimeType
            } );

            recorder.ondataavailable = function(e) {
                chunks.push(e.data);

                // If recording have stopped, create the audio
                if(recorder.state == 'inactive') {
                    // Convert stream data chunks to audio/webm format as a blob
                    const blob = new Blob(chunks, {
                        type: mimeType
                    } );

                    // Show recorded audio
                    showRecordedAudio(blob);

                    // Clear recorded chunks
                    chunks = [];
                }
            }

            recorder.onerror = function(e) {
                console.error('Error recording stream: ' + e.error.name);
            }

            // Start recording
            recorder.start();

            // Start recording countdown
            recordingTimeCountdown();
        }).catch( function(err) {
            console.log('Error: ' + err.message );

            // Toggle state of control buttons
            toggleRecordingButtons('stopped');

            // Clear recording time countdown
            clearInterval(recordingInterval);

            // Remove the recording status message
            recordingStatus.text('');
        });
    });

    stopButton.on( 'click', function() {
        stopRecording();
    });

    /**
     * Stop media recording.
     */
    function stopRecording() {
        // Stop MediaRecorder
        recorder.stop();

        // Stop audio stream
        audioStream.getAudioTracks()[0].stop();

        // Toggle state of control buttons
        toggleRecordingButtons('stopped');

        // Clear recording time countdown
        clearInterval(recordingInterval);

        // Remove the recording status message
        recordingStatus.text('');
    }

    /**
     * Limit the recording to N seconds and show message
     * how much time is left.
     */
    function recordingTimeCountdown(seconds = 10) {
        recordingInterval = setInterval( function() {
            if( seconds <= 0 ) {
                stopRecording();
            } else {
                recordingStatus.text(seconds + ' seconds left.');
            }
            seconds--;
        }, 1000);
    }

    /**
     * Display audio element with the recorded stream.
     */
    function showRecordedAudio(blob) {
        // Create URL for the Blob object
        var blobUrl = URL.createObjectURL(blob);
        
        // Read the content of the Blob and store the base64 in field
        var fileReader = new FileReader();
        fileReader.onload = function(e) {
            $('input#name_pronunciation_blob').val(e.target.result);
        }
        fileReader.readAsDataURL(blob);

        // Create html audio element
        var audio = document.createElement('audio');
        audio.setAttribute('src', blobUrl);
        audio.setAttribute('controls', 'controls');

        // Append the audio element
        recordedAudio.append(audio);
    }

    /**
     * Toggle the status of the start/stop buttons
     */
    function toggleRecordingButtons( status ) {
        if( status === 'recording' ) {
            startButton.text('Recording...');
            startButton.prop('disabled', true);
            stopButton.prop('disabled', false);
            recordedAudio.html('');
        } else {
            startButton.text('Start recording');
            startButton.prop('disabled', false);
            stopButton.prop('disabled', true);
        }
    }

});
