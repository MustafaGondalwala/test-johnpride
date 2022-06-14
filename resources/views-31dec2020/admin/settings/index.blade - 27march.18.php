@component('layouts.admin')

    @slot('title')
        Admin - Manage Settings - {{ config('app.name') }}
    @endslot

    <?php

    $setting_id = 0;

    $setting_id = (isset($settingRow->id))?$settingRow->id:0;
    $title = (isset($settingRow->title))?$settingRow->title:'';
    $name = (isset($settingRow->name))?$settingRow->name:'';
    $value = (isset($settingRow->value))?$settingRow->value:'';
    $type = (isset($settingRow->type))?$settingRow->type:'text';

    $action_url = url('admin/settings');

    $name_readonly = '';

    $form_heading = 'Add Setting';

    if(is_numeric($setting_id) && $setting_id > 0){
        $action_url = url('admin/settings', $setting_id);
        $name_readonly = 'readonly';

        $form_heading = 'Update Setting';
    }

    ?>


    <div class="row">

    <div class="col-md-12">
            <h1>Settings</h1>

            @include('snippets.errors')
            @include('snippets.flash')
            </div>

                <div class="col-md-12">
                <div class="topsearch">

                <h4>{{ $form_heading }}</h4>
                <br>
                    <form method="POST" action="{{ $action_url }}" accept-charset="UTF-8" role="form" class="form-inline heightform" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }} ">
                            <label for="title" class="control-label required">Title:</label>

                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $title) }}" maxlength="255" />

                            <?php
                            /*@include('snippets.errors_first', ['param' => 'amount'])*/
                            ?>
                        </div>


                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} ">
                            <label for="name" class="control-label required">Name:</label>

                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $name) }}" maxlength="255" {{ $name_readonly }} />

                            <?php
                            /*@include('snippets.errors_first', ['param' => 'amount'])*/
                            ?>
                        </div>


                        <div class="form-group{{ $errors->has('value') ? ' has-error' : '' }} ">
                            <label for="value" class="control-label required">Value:</label>
                            
                            <?php
                            if($type == 'img'){
                                ?>
                                <input type="file" name="value">
                                <p style="font-size: 10px;">
                                <?php
                                if($name == 'CUSTOMER_LOGIN_BG_IMG'){
                                    echo '(Preferred Dimensions - Width:1600px, Height:1122px)';
                                   
                                }
                                elseif($name == 'CUSTOMER_LOGIN_IMG'){
                                   echo '(Preferred Dimensions - Width:390px, Height:572px)';
                                }

                                $path = 'assets/uploads/settings/';

                                $storage = Storage::disk('public');

                                 if(!empty($value) && $storage->exists($path . $value)){
                                    ?>
                                    <a href="javascript:void(0)" class="show_img" data-id="{{$setting_id}}">
                                    <img src="{{url('public/storage/'.$path.$value)}}" width="100" />
                                    </a>
                                    <?php
                                 }

                                ?>
                                </p>
                                <?php
                            }
                            elseif($type == 'text' || empty($type)){
                                ?>
                                <textarea name="value" id="value" class="" cols="25" />{{ old('value', $value) }}</textarea>
                                <?php
                            }
                            ?>

                            @include('snippets.errors_first', ['param' => 'value'])
                            
                        </div>

                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="hidden" name="setting_id" value="{{ $setting_id }}">

                        <button class="btn btn-success"><i class="fa fa-save"></i> Save</button>

                        <?php
                        if($setting_id > 0){
                            ?>
                            <a href="{{ url('admin/settings') }}" class="btn resetbtn btn-primary" title="Cancel">Cancel</a>
                            <?php
                        }
                        ?>

                    </form>

                </div>
            </div>






        <div class="col-md-12">

            @if (count($settings) > 0)
                <table class="table table-striped">
                    <tr>
                        <th>Name</th>
                        <th>Value</th>
                        <!-- <th>Status</th> -->
                        <th>Edit</th>
                    </tr>
                    @foreach ($settings as $setting)
                        <tr>
                            <td>
                            <?php
                            $name = config('custom.settings.' . $setting['name'] . '.name');

                            if(empty($name)){
                                echo $setting['title'];
                            }
                            else{
                               echo $name; 
                            }
                            ?>
                               
                            </td>

                            <td><?php echo (!empty($setting['value']))?$setting['value']:'';?></td>
                            
                             <?php
                            /*
                           <td>
                                @if ($setting['state'])
                                <i class="fa fa-check"></i> ON
                                @else
                                <i class="fa fa-times"></i> OFF
                                @endif
                            </td>
                            */
                            ?>

                            <td>
                            <?php
                            /*
                            <a href="{{ route('admin.settings.show', $setting['id']) }}" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i> Edit</a>
                            */
                            ?>
                            <a href="{{ url('admin/settings', $setting['id']) }}" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i> Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <div class="alert alert-warning">There are no settings at the present.</div>
            @endif
        </div>

    </div>

@endcomponent

<!-- Image Modal -->
  <div class="modal fade" id="imgModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          
        </div>
        <div class="modal-body">
          <img src="" style="width:100%">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

  <!-- End - Image Modal -->

  <script type="text/javascript">
      $(document).on("click", ".show_img", function(){
        //var setting_id = $(this).data("id");

        var img_src = $(this).find("img").attr("src");

        $("#imgModal .modal-body img").attr("src", img_src);
        $("#imgModal").modal();
      });
  </script>