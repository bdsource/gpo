<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$document = JFactory::getDocument();
$document->addScript('components/com_gpo/assets/jquery-ui.min.js');
include_once 'mas.css'; //add the css file


$select_data['city']     = GpoGetHtmlForType( 'city' );
$select_data['hashtags'] = GpoGetHtmlForType('hashtags');
$select_data['category'] = GpoGetHtmlForType('category');
$createTask  = Joomla\CMS\Factory::getApplication()->getInput()->get('task');
$narrativeRows = 5;

$filename = JPATH_BASE . '/components/com_gpo/cache/admin_region.txt';
if( !file_exists( $filename ) )
{
	echo 'Error: Remember to create your region list';
	return;
}

$select_data['region'] = explode("\n",trim(file_get_contents( $filename )));
$filename = JPATH_BASE . '/components/com_gpo/cache/admin_country.txt';
if( !file_exists( $filename ))
{
	echo 'Error: Remember to create your country list';
	return;
}

$data = trim(file_get_contents( $filename ) );
$select_data['country'] =  explode("\n",$data);
$date_output = 'j F Y';

//Set display output for Published.
if( $this->oMas->modified === '0000-00-00 00:00:00' || empty( $this->oMas->modified ) ) { 
    $this->oMas->modified = GpoDefaultPublishTime(); 
}

if( $this->oMas->published === '0000-00-00 00:00:00' || empty( $this->oMas->published ) )
   { $this->oMas->published=GpoDefaultPublishTime();}
else
    { $this->oMas->published = strtotime( $this->oMas->published ); }

$this->oMas->published = date( $date_output, $this->oMas->published );

//Set display output for Date of Shooting.
//if( $this->oMas->date_of_shooting === '0000-00-00 00:00:00' || empty( $this->oMas->date_of_shooting ) ){ 
//     $this->oMas->date_of_shooting=GpoDefaultPublishTime();
//}
//else {
    $this->oMas->date_of_shooting = strtotime( $this->oMas->date_of_shooting ); 
//}

$this->oMas->date_of_shooting = date( $date_output, $this->oMas->date_of_shooting );

foreach( $this->oMas as $key => $value )
{
    if( $key !== 'location' && is_string( $value ) )
    {
	$this->oMas->$key = htmlspecialchars( $value, ENT_QUOTES );
    }
}

if(!empty($this->oMas->country_id)):
    $allStates = $this->masModel->getStatesByCountry($this->oMas->country_id);
endif;
//var_dump($this->oMas);
?>

<div id="message_box"></div>

<?php include_once('submenus_startblock.php'); ?>

<span style="font-size:90%;color:#ff0000;">Changes made to this &quot;Lookup&quot; version of the record cannot be saved. Select &quot;Edit&quot; to alter the record.</span>
<h3 id="form-header" style="font-weight:bold;text-align:center;line-height:2px;">
    <?php echo ( $this->isNew ) ? "Create new MAS Shooting Item" : "View this MAS Shooting Item";?>
</h3>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_gpo');?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="" />
<input type="hidden" name="controller" value="mas" />
<input type="hidden" id="form_id" name="mas[id]" value="<?php echo $this->oMas->id; ?>" />
<input type="hidden" id="mas_live_id" name="mas[live_id]" value="<?php echo $this->oMas->live_id; ?>" />
<input type="hidden" id="new_record" name="new_record" value="0" />

<?php
    echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details'));
    echo JHtml::_('bootstrap.addTab', 'myTab', 'details', 'Shooting');
?>

<div class="row">
    <div style="float:right;padding:0px;margin:0px auto;padding-right:50px;">
        <p>
            <label style="display:block;" title="Unique auto-inserted record number: cannot be altered. A new record shows zero until Published. When a record is deleted, its ID number cannot be re-used.">MAS ID*</label>
            <input class="not-editable" style="width:40px;" type="text" id="mas_id" name="mas[id]" value="<?php echo $this->oMas->id; ?>" disabled="true" />
        </p>
    </div>
	
    <div class="cell " style="margin-left:0px; padding:2px;">
        <label style="display:block;" for="mas_dateOfShooting" title="Date of first shooting homicide, or Unknown">Date*</label>
        <input class="date_of_shooting" readonly style="width:245px;text-align:center;" type="text" id="mas_date_of_shooting" name="mas[date_of_shooting]" value="<?php echo $this->oMas->date_of_shooting; ?>" />
    </div>
    
    <div class="cell">
        <p>
            <label style="display:block;" id="mas_location" title="Select a single, standardised country name in which the first killing occurred">Country*</label>
            <select id="select_mas_location" name="select_mas_location" style="width:400px">
                <option value="0" selected="selected"> – Country – </option>
            <?php 
            foreach($select_data['country'] as $cat):
                //to deal with the format the text file is in
                $value = trim(str_replace("&nbsp;",'',$cat ));
                $isSelected = empty($value) ? '' : (($value==$this->oMas->country_name) ? ' selected' : '');
                echo "<option value='{$value}'". $isSelected . ">{$cat}</option>";
            endforeach;
            ?>
            </select>
            <span id="mas_txt_locations" style="display:none;"></span>
            <input type="hidden" id="mas_hidden_locations" name="mas[location]" value=""/>
        </p>
    </div> 
</div>

<div class="row">   
    
    
    
    <div class="cell">
            <p>
                <label style="display:block;" title="Select or add a single, standardised state, province or canton name in which the first killing occurred">State or Province</label>
                <select id="select_mas_state_province" name="select_mas_state_province" style="width:260px;">
                    <option value="0"> – State or Province – </option>
                    <?php
                      foreach($this->alljurisdictions as $jurisdiction) {           
                          echo '<option value='. $jurisdiction['name'].'>' . $jurisdiction['name'] . '</option>' . PHP_EOL;
                      }
                    ?>
                </select>
                
                <input type="text" id="mas_state_province" name="mas[state_province]" style="width:385px;" value="<?php
                  if (!empty($this->oMas->state_province)) {
                      echo $this->oMas->state_province;
                  }
            ?>"/>            
            </p>
         </div>    
    </div>

    <div class="row">
    <div class="cell">
        <p>
            <label style="display:block;" title="Select or add a single primary city, town or place">City*</label>
            <select id="select_mas_city" name="select_mas_city" style="width:260px;">
                <option value=""> – City – </option>
                <?php
                    foreach($select_data['city'] as $cat) {
                        echo "<option value='{$cat}'>$cat</option>";
                    }
                ?>
            </select>
            <input type="text" id="mas_city" name="mas[city]" style="width:387px;" value="<?php
            if (!empty($this->oMas->city)) {
                echo $this->oMas->city;
            }
            ?>"/>
        </p>
    </div>
        
</div>

<div class="row">
    <label style="display:block;" title="The most common short name for the shooting, commonly the primary shooting site (Columbine High, Utøya Island, Sandy Hook Elementary, Broad Arrow Café, Charlie Hebdo, etc.), or Unknown">Primary Venue*</label>
    <input class="input_field" style="width:650px;" type="text" id="mas_primary_venue" name="mas[primary_venue]" value="<?php echo $this->oMas->primary_venue;?>" />
</div>

<div class="row">
    <div class="cell left_col">
    <label style="display:block;" title="Select a single primary venue type">Venue Type*</label>
    <select id="mas_venue_type" name="mas[venue_type]" style="width:258px;">
        <option value="0"> – Select – </option>
        <option value="government" <?php echo ('government'==$this->oMas->venue_type) ? ' selected' : ''?>>Government</option>
        <option value="military" <?php echo ('military'==$this->oMas->venue_type) ? ' selected' : ''?>>Military</option>
        <option value="public_place" <?php echo ('public_place'==$this->oMas->venue_type) ? ' selected' : ''?>>Public Place</option>
        <option value="residential" <?php echo ('residential'==$this->oMas->venue_type) ? ' selected' : ''?>>Residential</option>
        <option value="religious" <?php echo ('religious'==$this->oMas->venue_type) ? ' selected' : ''?>>Religious</option>
        <option value="school" <?php echo ('school'==$this->oMas->venue_type) ? ' selected' : ''?>>School</option>
        <option value="workplace" <?php echo ('workplace'==$this->oMas->venue_type) ? ' selected' : ''?>>Workplace</option>
        <option value="other" <?php echo ('other'==$this->oMas->venue_type) ? ' selected' : ''?>>Other</option>
        <option value="unknown" <?php echo ('unknown'==$this->oMas->venue_type) ? ' selected' : ''?>>Unknown</option>
    </select>
    </div>

    <div class="cell right_col">
    <label style="display:block;" title="Select a single primary shooting type">Shooting Type*</label>
    <select id="mas_shooting_type" name="mas[shooting_type]" style="width:404px;">
        <option value="0"> – Select – </option>
        <option value="family" <?php echo ('family'==$this->oMas->shooting_type) ? ' selected' : ''?>>Family</option>
        <option value="gang"   <?php echo ('gang'==$this->oMas->shooting_type) ? ' selected' : ''?>>Gang</option>
        <option value="political" <?php echo ('political'==$this->oMas->shooting_type) ? ' selected' : ''?>>Political</option>
        <option value="spree"  <?php echo ('spree'==$this->oMas->shooting_type) ? ' selected' : ''?>>Spree</option>
        <option value="terrorism" <?php echo ('terrorism'==$this->oMas->shooting_type) ? ' selected' : ''?>>Terrorism</option>
        <option value="other" <?php echo ('other'==$this->oMas->shooting_type) ? ' selected' : ''?>>Other</option>
        <option value="unknown" <?php echo ('unknown'==$this->oMas->shooting_type) ? ' selected' : ''?>>Unknown</option>
    </select>
    </div>
</div>

<div class="row">
    <div class="cell left_col">
        <label style="display:block;" title="Degrees and minutes, or Unknown">Latitude*</label>
        <input style="width:245px;" type="text" id="mas_latitude" name="mas[latitude]" value="<?php echo $this->oMas->latitude; ?>" />
    </div>
    
    <div class="cell right_col">
        <label style="display:block;" title="Degrees and minutes, or Unknown">Longitude*</label>
        <input style="width:387px;" type="text" id="mas_longitude" name="mas[longitude]" value="<?php echo $this->oMas->longitude; ?>" />
    </div>
</div>

<div class="row">
    <div class="cell left_col">
        <label style="display:block;" title="Number of victims killed by gunshot (not including any perpetrators), or Unknown">Victims Shot Dead*</label>
        <input style="width:245px;" type="text" id="mas_victims_shot_dead" name="mas[victims_shot_dead]" value="<?php echo $this->oMas->victims_shot_dead; ?>" />
    </div>

    <div class="cell right_col">
        <label style="display:block;" title="Number of victims killed other than by gunshot (not including any perpetrators), 0 (zero), or Unknown">Victims Killed by Other Means*</label>
        <input style="width:387px;" type="text" id="mas_victims_killed_other_means" name="mas[victims_killed_other_means]" value="<?php echo $this->oMas->victims_killed_other_means; ?>" />
    </div>
</div>

<div class="row">
    <div class="cell left_col">
        <label style="display:block;" title="Total number of victims killed by any means (not including any perpetrators), or Unknown">Total Victims Killed*</label>
        <input style="width:245px;" type="text" id="mas_victims_killed_total" name="mas[victims_killed_total]" value="<?php echo $this->oMas->victims_killed_total; ?>" />
    </div>
    
    <div class="cell right_col">
        <label style="display:block;" title="Number of victims wounded by any means (not including any perpetrators), 0 (zero), or Unknown">Victims Wounded*</label>
        <input  style="width:387px;" type="text" id="mas_victims_wounded" name="mas[victims_wounded]" value="<?php echo $this->oMas->victims_wounded; ?>" />
    </div>
</div>

<div class="clear"></div>

<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.addTab', 'myTab', 'otherparams', 'Perpetrators');
?>

<div class="row">
    <div class="cell left_col">
        <label style="display:block;" title="Number, zero or Unknown">Perpetrators Killed by Others*</label>
        <input class="input_field" style="width:250px" type="text" id="mas_perpetrators_killed_others" name="mas[perpetrators_killed_others]" value="<?php echo $this->oMas->perpetrators_killed_others; ?>" />
    </div>
    
    <div class="cell right_col">
        <label style="display:block;" title="Number, zero or Unknown">Perpetrators Killed in Suicide*</label>
        <input class="input_field" style="width:384px" type="text" id="mas_perpetrators_killed_suicide" name="mas[perpetrators_killed_suicide]" value="<?php echo $this->oMas->perpetrators_killed_suicide; ?>" />
    </div>
</div>

<div class="row">
    <div class="cell left_col">
        <label style="display:block;" title="Number, zero or Unknown">Perpetrators Captured or Escaped*</label>
        <input class="input_field" style="width:250px" type="text" id="mas_perpetrators_captured_escaped" name="mas[perpetrators_captured_escaped]" value="<?php echo $this->oMas->perpetrators_captured_escaped; ?>" />
    </div>
    
    <div class="cell right_col">
        <label style="display:block;" title="Last name, first name of primary offender, or Unknown">Name of Primary Perpetrator*</label>
        <input class="input_field" style="width:385px" type="text" id="mas_primary_perpetrator_name" name="mas[primary_perpetrator_name]" value="<?php echo $this->oMas->primary_perpetrator_name; ?>" />
    </div>
</div>

<div class="row">
    <div class="cell">
            <label style="display:block;" title="Male or Female (or Male, Male, Female) or Unknown">Gender of Perpetrator(s)*</label>
            <select id="select_mas_perpetrators_gender" name="select_mas_perpetrators_gender" style="width:263px;">
                <option value="0"> – Select – </option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="LGBT">LGBT</option>
                <option value="Unknown">Unknown</option>
            </select>
            
            <input class="input_field" style="width:383px" type="text" id="mas_perpetrators_gender" name="mas[perpetrators_gender]" style="width:740px;" value="<?php
            if( !empty( $this->oMas->perpetrators_gender ) )
            {
                    echo $this->oMas->perpetrators_gender;
            }
            ?>" />
    </div>
</div>

<div class="row">
    <label style="display:block;" title="Age of perpetrator(s) at time of event (e.g. 34, 23, 19) or Unknown">Age of Perpetrator(s)*</label>
    <input style="width:652px" class="input_field" type="text" id="mas_perpetrators_age" name="mas[perpetrators_age]" value="<?php echo $this->oMas->perpetrators_age; ?>" />
</div>

<div class="row">
    <div class="cell">
            <label style="display:block;" title="History of mental illness diagnosed, reported to authorities or otherwise established prior to the shooting – Yes or No (or Yes, Yes, No), or Unknown">Perpetrator(s) Previous Mental Illness*</label>
            <select id="select_mas_perpetrators_previous_illness" name="select_mas_perpetrators_previous_illness" style="width:260px;">
                <option value="0"> – Select – </option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
                <option value="Unknown">Unknown</option>
            </select>
            
            <input class="input_field" style="width:387px" type="text" id="mas_perpetrators_previous_illness" name="mas[perpetrators_previous_illness]" style="width:740px;" value="<?php
            if( !empty( $this->oMas->perpetrators_previous_illness ) )
            {
                    echo $this->oMas->perpetrators_previous_illness;
            }
            ?>" />
    </div>
</div>

<div class="row">
    <div class="cell">
            <label style="display:block;" title="Known history of violence reported to authorities or otherwise established prior to the shooting – Yes or No (or Yes, Yes, No), or Unknown">Perpetrator(s) Previous Violence*</label>
            <select id="select_mas_perpetrators_previous_violence" name="mas[select_mas_perpetrators_previous_violence]" style="width:260px;">
                <option value="0"> – Select – </option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
                <option value="Unknown">Unknown</option>
            </select>
            
            <input class="input_field" style="width:387px" type="text" id="mas_perpetrators_previous_violence" name="mas[perpetrators_previous_violence]" value="<?php
            if( !empty( $this->oMas->perpetrators_previous_violence ) )
            {
                    echo $this->oMas->perpetrators_previous_violence;
            }
            ?>" />
    </div>
</div>

<div class="clear"></div>

<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.addTab', 'myTab', 'otherparams2', 'Firearms');
?>

<div class="row">
    <div class="cell left_col">
        <label style="display:block;" title="Select a single primary firearm type">Primary Firearm Type*</label>
        <select id="mas_primary_firearm_type" name="mas[primary_firearm_type]" style="width: 270px;">
            <option value="0"> – Select – </option>
            <option value="pistol"    <?php echo ('pistol'==$this->oMas->primary_firearm_type) ? ' selected' : ''?>>Pistol</option>
            <option value="revolver"  <?php echo ('revolver'==$this->oMas->primary_firearm_type) ? ' selected' : ''?>>Revolver</option>
            <option value="derringer" <?php echo ('derringer'==$this->oMas->primary_firearm_type) ? ' selected' : ''?>>Derringer</option>
            <option value="shotgun"   <?php echo ('shotgun'==$this->oMas->primary_firearm_type) ? ' selected' : ''?>>Shotgun</option>
            <option value="rifle"     <?php echo ('rifle'==$this->oMas->primary_firearm_type) ? ' selected' : ''?>>Rifle</option>
            <option value="carbine"   <?php echo ('carbine'==$this->oMas->primary_firearm_type) ? ' selected' : ''?>>Carbine</option>
            <option value="assault_rifle" <?php echo ('assault_weapon'==$this->oMas->primary_firearm_type) ? ' selected' : ''?>>Assault Rifle</option>
            <option value="handgun"  <?php echo ('handgun'==$this->oMas->primary_firearm_type) ? ' selected' : ''?>>Handgun</option>
            <option value="long_gun" <?php echo ('long_gun'==$this->oMas->primary_firearm_type) ? ' selected' : ''?>>Long Gun</option>
            <option value="unknown"  <?php echo ('unknown'==$this->oMas->primary_firearm_type) ? ' selected' : ''?>>Unknown</option>
        </select>
    </div>
    
    <div class="cell right_col">
        <label style="display:block;" title="Select a single primary firearm action">Primary Firearm Action*</label>
        <select id="mas_primary_firearm_action" name="mas[primary_firearm_action]" style="width: 387px;" >
            <option value="0"> – Select – </option>
            <option value="single_shot"   <?php echo ('single_shot'==$this->oMas->primary_firearm_action) ? ' selected' : ''?>>Single Shot</option>
            <option value="double_barrel" <?php echo ('double_barrel'==$this->oMas->primary_firearm_action) ? ' selected' : ''?>>Double Barrel</option>
            <option value="semi_auto"     <?php echo ('semi_auto'==$this->oMas->primary_firearm_action) ? ' selected' : ''?>>Semi-auto</option>
            <option value="automatic"     <?php echo ('automatic'==$this->oMas->primary_firearm_action) ? ' selected' : ''?>>Automatic</option>
            <option value="unknown"       <?php echo ('unknown'==$this->oMas->primary_firearm_action) ? ' selected' : ''?>>Unknown</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="cell left_col">
        <label style="display:block;" title="Make and/or model of the primary firearm, Craft or Unknown">Primary Firearm Make/Model*</label>
        <input class="input_field" style="width:255px" type="text" id="mas_primary_firearm_make" name="mas[primary_firearm_make]" value="<?php echo $this->oMas->primary_firearm_make; ?>" />
    </div>
    
    <div class="cell right_col">
        <label style="display:block;" title="Select a single primary firearm legal status at acquisition">Primary Firearm Obtained Legally*</label>
        <select id="mas_primary_firearm_obtained_legally" name="mas[primary_firearm_obtained_legally]" style="width:385px">
            <option value="0"> – Select – </option>
            <option value="yes"      <?php echo ('yes'==$this->oMas->primary_firearm_obtained_legally) ? ' selected' : ''?>>Yes</option>
            <option value="no"       <?php echo ('no'==$this->oMas->primary_firearm_obtained_legally) ? ' selected' : ''?>>No</option>
            <option value="unknown"  <?php echo ('unknown'==$this->oMas->primary_firearm_obtained_legally) ? ' selected' : ''?>>Unknown</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="cell">
            <label style="display:block;" title="Select one or more secondary firearm types, Nil or Unknown">Secondary Firearm Type*</label>
            <select id="select_mas_secondary_firearm_type" style="width: 267px;">
                <option value="0"> – Select – </option>
                <option value="Pistol">Pistol</option>
                <option value="Revolver">Revolver</option>
                <option value="Derringer">Derringer</option>
                <option value="Shotgun">Shotgun</option>
                <option value="Rifle">Rifle</option>
                <option value="Carbine">Carbine</option>
                <option value="Assault Rifle">Assault Rifle</option>
                <option value="Handgun">Handgun</option>
                <option value="Long Gun">Long Gun</option>
                <option value="Nil">Nil</option>
                <option value="Unknown">Unknown</option>
            </select>

            <input class="input_field" style="width:370px" type="text" id="mas_secondary_firearm_type" name="mas[secondary_firearm_type]" style="width:740px;" value="<?php
            if( !empty( $this->oMas->secondary_firearm_type ) )
            {
                    echo $this->oMas->secondary_firearm_type;
            }
            ?>" />
    </div>
</div>

<div class="clear"></div>

<div class="row">
    <div class="cell">
            <label style="display:block;" title="Select one or more secondary firearm actions, Nil or Unknown">Secondary Firearm Action*</label>
            <select id="select_mas_secondary_firearm_action"  style="width:265px;">
                <option value="0"> – Select – </option>
                <option value="Single Shot">Single Shot</option>
                <option value="Double Barrel">Double Barrel</option>
                <option value="Semi-auto">Semi-auto</option>
                <option value="Automatic">Automatic</option>
                <option value="Nil">Nil</option>
                <option value="Unknown">Unknown</option>
            </select>

            <input class="input_field" style="width:371px" type="text" id="mas_secondary_firearm_action" name="mas[secondary_firearm_action]" style="width:740px;" value="<?php
            if( !empty( $this->oMas->secondary_firearm_action ) )
            {
                    echo $this->oMas->secondary_firearm_action;
            }
            ?>" />
    </div>
</div>
<div class="clear"></div>

<div class="row">
    <label style="display:block;" title="Make and/or model of any secondary firearm(s), Craft, Nil or Unknown">Secondary Firearm Make/Model*</label>
    <input class="input_field" style="width:640px" type="text" id="mas_secondary_firearm_make" name="mas[secondary_firearm_make]" value="<?php echo $this->oMas->secondary_firearm_make; ?>" />
</div>

<div class="row">
    <div class="cell">
            <label style="display:block;" title="Select secondary firearm legal status at acquisition (one or more)">Secondary Firearm Obtained Legally*</label>
            <select id="select_mas_secondary_firearm_obtained_legally" style="width: 265px;">
                <option value="0"> – Select – </option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
                <option value="Nil">Nil</option>
                <option value="Unknown">Unknown</option>
            </select>

            <input class="input_field" style="width:370px" type="text" id="mas_secondary_firearm_obtained_legally" name="mas[secondary_firearm_obtained_legally]" style="width:740px;" value="<?php
            if( !empty( $this->oMas->secondary_firearm_obtained_legally ) )
            {
                    echo $this->oMas->secondary_firearm_obtained_legally;
            }
            ?>" />
    </div>
</div>
<div class="clear"></div>

<div class="row">
    <div class="cell left_col">
        <label style="display:block;" title="Select Yes, No or Unknown">Citizen Armed Intervention*</label>
        <select id="mas_citizen_armed_intervention" name="mas[citizen_armed_intervention]" style="width:265px;">
            <option value="0"> – Select – </option>
            <option value="yes"     <?php echo ('yes'==$this->oMas->citizen_armed_intervention) ? ' selected' : ''?>>Yes</option>
            <option value="no"      <?php echo ('no'==$this->oMas->citizen_armed_intervention) ? ' selected' : ''?>>No</option>
            <option value="unknown" <?php echo ('unknown'==$this->oMas->citizen_armed_intervention) ? ' selected' : ''?>>Unknown</option>
        </select>
    </div>    
</div>
<div class="clear"></div>
<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.addTab', 'myTab', 'otherparams3', 'Narrative');
?>



<div class="row">
    <div class="cell left_col">
        <label style="display:block;" title="Enter a summary of main facts in standardised format">Narrative*</label>
        <textarea id="mas_narrative" name="mas[narrative]" style="width:632px;height: 250px" rows="<?php echo $narrativeRows;?>"><?php echo $this->oMas->narrative;?></textarea>
    </div>   
<div>
        


<div class="row">
    <div class="cell left_col">
        <label style="display:block;" title="Select and enter your initials from the drop-down list">Staff</label>
            <span>
             <select name="mas[staff_list]" id="mas_staff_list" style="width:242px;">
                 <option value="0"> – Select – </option>
                   <?php
                        foreach ($this->staffs as $staff) {
                        echo '<option>' . $staff->initial . '</option>' . PHP_EOL;
                       }
                    ?>
            </select>
		     <input type="text" id="staffs" name="mas[staff]" style="width: 388px;" value="<?php echo $this->oMas->staff; ?>"
                    size="30"/>
	    </span>
        
     </div>    
</div>

<div class="row">
        <p>
            <label style="display:block;" title="Staff only: often empty, never published.">Notes</label>
            <textarea id="mas_notes" name="mas[notes]" style="width:632px;height:60px;"><?php
                if (!empty($this->oMas->notes)) {
                echo $this->oMas->notes;
            }
                ?></textarea>
        </p>  
</div>

<div class="row">
    <div style="float:left;padding:0px;margin:0px auto;padding-right:50px">
            <label style="display:block;" for="mas_modified" title="Date of publication. Book, 2008: 1/1/08. Aug/Sep 2008 Issue: 1/8/08. Early copy: date received, plus 'June issue,' 'Summer issue,' etc. in Notes">Modified*</label>
            <input readonly="readonly" class="modified" style="width:632px;" type="text" id="mas_modified" name="mas[modified]" 
                   value="<?php echo date(("j M Y"),$this->oMas->modified);?>" />
    </div>  
</div>


<div class="clear"></div>
<?php
echo JHtml::_('bootstrap.endTab');
echo JHtml::_('bootstrap.endTabSet');
?>

<div id="tool-tip-box"></div>
</form>
<p> &nbsp; </p>
<?php include_once('submenus_endblock.php');?>

<script>
Calendar._DN = new Array ("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");Calendar._SDN = new Array ("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"); Calendar._FD = 0;	Calendar._MN = new Array ("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");	Calendar._SMN = new Array ("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");Calendar._TT = {};Calendar._TT["INFO"] = "About the Calendar";
 		Calendar._TT["ABOUT"] =
 "DHTML Date/Time Selector\n" +
 "(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" +
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Date selection:\n" +
"- Use the \xab, \xbb buttons to select year\n" +
"- Use the " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " buttons to select month\n" +
"- Hold mouse button on any of the above buttons for faster selection.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Time selection:\n" +
"- Click on any of the time parts to increase it\n" +
"- or Shift-click to decrease it\n" +
"- or click and drag for faster selection.";

Calendar._TT["PREV_YEAR"] = "Click to move to the previous year. Click and hold for a list of years.";Calendar._TT["PREV_MONTH"] = "Click to move to the previous month. Click and hold for a list of the months.";	Calendar._TT["GO_TODAY"] = "Go to today";Calendar._TT["NEXT_MONTH"] = "Click to move to the next month. Click and hold for a list of the months.";Calendar._TT["NEXT_YEAR"] = "Click to move to the next year. Click and hold for a list of years.";Calendar._TT["SEL_DATE"] = "Select a date.";Calendar._TT["DRAG_TO_MOVE"] = "Drag to move";Calendar._TT["PART_TODAY"] = " (Today)";Calendar._TT["DAY_FIRST"] = "Display %s first";Calendar._TT["WEEKEND"] = "0,6";Calendar._TT["CLOSE"] = "Close";Calendar._TT["TODAY"] = "Today";Calendar._TT["TIME_PART"] = "(Shift-)Click or Drag to change the value.";Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%D"; Calendar._TT["TT_DATE_FORMAT"] = "%A, %B %e";Calendar._TT["WK"] = "wk";Calendar._TT["TIME"] = "Time:";
</script>

<script type="text/javascript">
// userAllow for allow user to save data after warning twitter message.
var userAllow = 0;

//<![CDATA[	
var check = new  Hash();
var tab_order_str="mas_published,select_mas_country,select_mas_region,select_mas_source,mas_source,select_mas_keywords,mas_keywords,mas_title,mas_sub_title,mas_byline,mas_category,mas_websource,mas_gpnheader,mas_notes";
var locations = [];
<?php
    
    if( count( $this->oMas->locations ) > 0 ): ?>
var current_locations = '<?php
//tidy up for the json 
	$data = json_encode( $this->oMas->locations );
	$data = str_replace("'","\'",$data);
	echo $data;
?>';
<?php  else: ?>
var current_locations = null;	
<?php
	 endif;
?>

location_populate();

Event.observe(window,'load',function(){

dd = document.body;
Element.extend( dd );

//Allow the left click of the mouse to trigger a new item
$('mas_staff_list').observe('click', function(event) {
//alert('i m fired');
        if (this.selectedIndex != '0') {
            select_split('mas_staff_list', 'staffs');
        }
 });
        
$('select_mas_perpetrators_previous_illness').observe( 'change', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split('select_mas_perpetrators_previous_illness', 'mas_perpetrators_previous_illness');
	}
});

$('select_mas_perpetrators_previous_violence').observe( 'change', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split('select_mas_perpetrators_previous_violence', 'mas_perpetrators_previous_violence');
	}
});

$('select_mas_perpetrators_gender').observe( 'change', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split('select_mas_perpetrators_gender', 'mas_perpetrators_gender');
	}
});

$('select_mas_secondary_firearm_type').observe( 'change', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split( 'select_mas_secondary_firearm_type', 'mas_secondary_firearm_type' );
	}
});
//this is required to reset the list options
$('mas_secondary_firearm_type').observe('focus',function(event){
	$('select_mas_secondary_firearm_type').selectedIndex = '0';
});

$('select_mas_secondary_firearm_type').observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		select_split( 'select_mas_secondary_firearm_type', 'mas_secondary_firearm_type' );
	}
});

//Allow the left click of the mouse to trigger a new item
$('select_mas_secondary_firearm_action').observe( 'change', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split( 'select_mas_secondary_firearm_action', 'mas_secondary_firearm_action' );
	}
});
//this is required to reset the list options
$('mas_secondary_firearm_action').observe('focus',function(event){
	$('select_mas_secondary_firearm_action').selectedIndex = '0';
});

$('select_mas_secondary_firearm_action').observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		select_split( 'select_mas_secondary_firearm_action', 'mas_secondary_firearm_action' );
	}
});

//Allow the left click of the mouse to trigger a new item
$('select_mas_secondary_firearm_obtained_legally').observe( 'change', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split( 'select_mas_secondary_firearm_obtained_legally', 'mas_secondary_firearm_obtained_legally' );
	}
});
//this is required to reset the list options
$('mas_secondary_firearm_obtained_legally').observe('focus',function(event){
	$('select_mas_secondary_firearm_obtained_legally').selectedIndex = '0';
});

$('select_mas_secondary_firearm_obtained_legally').observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		select_split( 'select_mas_secondary_firearm_obtained_legally', 'mas_secondary_firearm_obtained_legally' );
	}
});

/*
$("save_create_another").observe("click",function(event){
	$('new_record').value ='1';
	Event.stop(event);
	mas_save();
});
*/

//$("item_saveAndCloneToQuotes").observe("click",function(event){
//      $('new_record').value ='1';
//      Event.stop(event);
//      filter_save_and_clone_mas_to_quotes();
//});

document.observe("adminFormMas:clone", function(event) {
	$("mas_content").value ="";
	$('form-header').update('Create New Mas Item - <span style="color:#ff0000;">Cloned Copy</span>');
	$('form_id').value='0';
	
	$("ext_id").value = '0';	
	$("mas_live_id").value = '0';
		
	$("message_box").update("Saved");
	$('new_record').value ='0';	
	check.each(function(c){
		check.set(c.key, false );
	});	
});

document.observe("adminFormMas:clear", function(event) {				
	$('new_record').value ='0';	
	$("mas_txt_locations").update("");
	$("mas_notes").value = "";
	$("mas_content").value ="";
	$('mas_hidden_locations' ).value = "";			
	$('adminForm').getInputs('text').each( function(i){ i.value=""; });

	$("mas_category").selectedIndex = 0;	
	
	$("ext_id").value = '0';
	$("mas_live_id").value = '0';
	$("form_id").value = '0';
	locations = [];
	$("form-header").update("Create New Mas Item");
	$("message_box").update("");
	

	check.each(function(c){
		check.set(c.key, false );
	});
});

/*
if( Object.isElement( $("clear_form") ) )
{
	$("clear_form").observe("click",function(event){
		Event.stop(event);		
		this.fire("adminFormMas:clear");
	});	
}
*/

if( Object.isElement( $('item_publish') ) )
{
    $('item_publish').observe('click',function(event)
    {
        Event.stop(event);

        $('mas_hidden_locations' ).value = locations.compact().uniq().join(',');
        $('adminForm_task').value ='save_publish';
        new Ajax.Updater( 'message_box', $('adminForm').action,{
        parameters :  $('adminForm').serialize( true ),
        evalScripts : true
        });

        return false;
    });
}

function display_tip(el)
{
	pos = $( el ).viewportOffset();
	$('tool-tip-box').hide();
	title = el.readAttribute('title');
	$('tool-tip-box').update( title );
	$('tool-tip-box').setStyle({
		'position':'fixed',
		'top': pos.top + 'px',
		'left': pos.left + 'px',
		'z-index':100
	});
	$('tool-tip-box').show();
	new PeriodicalExecuter(function(pe){
	$('tool-tip-box').hide();
	pe.stop();
	},2);
}

$('adminForm').select('label').each(function(s){
	str = s.readAttribute('title');
	if( str == null || str.length == 0 ){return;}
	s.observe('click', function(event){
		Event.stop(event);
		display_tip( s );
	});
	s.observe('focus',function(event){Event.stop(event);});
	s.observe('mouseover',function(event){Event.stop(event);});
});

tab_order = tab_order_str.split(",");
tab_order.each(function(s, i){
	if( $(s) )
	{
		$(s).writeAttribute( 'tabindex', i+1 );
	}
});


//this catches return

$('adminForm').select('input').each( function( el ){

	if( el.readAttribute( 'type' ) != 'text' )
	{
		return;
	}
	el.observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		Event.stop(event);
		if( this.readAttribute( 'tabindex' ) )
		{
			tabto = this.readAttribute( 'tabindex' );
			++tabto;
			el = $('adminForm').select('[tabindex="' + tabto + '"]').first();
			if( Object.isElement( el ) )
			{
				el.focus();
			}
		}
	}
	});
});
		
dd.observe('keypress',function(event){
	if( event.keyCode == Event.KEY_TAB )
	{
		element = Event.element(event);
		if( element.readAttribute( 'tabindex' ) > 0 )
		{
			tabto = parseInt( element.readAttribute('tabindex') );
			++tabto;
		}else //if( current_tab >= tab_order.length )
		{
			tabto = 1;
		}
		el = this.select('[tabindex="' + tabto + '"]').first();
		if( Object.isUndefined(el) )
		{
			tabto = 1;
			el = this.select('[tabindex="' + tabto + '"]').first();
		}
		Event.stop(event);
		el.focus();
	}
});

Calendar.setup({
    inputField     :    "mas_date_of_shooting",     // id of the input field
    ifFormat       :    "%e %B %Y",      // format of the input field
    align          :    "Bl",           // alignment (defaults to "Bl")
    singleClick    :    true
});

//id = tab_order.first();
//$(id).focus();


},false);
//]]>

</script>

<script type="text/javascript">
   /*
   jQuery('#select_mas_location').on('change',function(event)
   {
        var URL = '<?php echo JRoute::_("index.php?option=com_gpo&controller=mas&task=getStates", false); ?>';
        URL += '&countryName=' + jQuery(this).val();
        jQuery(this).prop('disabled', true);
        jQuery('#select_mas_state_province').prop('disabled', true);
        jQuery.ajax({
                     url: URL, 
                     success: function(result) {
                         jQuery("#select_mas_state_province").html(result);
                         jQuery('#select_mas_location').prop('disabled', false);
                         jQuery('#select_mas_state_province').prop('disabled', false);
                     }
                 });
    
    });
    */
       
   jQuery("#select_mas_state_province").on('change',function(event) {
         jQuery("#mas_state_province").val(jQuery(this).val());
    });
  /* 
  jQuery("#select_mas_city").on('change',function(event) {
        jQuery("#mas_city").val(jQuery(this).val());
  });*/
</script>
<script type="text/javascript">
     jQuery( function() {
    jQuery( "#mas_date_of_shooting" ).datepicker({ dateFormat: 'dd MM yy'});
  } );
</script>
