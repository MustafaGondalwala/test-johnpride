<table>
    <thead>
        <tr>
            <th>State</th>
            <th>City</th>
            <th>Pincode</th>
            <th>Field1</th>
            <th>Field2</th>
            <th>Field3</th>
            <th>Cod Available</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>
        <?php

        if(!empty($pincodes)){
            foreach($pincodes as $pincode){

                $stateName = '';
                $cityName = '';

                if(!empty($pincode->pincodeState) && count($pincode->pincodeState) > 0){
                    $stateName = $pincode->pincodeState->name;
                }

                if(!empty($pincode->pincodeCity) && count($pincode->pincodeCity) > 0){
                    $cityName = $pincode->pincodeCity->name;
                }

                ?>

                <tr>
                    <td>{{$stateName}}</td>
                    <td>{{$cityName}}</td>
                    <td>{{$pincode->pin}}</td>
                    <td>{{$pincode->field1}}</td>
                    <td>{{$pincode->field2}}</td>
                    <td>{{$pincode->field3}}</td>
                    <td>
                        <?php
                        if($pincode->cod_available == 1){
                            echo 'Yes';
                        }
                        else{
                            echo 'No';
                        }
                    ?></td>
                    <td>{{ CustomHelper::getStatusStr($pincode->status) }}</td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>