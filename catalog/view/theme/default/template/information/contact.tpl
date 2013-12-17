<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content" class="cont-white"><?php echo $content_top; ?>
<?/*
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  */?>
  <h1><?php echo $heading_title; ?></h1>  
 
  <iframe width="730" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.pl/maps?f=q&amp;source=s_q&amp;hl=pl&amp;geocode=&amp;q=gda%C5%84sk,+wrzeszcz&amp;aq=&amp;sll=54.359257,18.693752&amp;sspn=0.071519,0.198441&amp;t=m&amp;ie=UTF8&amp;hq=&amp;hnear=Wrzeszcz+Gda%C5%84sk,+pomorskie&amp;ll=54.359257,18.693752&amp;spn=0.017505,0.062056&amp;z=14&amp;iwloc=A&amp;output=embed"></iframe>
 
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
    <h2 style="margin-top:10px;"><?php echo $text_location; ?></h2>
    <div class="contact-info" style="padding:10px 0;">
      <div class="content" style="border:none; padding:0;"><div class="left"><b><?php echo $text_address; ?></b><br />
        <?php echo $store; ?><br />
        <?php echo $address; ?></div>
      <div class="right">
        <?php if ($telephone) { ?>
        <b><?php echo $text_telephone; ?></b><br />
        <?php echo $telephone; ?><br />
        <br />
        <?php } ?>
        <?php if ($fax) { ?>
        <b><?php echo $text_fax; ?></b><br />
        <?php echo $fax; ?>
        <?php } ?>
      </div>
    </div>
    </div>
    <h2><?php echo $text_contact; ?></h2>
    <div class="content">
    <b><?php echo $entry_name; ?></b><br />
    <input type="text" name="name" value="<?php echo $name; ?>" />
    <br />
    <?php if ($error_name) { ?>
    <span class="error"><?php echo $error_name; ?></span>
    <?php } ?>
    <br />
    <b><?php echo $entry_email; ?></b><br />
    <input type="text" name="email" value="<?php echo $email; ?>" />
    <br />
    <?php if ($error_email) { ?>
    <span class="error"><?php echo $error_email; ?></span>
    <?php } ?>
    <br />
    <b><?php echo $entry_enquiry; ?></b><br />
    <textarea name="enquiry" cols="40" rows="10" style="width: 99%;"><?php echo $enquiry; ?></textarea>
    <br />
    <?php if ($error_enquiry) { ?>
    <span class="error"><?php echo $error_enquiry; ?></span>
    <?php } ?>
    <br />
    <b><?php echo $entry_captcha; ?></b><br />
    <input type="text" name="captcha" value="<?php echo $captcha; ?>" />
    <br />
    <img src="index.php?route=information/contact/captcha" alt="" />
    <?php if ($error_captcha) { ?>
    <span class="error"><?php echo $error_captcha; ?></span>
    <?php } ?>
    </div>
    <div class="buttons">
      <div class="right"><input type="submit" value="<?php echo $button_continue; ?>" class="button" /></div>
    </div>
  </form>
  <?php echo $content_bottom; ?></div>
  <div id="panel-right">

  </div>
  
  <script type="text/javascript">
	$(document).ready(function() {
	/*
	setTimeout(function () {
	
	$("#szukaj #left").css('position','absolute');
	$("#szukaj #wyszukiwanie").css('position','absolute');
	$("#szukaj #wyszukiwanie").css('right','0');
	
	$("#szukaj #left").animate({
		right: '-25',
		bottom: '-863',
		width: '200'
	}, 700, function() {
		$("#szukaj #left").css('box-shadow','none');
		$("#szukaj #left").css('background','#fff');
		$("#szukaj #left").css('border-color','#DBDEE1');
	});
  
	$("#szukaj #wyszukiwanie").animate({
		right: '-25',
		bottom: '-677',
		width: '200'
	}, 700, function() {
		$("#szukaj #wyszukiwanie").css('box-shadow','none');
		$("#szukaj #wyszukiwanie").css('background','#fff');
		$("#szukaj #wyszukiwanie").css('border-color','#DBDEE1');	
		$("#sliderbg").hide('medium');
		$("#szukaj #wyszukiwanie").css('bottom','-490px');
		$("#szukaj #wyszukiwanie").css('height','337px');
		$("#szukaj #left").css('bottom','-672px');

		});
			
	},500);
	*/
	
		$("#sliderbg").hide(100);
		$("#szukaj #wyszukiwanie").css('position','absolute');
		$("#szukaj #wyszukiwanie").css('bottom','-490px');
		$("#szukaj #wyszukiwanie").css('height','337px');
		$("#szukaj #wyszukiwanie").css('width','200px');
		$("#szukaj #wyszukiwanie").css('right','-25px');
		$("#szukaj #wyszukiwanie").css('box-shadow','none');
		$("#szukaj #wyszukiwanie").css('border-color','#DBDEE1');
		$("#szukaj #left").css('position','absolute');
		$("#szukaj #left").css('bottom','-672px');
		$("#szukaj #left").css('width','200px');
		$("#szukaj #left").css('box-shadow','none');
		$("#szukaj #left").css('right','-25px');		
		$("#szukaj #left").css('border-color','#DBDEE1');
		
	});
  </script>

<?php echo $footer; ?>