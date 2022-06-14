@component('admin.layouts.main')

    @slot('title')
        Admin - Upload Products - {{ config('app.name') }}
    @endslot

    <div class="row">

    <?php
    $back_url = (request()->has('back_url'))?request()->input('back_url'):'';
    if(empty($back_url)){
        $back_url = 'admin/products';
    }
    ?>

			 <div class="col-md-12">

            <h2>Upload Product(s)</h2>
			</div>
			 <div class="col-md-12">
			
            @include('snippets.errors')
            @include('snippets.flash')

            <?php
            if(session()->has('scc_msg')){
                echo session('scc_msg');
            }
            ?>

            <?php
            if(session()->has('err_msg')){
                echo session('err_msg');
            }
            ?>

			<div class="bgcolor">
            <form method="POST" action="" accept-charset="UTF-8" enctype="multipart/form-data" role="form">
                {{ csrf_field() }}

                <p>
                    <span style="font-family: arial, sans-serif;"><i><b>Instructions on use:</b></i> <br>
                        Please make sure you are trying to use a CSV/XLS/XLSX file only with the extension .csv/.xls/.xlsx at the end of the file name. <br>
                        You can create your files using MS-Excel or similar software.<br><br></span>
                        
                        <a href="{{ url('assets/uploads/sample_upload_products.xlsx') }}" class="btn-link btn-xs">View Sample file</a></font><br><br>
                    </p>


                <div class="form-group col-md-3{{ $errors->has('upload') ? ' has-error' : '' }}">
                    <label for="upload" class="control-label required">Upload Products:</label>

                    <input type="file" name="upload" id="upload" />

                    @include('snippets.errors_first', ['param' => 'upload'])
                </div>
				
				<div class="clearfix"></div>
                <div class="form-group col-md-12">
                    <input type="hidden" name="back_url" value="{{ $back_url }}" >
                    <button type="submit" class="btn btn-success" title="Update this product"><i class="fa fa-save"></i> Save</button>

                    <a href="{{ url($back_url) }}" class="btn btn-lg btn-primary" title="Click here to cancel">Cancel</a>
                </div>

				<br/><br/>
                
            </form>
			</div>
        </div>
		
		 
        <?php
        /*
        <div class="col-md-5">
            <div class="bgcolor">
            @include('admin.products.inventory.index', ['product' => $product, 'inventoryItems' => $product->inventoryItems])
            </div>
        </div>
        */
        ?>
    </div>



@endcomponent