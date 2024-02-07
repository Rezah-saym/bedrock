<?php

/**
 * 
 * Template for signle page
 * 
 * @package bootstrap-basic
 */

get_header(); 

?>
<div class="container-fluid page_top">
    <div id="twig_page" class="row">
        <?php 
            $context = $customTimberInstance->timber_option(null, 'twig/templates-twig/single.twig');
        ?>
    </div>
</div>
<?php
get_footer();
?>