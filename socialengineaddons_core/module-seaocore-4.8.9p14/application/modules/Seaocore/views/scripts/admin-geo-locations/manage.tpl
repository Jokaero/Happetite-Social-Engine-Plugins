<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 6590 2014-06-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo 'SocialEngineAddOns Core Plugin' ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<?php if(Engine_Api::_()->seaocore()->getLocationsTabs()): ?>
    <div class='tabs'>
      <ul class="navigation">
        <li>
        <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'seaocore','controller'=>'settings','action'=>'map'), 'General Settings', array())
        ?>
        </li>
        <li class="active">
        <?php
          echo $this->htmlLink(array('route'=>'admin_default','module'=>'seaocore','controller'=>'geo-locations','action'=>'manage'), 'Manage Locations', array())
        ?>
        </li>			
      </ul>
    </div>
<?php endif; ?>

<h3><?php echo 'Manage Locations'; ?></h3>
<p>
    <?php echo 'This page lists all the location added by you. Here, you can enabled / disabled, edit and delete them. You must to add atleast one location if you have enabled "Enabled Specific Location" setting from Map >> General Settings section.'; ?>
</p>
<br />
<a class="buttonlink seaocore_icon_add smoothbox" href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'geo-locations', 'action' => 'add'), "admin_default", true); ?>"><?php echo "Add Location"; ?></a>
<br />

<?php if (Count($this->locationContents) > 0): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete()">

        <table class='admin_table seaocore_admin_table' width="100%">
            <thead>
                <tr>
                    <th style="width:2%"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>

                    <?php $class = ( $this->order == 'locationcontent_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th style="width:8%" class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('locationcontent_id', 'DESC');"><?php echo 'ID'; ?></a></th>

                    <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th style="width:20%" class="<?php echo $class ?>"  align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo 'Title'; ?></a></th>
                    
                    <?php $class = ( $this->order == 'location' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                                        <th style="width:40%" class="<?php echo $class ?>"  align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('location', 'ASC');"><?php echo 'Location'; ?></a></th>                    
                    
                    <?php $class = ( $this->order == 'status' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th style="width:10%" class="<?php echo $class ?> admin_table_centered" title="<?php echo 'Status'; ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'ASC');"><?php echo 'Status'; ?></a></th>                  
    
                    <th style="width:10%" class="<?php echo $class ?>"  class='admin_table_centered'><?php echo 'Options'; ?></th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($this->locationContents)): ?>
                    <?php foreach ($this->locationContents as $locationContent): ?> 
                        <tr>

                            <td><input name='delete_<?php echo $locationContent->locationcontent_id; ?>' type='checkbox' class='checkbox' value="<?php echo $locationContent->locationcontent_id ?>"/></td>

                            <td><?php echo $locationContent->locationcontent_id ?></td>

                            <td class='admin_table_bold' style="white-space:normal;" title="<?php echo $locationContent->getTitle() ?>">
                            <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($locationContent->getTitle(), 30) ?>
                            </td>
                            
                            <td class='admin_table_bold' style="white-space:normal;" title="<?php echo $locationContent->location ?>">
                            <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($locationContent->location, 50) ?>
                            </td>                            

                            <?php if ($locationContent->status == 1): ?>
                                <?php if($this->locationsCount <= 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0)): ?>
                                    <td align="center" class="admin_table_centered"> <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => 'You can not disable this location because you have enabled "Enable Specific Locations" setting from "General Settings" section and for specific location at least one location should be exist.')); ?></td>
                                <?php elseif($this->locationContentId == $locationContent->locationcontent_id): ?>
                                    <td align="center" class="admin_table_centered"> <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => 'You can not disable this location because you have choosen this location as "Default Location for Content Searching" from "General Settings" section.')); ?></td>
                                    
                                <?php else: ?>
                                    
                                    <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'seaocore', 'controller' => 'geo-locations', 'action' => 'status', 'locationcontent_id' => $locationContent->locationcontent_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => 'Make Disable'))) ?></td>
                                    
                                <?php endif; ?>
                            <?php else: ?>
                                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'seaocore', 'controller' => 'geo-locations', 'action' => 'status', 'locationcontent_id' => $locationContent->locationcontent_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => 'Make Enable'))) ?></td>
                            <?php endif; ?>              
            
                            <td class='admin_table_options'>
                                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'seaocore', 'controller' => 'geo-locations', 'action' => 'edit', 'locationcontent_id' => $locationContent->locationcontent_id), 'edit', array('class' => 'smoothbox', 'target' => '_blank')) ?>
                                |
                                <?php if($this->locationsCount <= 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0)): ?>
                                    <span title='You can not delete this location because you have enabled "Enable Specific Locations" setting from "General Settings" section and for specific location at least one location should be exist.'><?php echo 'delete'; ?></span>  
                                <?php elseif($this->locationContentId == $locationContent->locationcontent_id): ?>
                                    <span title='You can not delete this location because you have choosen this location as "Default Location for Content Searching" from "General Settings" section.'><?php echo 'delete'; ?></span>          
                                <?php else: ?>
                                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'seaocore', 'controller' => 'geo-locations', 'action' => 'delete', 'locationcontent_id' => $locationContent->locationcontent_id), 'delete', array('class' => 'smoothbox')) ?>
                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <br /><br />
        <div class='buttons'>
            <button type='submit'><?php echo 'Delete Selected'; ?></button>
        </div>
    </form>

<?php else: ?>
        <br/>
        <div class="tip mtip10">
            <span> <?php echo 'No locations has been added yet.'; ?>
            </span> 
        </div>
<?php endif; ?>

<script type="text/javascript">
    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';
    var changeOrder = function(order, default_direction) {

        if (order == currentOrder) {
            $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
        } else {
            $('order').value = order;
            $('order_direction').value = default_direction;
        }
        $('filter_form').submit();
    }

    function multiDelete()
    {
        return confirm('<?php echo $this->string()->escapeJavascript("Are you sure you want to delete selected locations ?") ?>');
    }

    function selectAll()
    {
        var i;
        var multidelete_form = $('multidelete_form');
        var inputs = multidelete_form.elements;

        for (i = 1; i < inputs.length - 1; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }
</script>

<div class='admin_search'>
	<?php echo $this->formFilter->render($this) ?>
</div>