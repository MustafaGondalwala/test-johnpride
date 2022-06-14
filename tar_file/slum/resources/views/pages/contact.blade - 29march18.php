<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Contact Us</title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="robots" content="index, follow"/>
<meta name="robots" content="noodp, noydir"/>

@include('common.head')

</head>

<body>

@include('common.user_header')

<section>
  <div class="contentArea">

@include('common.left_menu')

    <div class="rightBar">
      <div class="container-custom">
        <div class="panel panel-default"> 
          <div class="topHeading panel-heading"><span></span><span></span><span></span>Contact Us</div>

          @include('snippets.errors')
            @include('snippets.flash')

          @if (session('sccMsg'))
            <div class="alert alert-success alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ session('sccMsg') }}
            </div>
            @endif

            @if (session('errMsg'))
            <div class="alert alert-danger alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ session('errMsg') }}
            </div>
            @endif

          <div class="panel-body">
            <div class="midText text-center">
              <h2>We’d <i class="fa fa-heart-o" aria-hidden="true"></i> to help!</h2>
              <!-- <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. </p> -->
            </div>
            <div class="row ">


            <div class="col-md-12">

            <?php
            
            $agent_id=0;
           
            if(!empty($agent) && count($agent) > 0){
              $agent_name = trim($agent->first_name.' '.$agent->last_name);
              $agent_email = $agent->email;
              $agent_phone = $agent->phone;
              $agent_id= $agent->id; 
              ?>
               <div class="addressDetail">
               <strong>Account Manager:</strong> <span style="color: #686a78">{{$agent_name}}</span><br>
               <strong>Contact:</strong> <span style="color: #686a78">{{$agent_phone}}</span><br>
               </div>
               <hr>
              <?php
            }
            ?>           
              
            </div>


              <div class="col-md-6 contactfilds">


              <?php
              $first_name = (isset($user->first_name))?$user->first_name:'';
              $last_name = (isset($user->last_name))?$user->last_name:'';

              $name = trim($first_name.' '.$last_name);

              $email = (isset($user->email))?$user->email:'';
              $phone = (isset($user->phone))?$user->phone:'';
              ?>

                <form class="form-horizontal" method="POST" action="{{url('contact')}}">
                {{ csrf_field() }}

                <input type="hidden" name="agent_id" value="<?php echo $agent_id;  ?>"  >

                   <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <div class="col-sm-12 contactForm">
                    <label for="inputEmail3" class="control-label">Name :</label>
                      <input type="text" name="name" class="form-control" id="inputEmail3" value="{{old('name', $name)}}">
                      <img src="{{url('public/assets')}}/img/cn1.png" alt="cn1" />
                    </div>
                    <span style="color:red;">{{$errors->first('Name')}}</span>
                  </div>
                  <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <div class="col-sm-12 contactForm">
                    <label for="inputEmail3" class="control-label">Email :</label>
                      <input type="text" name="email" class="form-control" id="inputEmail3" value="{{old('email', $email)}}">
                      <img src="{{url('public/assets')}}/img/cn2.png" alt="cn2" />
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-12 contactForm">
                    <label for="inputEmail3" class="control-label">Phone :</label>
                      <input type="text" name="phone" class="form-control" id="inputEmail3" value="{{old('phone', $phone)}}">
                      <img src="{{url('public/assets')}}/img/cn3.png" alt="cn3" />
                    </div>
                  </div>
                  
                  <div class="form-group{{ $errors->has('option') ? ' has-error' : '' }}">
                    <div class="col-sm-12 contactForm" >
                               
                     <select name="option" class="form-control selectoption">

                     <option value="">Choose an option</option>
                     
                      <?php
                      $option = old('option');

                      $contact_options = config('custom.contact_options');
                      //pr($contact_options);

                      if(count($contact_options)){
                        foreach($contact_options as $option_key=>$option_val){
                          $selected = '';
                          if($option_key == $option){
                            $selected = 'selected';
                          }
                          ?>
                          <option value="{{$option_key}}" {{$selected}}>{{$option_val}}</option>
                          <?php
                        }
                      }
                      ?>
                    </select>
                    </div>
                  </div>
                  
                  <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                    <div class="col-sm-12 contactForm">
                    <label for="inputEmail3" class="msg control-label">Message :</label>
                     <textarea name="message" class="form-control" rows="3">{{old('message')}}</textarea>
                      <img src="{{url('public/assets')}}/img/cn4.png" alt="cn4" />
                    </div>
                  </div>

                  <?php
                  /*
                  <div class="form-group">
                   <div class="col-sm-4 contactFormCap contactForm">
                   <?php
                   //echo $captcha_img;
                   //prd($CaptchaObj->generateCaptcha());
                   //$CaptchaObj->generateCaptcha()
                   ?>
                   <img src="{{url('captcha')}}">
                    
                  </div>
                    <div class="col-sm-8 contactForm {{ $errors->has('code') ? 'has-error' : '' }}">
                     <input type="text" name="code" class="last-child form-control" id="inputEmail3" placeholder="">
                    </div>
                  </div>
                  */
                  ?>

                  <div class="col-md-3 noPaddings">
                    <button class="btn btnSendNew btn-send btn-default" id="verify">Send <i class="fa fa-long-arrow-right" aria-hidden="true"></i></button>
                  </div>
                </form>
              </div>
              <div class="col-md-6 paddingLeftLarge">
                <?php
                $cms = CustomHelper::GetCMSPage("contact");
                echo $cms['content'];
                ?>
                
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 kcMap">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3500.890205675561!2d77.26107031508329!3d28.663005982405778!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cfcf2b2453de3%3A0x6090cbaaf46f62ce!2sK+C+Garments+Private+Limited!5e0!3m2!1sen!2sin!4v1507277851780" width="100%" height="320" frameborder="0" style="border:0" allowfullscreen></iframe>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@include('common.footer')

</body>
</html>
