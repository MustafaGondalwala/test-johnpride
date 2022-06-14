<style> .lang-select { width: auto !important;padding: 4px 4px 5px;}</style>
<?php $cms = CustomHelper::GetCMSPage("privacy"); ?>
        <div class="panel panel-default"> 
          <div class="topHeading panel-heading"><span></span><span></span><span></span>{{trans('custom.privacy_policy')}}</div>
          <div class="panel-body pad_sm">
            <div class="row">
              <div class="col-md-12 terms">
				<?php
				echo $cms['content'];
				?>
              </div>              
            </div>
            </div>
          </div>
