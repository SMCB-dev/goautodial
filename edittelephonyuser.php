<?php

require_once('./php/CRMDefaults.php');
require_once('./php/UIHandler.php');
//require_once('./php/DbHandler.php');
require_once('./php/LanguageHandler.php');
require('./php/Session.php');
require_once('./php/goCRMAPISettings.php');

// initialize structures
$ui = \creamy\UIHandler::getInstance();
$lh = \creamy\LanguageHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

$userid = NULL;
if (isset($_POST["userid"])) {
	$userid = $_POST["userid"];
}
if(isset($_POST["role"])){
	$userrole = $_POST["role"];
}

$voicemails = $ui->API_goGetVoiceMails();
$user_groups = $ui->API_goGetUserGroupsList();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Users</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
		<!-- Forms and actions -->
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>
        	
        	<!-- =============== BOOTSTRAP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/bootstrap.css" id="bscss">
				<!-- =============== APP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/app.css" id="maincss">

        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			})
		</script>
    </head>
    <style>
    	select{
    		font-weight: normal;
    	}
    </style>
    <?php print $ui->creamyBody(); ?>
        <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1 style="font-weight:normal;">
                        <?php $lh->translateText("Users"); ?>
                        <small><?php $lh->translateText("Edit Users"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("telephony"); ?></li>
                        <?php
							if(isset($_POST["userid"])){
						?>	
							<li><a href="./telephonyusers.php"><?php $lh->translateText("Users"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

                <?php
                	if($userrole == "9"){
					echo "<br/><br/>";
					print $ui->getUnauthotizedAccessMessage();

					}else{
                ?>

               <!-- Main content -->
                <section class="content">
					<div class="panel panel-default">
					<!-- standard custom edition form -->
					<?php
					$userobj = NULL;
					$errormessage = NULL;

				//echo $userrole;
				
					if(isset($userid)) {
						//$db = new \creamy\DbHandler();
						//$customerobj = $db->getDataForCustomer($customerid, $customerType);
						
						$url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
						$postfields["goUser"] = goUser; #Username goes here. (required)
						$postfields["goPass"] = goPass; #Password goes here. (required)
						$postfields["goAction"] = "goGetUserInfo"; #action performed by the [[API:Functions]]. (required)
						$postfields["responsetype"] = responsetype; #json. (required)
						$postfields["user_id"] = $userid; #Desired User ID (required)

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_TIMEOUT, 100);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
						$data = curl_exec($ch);
						curl_close($ch);
						$output = json_decode($data);
						
						//var_dump($output);
						// print_r($data);
						
						if ($output->result=="success") {
						# Result was OK!
							for($i=0;$i<count($output->userno);$i++){
					
						?>

						<div class="panel-body">
						<legend>MODIFY USER : <u><?php echo $output->userno[$i];?></u></legend>

							<form id="modifyuser">

							<!-- Custom Tabs -->
							<div role="tabpanel">
							<!--<div class="nav-tabs-custom">-->
								<ul role="tablist" class="nav nav-tabs">
									<li class="active"><a href="#tab_1" data-toggle="tab"><em class="fa fa-gear fa-lg"></em> Basic Settings</a></li>
									<li><a href="#tab_2" data-toggle="tab"><em class="fa fa-gears fa-lg"></em> Advanced Settings</a></li>
								</ul>
				               <!-- Tab panes-->
				               <div class="tab-content">

					               	<!-- BASIC SETTINGS -->
					                <div id="tab_1" class="tab-pane fade in active">

										<input type="hidden" name="modifyid" value="<?php echo $userid;?>" />
									
										<fieldset>
											<div class="form-group mt">
												<label for="fullname" class="col-sm-2 control-label">Fullname</label>
												<div class="col-sm-10 mb">
													<input type="text" class="form-control" name="fullname" id="fullname" value="<?php echo $output->full_name[$i];?>" placeholder="Fullname">
												</div>
											</div>
											<div class="form-group">
												<label for="email" class="col-sm-2 control-label">Email</label>
												<div class="col-sm-10 mb">
													<input type="text" class="form-control" name="email" id="email" value="<?php echo $output->email[$i];?>" placeholder="Email">
													<small><span id="email_check"></span></small>
												</div>
											</div>
											<div class="form-group">
												<label for="usergroup" class="col-sm-2 control-label">User Group</label>
												<div class="col-sm-10 mb">
													<select class="form-control" id="usergroup" name="usergroup">
														<?php
															for($a=0;$a<count($user_groups->user_group);$a++){
														?>
															<option value="<?php echo $user_groups->user_group[$a];?>" <?php if($output->user_group[$i] == $user_groups->user_group[$a]){echo "selected";}?> >  
																<?php echo $user_groups->user_group[$a].' - '.$user_groups->group_name[$a];?>  
															</option>
														<?php
															}
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="status" class="col-sm-2 control-label">Status</label>
												<div class="col-sm-10 mb">
													<select class="form-control" name="status" id="status">
													<?php
														$status = NULL;
														if($output->active[$i] == "Y"){
															$status .= '<option value="Y" selected> Active </option>';
														}else{
															$status .= '<option value="Y" > Active </option>';
														}
														
														if($output->active[$i] == "N" || $output->active[$i] == NULL){
															$status .= '<option value="N" selected> Inactive </option>';
														}else{
															$status .= '<option value="N" > Inactive </option>';
														}
														echo $status;
													?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="userlevel" class="col-sm-2 control-label">User Level</label>
												<div class="col-sm-10 mb">
													<select class="form-control" name="userlevel" id="userlevel">
													<?php
														$userlevel = NULL;
															if($output->user_level[$i] == "1"){
																$userlevel .= '<option value="1" selected> 1 </option>';
															}else{
																$userlevel .= '<option value="1" > 1 </option>';
															}
															if($output->user_level[$i] == "2"){
																$userlevel .= '<option value="2" selected> 2 </option>';
															}else{
																$userlevel .= '<option value="2" > 2 </option>';
															}
															if($output->user_level[$i] == "3"){
																$userlevel .= '<option value="3" selected> 3 </option>';
															}else{
																$userlevel .= '<option value="3" > 3 </option>';
															}
															if($output->user_level[$i] == "4"){
																$userlevel .= '<option value="4" selected> 4 </option>';
															}else{
																$userlevel .= '<option value="4" > 4 </option>';
															}
															if($output->user_level[$i] == "5"){
																$userlevel .= '<option value="5" selected> 5 </option>';
															}else{
																$userlevel .= '<option value="5" > 5 </option>';
															}
															if($output->user_level[$i] == "6"){
																$userlevel .= '<option value="6" selected> 6 </option>';
															}else{
																$userlevel .= '<option value="6" > 6 </option>';
															}
															if($output->user_level[$i] == "7"){
																$userlevel .= '<option value="7" selected> 7 </option>';
															}else{
																$userlevel .= '<option value="7" > 7 </option>';
															}
															if($output->user_level[$i] == "8"){
																$userlevel .= '<option value="8" selected> 8 </option>';
															}else{
																$userlevel .= '<option value="8" > 8 </option>';
															}
															if($output->user_level[$i] == "9"){
																$userlevel .= '<option value="9" selected> 9 </option>';
															}else{
																$userlevel .= '<option value="9" > 9 </option>';
															}
														echo $userlevel;
													?>
														
													</select>
												</div>
											</div>
										</fieldset>
										<fieldset>
											<div class="form-group">
												<label for="phone_login" class="col-sm-2 control-label">Phone Login</label>
												<div class="col-sm-10 mb">
													<input type="text" class="form-control" name="phone_login" id="phone_login" value="<?php echo $output->phone_login[$i];?>" placeholder="Phone Login">
												</div>
											</div>
											<div class="form-group">
												<label for="phone_password" class="col-sm-2 control-label">Phone Password</label>
												<div class="col-sm-10 mb">
													<input type="text" class="form-control" name="phone_password" id="phone_password" value="<?php echo $output->phone_pass[$i];?>" placeholder="Phone Password">
												</div>
											</div>									
											<div class="form-group">
												<label for="voicemail" class="col-sm-2 control-label">Voicemail</label>
												<div class="col-sm-10 mb">
													<select class="form-control" name="voicemail" id="voicemail">
														<?php
															if($voicemails == NULL){
														?>
															<option value="" selected>--No Voicemails Available--</option>
														<?php
															}else{
															for($a=0;$a<count($voicemails->voicemail_id);$a++){
														?>
																<option value="<?php echo $voicemails->voicemail_id[$i];?>" <?php if($output->voicemail_id[$i] == $voicemails->voicemail_id[$a]){echo "selected";}?> >
																	<?php echo $voicemails->voicemail_id[$a].' - '.$voicemails->fullname[$a];?>
																</option>									
														<?php
																}
															}
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="change_pass" class="col-sm-2 control-label">Change Password?</label>
												<div class="col-sm-10 mb">
													<select class="form-control " name="change_pass" id="change_pass">
														<option value="N" selected> No </option>
														<option value="Y" > Yes </option>
													</select>
												</div>
											</div>
											<div class="form-group" id="form_password" style="display:none;">
												<label for="password" class="col-sm-2 control-label">Password</label>
												<div class="col-sm-10 mb">
													<input type="text" class="form-control" name="password" id="password" value="<?php echo $output->password[$i];?>" placeholder="Password">
												</div>
											</div>
										</fieldset>
								   	</div><!-- tab 1 -->

								   	<!-- ADVANCED SETTINGS -->
								   	<div id="tab_2" class="tab-pane fade in">
						       			<input type="hidden" name="agent_choose_ingroup" value="0">
						       			<input type="hidden" name="agent_choose_blended" value="0">
						       			<input type="hidden" name="scheduled_callbacks" value="1">
						       			<input type="hidden" name="agent_call_manual" value="1">

						       			<fieldset>
						       				<div class="form-group mt">
												<label for="hotkeys" class="col-sm-2 control-label">HotKeys</label>
												<div class="col-sm-10 mb">
													<select class="form-control" name="hotkeys" id="hotkeys">
													<?php
														$status = NULL;
														if($output->hot_keys[$i] == "0"){
															$status .= '<option value="Y" selected> Active </option>';
														}else{
															$status .= '<option value="Y" > Active </option>';
														}
														
														if($output->hot_keys[$i] == "1" || $output->hot_keys[$i] == NULL){
															$status .= '<option value="N" selected> Inactive </option>';
														}else{
															$status .= '<option value="N" > Inactive </option>';
														}
														echo $status;
													?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="agent_recordings" class="col-sm-2 control-label">Agent Recordings</label>
												<div class="col-sm-10 mb">
													<select class="form-control" name="agent_recordings" id="agent_recordings">
														<option value="0"> 0 </option>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="agent_transfers" class="col-sm-2 control-label">Agent Transfers</label>
												<div class="col-sm-10 mb">
													<select class="form-control" name="agent_transfers" id="agent_transfers">
														<option value="1"> 1 </option>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="closer_default_blended" class="col-sm-2 control-label">Closer Default Blended</label>
												<div class="col-sm-10 mb">
													<select class="form-control" name="closer_default_blended" id="closer_default_blended">
														<option value="1"> 1 </option>
													</select>
												</div>
											</div>
										</fieldset>		
									</div>
									
								   	<!-- FOOTER BUTTONS -->
								   	<div id="modifyUSERresult"></div>

								   	<fieldset>
				                        <div class="form-group">
				                           <div class="pull-right">
				                              
				                              	<div class="col-sm-6">
													<a href="telephonyusers.php" type="button" class="btn btn-danger pull-right"><i class="fa fa-close"></i> Cancel </a>
				                           		</div>
				                              	<div class="col-sm-6">
				                                	<button type="submit" class="btn btn-primary pull-right" id="modifyUserOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
												</div>
				                           </div>
				                        </div>
				                    </fieldset>
							   		</div>
				            	</div><!-- end of tab content -->
				       		</form>
	                    	</div><!-- tab panel -->

						<?php
							}
						} else {
						# An error occured
							echo $output->result;
						}
                	}
                }
					
					?>
					</div><!-- body -->
                </section>
				<!-- /.content -->
            </aside><!-- /.right-side -->
			
            <?php //print $ui->creamyFooter(); ?>
			
        </div><!-- ./wrapper -->

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>
		<!-- SLIMSCROLL-->
   		<script src="theme_dashboard/js/slimScroll/jquery.slimscroll.min.js"></script>

		<script type="text/javascript">
			$(document).ready(function() {

				$('#change_pass').on('change', function() {
				//  alert( this.value ); // or $(this).val()
					if(this.value == "Y") {
					  $('#form_password').show();
					}
					if(this.value == "N") {
					  $('#form_password').hide();
					}
				});

				/** 
				 * Modifies a telephony user
			 	 */
				
				$('#modifyUserOkButton').click(function(){
					
					$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
					$('#modifyUserOkButton').prop("disabled", true);

					var validate_email = 0;

	                var x = document.forms["modifyuser"]["email"].value;
	                var atpos = x.indexOf("@");
	                var dotpos = x.lastIndexOf(".");
	                if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length) {
	                    validate_email = 1;
	                }

	                if(validate_email == 0){
	                	$.ajax({
                            url: "./php/ModifyTelephonyUser.php",
                            type: 'POST',
                            data: $("#modifyuser").serialize(),
                            success: function(data) {
                              // console.log(data);
                                if (data == 1) {
								<?php 
									$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
									print $ui->fadingInMessageJS($errorMsg, "modifyUSERresult"); 
								?>
								$('#update_button').html("<i class='fa fa-check'></i> Update");
								$('#modifyUserOkButton').prop("disabled", false);
								window.setTimeout(function(){location.replace("./telephonyusers.php")},2000);
								} else {
								<?php 
									$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data"), false, true);
									print $ui->fadingInMessageJS($errorMsg, "modifyUSERresult");
								?>
								$('#update_button').html("<i class='fa fa-check'></i> Update");
								$('#modifyUserOkButton').prop("disabled", false);	
								}
                            }
                        });
					}else{
						$("#email_check").html("<font color='red'>Input a Valid Email Address</font>");
						$('#email_check').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
					}
				return false;
				});
				 
			});
		</script>

    </body>
</html>
