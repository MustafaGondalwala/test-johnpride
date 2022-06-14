<style> .lang-select { width: auto !important;padding: 4px 4px 5px;}</style>
<?php
$cms = CustomHelper::GetCMSPage("terms");
$privacy_url = url('privacy');
if(Auth::check()){
  $privacy_url = url('users/privacy');
}
?>

<div class="panel panel-default"> 
  <div class="topHeading panel-heading">
    <span></span><span></span><span></span>{{trans('custom.terms_conditions')}}</div>
    <div class="panel-body pad_sm">
      <div class="shipMent termText">              
        <h2>
          {{trans('custom.terms_last_updated_on', ['month'=>date('F',strtotime($cms['updated_at'])), 'date_year'=>date('d,Y',strtotime($cms['updated_at']))])}}

          <!-- Last Updated On  {{ date('F',strtotime($cms['updated_at'])) }} <strong> {{ date('d,Y',strtotime($cms['updated_at'])) }}</strong> -->
      </h2>
        <p><span><a href="{{ $privacy_url }}">{{trans('custom.click_hear')}}</a></span> {{trans('custom.to_refer_our_privacy_policy')}}</p>
      </div>
      <?php
      echo $cms['content'];
      ?>
    </div>
  </div>