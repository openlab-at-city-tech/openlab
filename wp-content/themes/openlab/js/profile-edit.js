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
    var list = document.getElementById('recordingsList');
    const constraints = {
        audio: true
    };
    const mimeType = 'audio/webm';
    var gumStream;
    var recorder;
    var chunks = [];

    $(document).on( 'click', 'button#recordPronunciation', function() {
        console.log('start recording');
        recordButton.prop('disabled', true);

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
                    createDownloadLink(blob);
                    chunks = [];
                }
            }

            recorder.onerror = function(e) {
                console.log( 'RECORDER ERRROR');
                console.log(e.error);
            }

            //start recording using 1 second chunks
            //Chrome and Firefox will record one long chunk if you do not specify the chunck length
            // recorder.start(1000);
            recorder.start();
        }).catch(function(err) {
            console.log( 'ERROR' );
            console.log( err );
            //enable the record button if getUserMedia() fails
            // recordButton.disabled = false;
            // stopButton.disabled = true;
            // pauseButton.disabled = true
        });
        
    });

    function createDownloadLink(blob) {
        const blobUrl = URL.createObjectURL(blob);
        $('input#field_name_pronunciation_recording').val(blobUrl);
        const li = document.createElement('li');
        const audio = document.createElement('audio');
        const anchor = document.createElement('a');
        anchor.setAttribute('href', blobUrl);
        const now = new Date();
        var fileName = `recording-${now.getFullYear()}-${(now.getMonth() + 1).toString().padStart(2, '0')}-${now.getDay().toString().padStart(2, '0')}--${now.getHours().toString().padStart(2, '0')}-${now.getMinutes().toString().padStart(2, '0')}-${now.getSeconds().toString().padStart(2, '0')}.webm`;
        anchor.setAttribute(
            'download',
            fileName
        );
        anchor.innerText = 'Download';
        audio.setAttribute('src', blobUrl);
        audio.setAttribute('controls', 'controls');
        li.appendChild(audio);
        li.appendChild(anchor);
        list.appendChild(li);
        
        // createFileFromBlob(blob, fileName);
    }

    function createFileFromBlob(blob, filename) {
    }

    $(document).on( 'click', 'button#stopPronunciation', function() {
        console.log('stop recording');
        recorder.stop();
        gumStream.getAudioTracks()[0].stop();
        recordButton.prop('disabled', false);
    });

});
