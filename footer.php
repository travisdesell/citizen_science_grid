<?php

function print_footer($designer_names, $copyright_names) {
    $year = date("Y");

    echo "
        <!-- Footer
        ================================================== -->
        <hr>
        <footer class='footer'>
            <div class='container'>
                <center>
                <p>Designed by $designer_names with much help from <a href='http://twitter.github.com/bootstrap/getting-started.html'>Twitter's Bootstrap</a>.</p>
                <p>&copy; $year $copyright_names and the University of North Dakota. Images are under creative commons or wikimedia commons licenses.</p>
                </center>
            </div>
        </footer>
    ";
}

?>
