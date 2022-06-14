@component('layouts.admin')

    @slot('title')
        Admin - Brands - {{ config('app.name') }}
    @endslot

   
  <div class="row">
    <div class="col-md-12">
      <div class="titlehead">
        <h1 class="pull-left">{{ $title }}</h1>
       
      </div>
    </div>
  </div>         
            
    <div class="row">

        <div class="col-md-12">

            <?php
            show_flash_msg();
            ?>

            <div class="table table-striped table-bordered">
        <form method="POST" action="{{ $form_action }}" accept-charset="UTF-8" role="form">
                {{ csrf_field() }}
         
               <div class="row">
                    
                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="control-label">Name:</label>
                            <?php echo old('name', $rec->name); ?>
                           
                        </div>
                    </div>

                     <div class="col-md-12">
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="control-label">Available Shortcodes:</label>
                             <?php echo old('shortcodes', $rec->shortcodes); ?>
                           
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="description" class="control-label required">Description:</label>
                            <textarea id="description" name="description" class="form-control" ><?php echo old('description', $rec->description); ?></textarea> 

                            @include('snippets.errors_first', ['param' => 'description'])
                        </div>
                    </div>

                   <div class="col-md-12">
                        <div class="form-group{{ $errors->has('subject') ? ' has-error' : '' }}">
                            <label for="subject" class="control-label required">Subject (required if Type is 'Email'):</label>
                            <textarea id="subject" name="subject" class="form-control" ><?php echo old('subject', $rec->subject); ?></textarea> 

                            @include('snippets.errors_first', ['param' => 'subject'])
                        </div>
                    </div>

                    <?php
                    $type = old('type', $rec->type);
                    ?>

                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                            <label for="c_password" class="control-label required">Type:</label>
                            <select name="type" class="form-control">
                                <option value="">--Select--</option>
                                <option value="email" {{ ($type == 'email')?'selected':'' }}>Email</option>
                                <option value="sms" {{ ($type == 'sms')?'selected':'' }}>sms</option>
                            </select>

                            @include('snippets.errors_first', ['param' => 'type'])
                        </div>
                    </div>

                    

                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                            <label for="c_password" class="control-label required">Content:</label>
                            <textarea id="content" name="content" class="form-control ckeditor" ><?php echo old('content', $rec->content); ?></textarea>    

                            @include('snippets.errors_first', ['param' => 'content'])
                        </div>
                    </div>                    
                    
                </div>               

                 
                <div class="row">


                </div>

                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update</button>
                    </div>
                </div>

            </form>
            </div>
            


        </div>

    </div>

@endcomponent
<script language="javascript" type="text/javascript" src="<?php echo url('public/js') ?>/ckeditor/ckeditor.js"></script>