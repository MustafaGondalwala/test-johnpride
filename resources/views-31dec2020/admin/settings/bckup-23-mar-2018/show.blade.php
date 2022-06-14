@component('layouts.admin')

    @slot('title')
        Admin - Edit Setting - {{ config('app.name') }}
    @endslot

    <?php
    //prd($setting);
    ?>

    <div class="row">

        <div class="col-md-12">

            <?php
            $name = config('custom.settings.' . $setting['name'] . '.name');

            if(empty($name)){
                $name = $setting['title'];
            }
         ?>

            <h2>Edit Setting - {{ $name }}</h2>

            @include('snippets.errors')
            @include('snippets.flash')

            <form method="POST" action="{{ route('admin.settings.update', $setting['id']) }}" class="form-horizontal" accept-charset="UTF-8" role="form">
                {{ csrf_field() }}
                {{ method_field('PUT') }}

                <div class="row">
                    <div class="col-sm-12 col-md-6">

                        <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
                            <div class="col-md-6">
                                <label for="state" class="control-label">Setting State:</label>

                                <input type="checkbox" id="state" name="state" {{ old('state', $setting['state']) ? ' checked' : '' }} />

                                @include('snippets.errors_first', ['param' => 'state'])

                                <p class="help-block">
                                    Boolean (on/off) setting state
                                </p>
                            </div>
                        </div>

                        <?php
                        if(!empty($setting['value'])){
                            ?>
                            <div class="form-group{{ $errors->has('value') ? ' has-error' : '' }}">

                            <div class="col-md-6">
                                <label for="value" class="control-label">Setting Value:</label>

                                <input type="text" name="value" class="form-control" value="{{old('value', $setting['value'])}}">
                            </div>

                            </div>
                            <?php
                        }
                        ?>

                       

                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success pull-right" title="Update this setting"><i class="fa fa-save"></i> Save</button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>

        </div>

    </div>

@endcomponent