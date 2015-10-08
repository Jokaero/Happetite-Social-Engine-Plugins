<div id="linkedin_helpsteps" style="display:none;">
    <h3><?php echo $this->translate("Guidelines to configure Linkedin Application") ?></h3>
    <p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />

    <div class="admin_seaocore_guidelines_wrapper">
        <ul class="admin_seaocore_guidelines" id="guideline_1">
            <li>
                <div class="steps">
                    <a href="javascript:void(0);" onClick="guideline_show('linkedinstep-1');"><?php echo $this->translate("Step 1"); ?></a>
                    <div id="linkedinstep-1" style='display: none;'>
                        <p>
                            <?php echo $this->translate("1) Go to this URL : "); ?>

                            <a href="https://www.linkedin.com/secure/developer" target="_blank" style="color:#5BA1CD;">https://www.linkedin.com/secure/developer</a><br/>

                            <?php echo $this->translate("2) Go to this URL for steps to configure Linkedin Integration on your SocialEngine website : "); ?><a href="https://www.youtube.com/watch?v=Yp4vdkk9PrE
                               " target="_blank" style="color:#5BA1CD;">https://www.youtube.com/watch?v=Yp4vdkk9PrE</a><br/>

                            <?php echo $this->translate("3) <b>Authorized Redirect URLs:</b>") . ' => <b>' . ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . "/seacore/auth/linkedin/</b>"; ?><br />

                        </p>

                    </div>
                </div>
            </li>	

            <li>
                <div class="steps">
                    <a href="javascript:void(0);" onClick="guideline_show('linkedinstep-2');"><?php echo $this->translate("Step 2"); ?></a>
                    <div id="linkedinstep-2" style='display: none;'>
                        <p><?php echo $this->translate("Now, copy below 'API Key' and 'Secret Key' and paste these values in your site's Likedin contact importer settings fields ."); ?></p>
                        <img src="https://lh5.googleusercontent.com/-ASQDm4QyBZk/UGQsCYGfTiI/AAAAAAAAAOU/ldLqmHmx-n4/s520/linkedin-help3.jpg" alt="Step 3" />
                    </div>
                </div>	
            </li>

        </ul>
    </div>
    <script type="text/javascript">
        function guideline_show(id) {
            if ($(id).style.display == 'block') {
                $(id).style.display = 'none';
            } else {
                $(id).style.display = 'block';
            }
        }
    </script>
</div>