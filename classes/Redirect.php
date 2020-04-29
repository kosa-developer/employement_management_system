<?php

class Redirect {

    public static function to($location = NULL) {
        ?>
        <script type="text/javascript">
            window.location = "<?php echo $location; ?>";
        </script>
        <?php
    }

    public static function go_to($location = NULL) {
        ?>
        <script type="text/javascript">
            setTimeout('Redirect()', 2000);
            function Redirect() {
                window.location = "<?php echo $location; ?>";
            }
        </script>
        <?php
    }

    public static function to_print($parent_location = NULL, $print_location = NULL) {
        ?>
        <script type="text/javascript">
            window.open('<?php echo $print_location; ?>', '_blank');
            setTimeout('Redirect()', 2000);
            function Redirect() {
                window.location = "<?php echo $parent_location; ?>";
            }
        </script>
        <?php
    }

}
?>