<?php header( 'location: http://' . filter_input( INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL ) ); ?>
