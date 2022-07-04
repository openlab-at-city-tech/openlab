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

    // Name pronunciation recording
    var recordButton = $('button#recordPronunciation');
    var stopButton = $('button#stopPronunciation');
    var recordingStatus = $('p.recordingStatus');
    var recordedAudio = $('#recordedAudio')
    const constraints = {
        audio: true
    };
    const mimeType = 'audio/webm';
    var gumStream;
    var recorder;
    var chunks = [];
    var recordingTimeout;

    $(document).on( 'click', 'button#recordPronunciation', function() {
        recordButton.prop('disabled', true);
        stopButton.prop('disabled', false);
        recordingStatus.text('Recording...');
        recordedAudio.html('');

        navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
            console.log("getUserMedia() success, stream created, initializing MediaRecorder");
            gumStream = stream;
            recorder = new MediaRecorder(stream, { type: mimeType } );

            recorder.ondataavailable = function(e) {               
                // add stream data to chunks
                chunks.push(e.data);
                
                // if recorder is 'inactive' then recording has finished
                if ( recorder.state == 'inactive' ) {
                    // convert stream data chunks to a 'webm' audio format as a blob
                    const blob = new Blob(chunks, { type: mimeType } );
                    showRecordedAudio(blob);
                    chunks = [];
                }
            }

            recorder.onerror = function(e) {
                console.log( 'RECORDER ERRROR');
                console.log(e.error);
            }

            recorder.start();
            recordingTimeout = setTimeout( stopRecording, 11000 );
        }).catch(function(err) {
            console.log( err );
            
            recordButton.prop('disabled', false);
            stopButton.prop('disabled', true);
            recordingStatus.innerHTML = 'Show error message...';
        });
        
    });


    function showRecordedAudio(blob) {
        const blobUrl = URL.createObjectURL(blob);
        
        var fileReader = new FileReader();
        fileReader.onload = function(e) {
            console.log('executing this');
            $('input#name_pronunciation_blob').val(e.target.result);
        }

        fileReader.readAsDataURL(blob);

        const audio = document.createElement('audio');
        audio.setAttribute('src', blobUrl);
        audio.setAttribute('controls', 'controls');
        recordedAudio.append(audio);
        recordingStatus.text('You can listen to your recording below. If you want to record new audio, just click Record.')
    }

    function stopRecording() {
        console.log('stop recording');
        recorder.stop();
        gumStream.getAudioTracks()[0].stop();
        recordButton.prop('disabled', false);
        stopButton.prop('disabled', true);
    }

    $(document).on( 'click', 'button#stopPronunciation', function() {
        stopRecording();
    });

});
