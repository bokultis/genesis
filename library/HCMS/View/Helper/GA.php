<?php
/**
 * View helper which sets GA js
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_GA extends Zend_View_Helper_Abstract {
    /**
     * Get GA code
     *
     * @param Application_Model_Application $app
     */
    public function GA ($app) {
        $gaSettings = $app->get_settings('ga');
        if(!isset ($gaSettings) || $gaSettings['active'] != 'yes'){
            return '';
        }
        $html = "
<script type=\"text/javascript\">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '$gaSettings[tracking_id]']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
            ";
        return  $html;
    }
}