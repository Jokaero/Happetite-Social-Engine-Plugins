<div id="instagram_helpsteps" style="display:none;">
  <h3><?php echo $this->translate("Guidelines to configure Instagram Application") ?></h3>
  <p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />

  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="instagram-config">
      <li>
        <div class="steps">
          <a href="javascript:void(0);" onClick="guideline_show('instagramstep-1');"><?php echo $this->translate("Step 1") ?></a>
          <div id="instagramstep-1" style='display: none;'>
            <p>

              <?php echo $this->translate("Link your mobile no. with your instagram profile if you have not linked it already. It is necessary for creating instagram application. For linking this go to this URL: "); ?> <a href="https://instagram.com/accounts/edit/" target="_blank" style="color:#5BA1CD;">https://instagram.com/accounts/edit/</a><br />			

            </p>

          </div>
        </div>
      </li>
      <li>
        <div class="steps">
          <a href="javascript:void(0);" onClick="guideline_show('instagramstep-2');"><?php echo $this->translate("Step 2") ?></a>
          <div id="instagramstep-2" style='display: none;'>
            <p>

              <?php echo $this->translate("Go to this URL for steps to configure basic Instagram Integration on your SocialEngine website : "); ?> <a href="http://youtu.be/lAMVln1Bm8U" target="_blank" style="color:#5BA1CD;">http://youtu.be/lAMVln1Bm8U</a><br />			

            </p>
          </div>
        </div>
      </li>

      <li>	
        <div class="steps">
          <a href="javascript:void(0);" onClick="guideline_show('instagramstep-3');"><?php echo $this->translate("Step 3") ?></a>
          <div id="instagramstep-3" style='display: none;'>
            <h3><?php echo $this->translate(" Details to be filled while creating Instagram Application: "); ?><br /></h3>

            <p>
              <?php echo $this->translate("a) <b>Website</b>") . ' => <b>' . ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . "/</b>"; ?><br />

              <?php echo $this->translate("b) <b>OAuth redirect_uri</b>") . ' => <b>' . ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . "/instagram/auth/instagram/</b>"; ?><br />                

            </p>	
          </div>
        </div>
      </li>
      <li>
        <div class="steps">
          <a href="javascript:void(0);" onClick="guideline_show('instagramstep-4');"><?php echo $this->translate("Step 4") ?></a>
          <div id="instagramstep-4" style='display: none;'>
            <p><?php echo $this->translate("Open the hidden file '.htaccess', from your root directory and search for the lines given below:"); ?></p>
            <div class="code_box">
              <b>
                <?php echo $this->translate("&lt;IfModule mod_rewrite.c&gt;<br /><br />
	
	                  Options +FollowSymLinks<br /><br />
	                
	                  RewriteEngine On"); ?><br /><br />
              </b>      
            </div><br />
            <p>Now replace 'yourdomain.com' with your site's domain name in the below lines, and insert them just after the above mentioned lines.</p>
            <div  class="code_box">
              <b>
                <?php echo $this->translate("<br /><br />RewriteCond %{HTTP_HOST} ^yourdomain.com [NC]<br /><br />  
	              RewriteRule ^(.*)$ http://www.yourdomain.com/$1 [L,R=301]"); ?><br /><br />  
              </b>

            </div>
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