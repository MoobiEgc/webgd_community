<script type="text/javascript" src="lib/cam/webcam.js"></script>
<script language="JavaScript">
    document.write(webcam.get_html(320, 240));
</script>
<form>
    <input type=button value="Configure..." onClick="webcam.configure()">
    &nbsp;&nbsp;
    <input type=button value="Take Snapshot" onClick="take_snapshot()">
</form>

<script language="JavaScript">
    webcam.set_api_url('lib/cam/test.php');
    webcam.set_quality(90); // JPEG quality (1 - 100)
    webcam.set_shutter_sound(true); // play shutter click sound
    webcam.set_hook('onComplete', 'my_completion_handler');

    function take_snapshot() {
        // take snapshot and upload to server
        //ocument.getElementById('upload_results').innerHTML = '<h1>Uploading...</h1>';
        webcam.snap();
    }

    function my_completion_handler(msg) {
        // extract URL out of PHP output
        if (msg.match(/(http\:\/\/\S+)/)) {
            // show JPEG image in page
            document.getElementById('upload_results').innerHTML = '<h1>Upload Successful!</h1>';
            // reset camera for another shot
            webcam.reset();
        } else {
            alert("PHP Error: " + msg);
        }
    }
</script>