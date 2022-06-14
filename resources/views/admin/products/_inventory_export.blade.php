<table>
    <thead>
    <tr>
        <th>Product ID</th>
        <th>Product Name</th>
        <th>SKU</th>
        <th>Size</th>
        <th>Stock</th>
    </tr>
    </thead>
    <tbody>
        <?php

        $storage = Storage::disk('public');

        $img_path = 'products/';
        $thumb_path = $img_path.'thumb/';

        if(!empty($products)){
            foreach($products as $product){
                $productName = $product->name;
                $productInventory = $product->productInventory;

                //prd($product->toArray());

                if(!empty($productInventory)){

                    foreach($productInventory as $pi){

                        ?>
                        <tr>
                            <td>{{$product->id}}</td>
                            <td>{{$productName}}</td>
                            <td>{{$pi->sku}}</td>
                            <td>{{$pi->size_name}}</td>
                            <td>{{$pi->stock}}</td>
                        </tr>
                        <?php
                    }
                }
            }
        }
        elseif(!empty($inventories)){
            foreach($inventories as $inventory){

                $product = $inventory->inventoryProduct;

                $productName = $product->name;

                ?>
                <tr>
                    <td>{{$product->id}}</td>
                    <td>{{$productName}}</td>
                    <td>{{$inventory->sku}}</td>
                    <td>{{$inventory->size_name}}</td>
                    <td>{{$inventory->stock}}</td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>