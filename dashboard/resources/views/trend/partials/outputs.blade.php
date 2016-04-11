<?php
?>
<table class="table table-hover table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Result</th>
            <th>Category</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Amount</th>
            <th>Price</th>
            <th>Payment type</th>
        </tr>
    </thead>
    <tbody>
        @if($outputs)
            <?php
            foreach($outputs as $output):
                $temp_value = $output->temp ? $output->temp : (object)[
                    'cid' => '',
                    'pid' => '',
                    'quantity' => '',
                    'amount' => '',
                    'price' => '',
                    'paymentype' => '',
                ];
            ?>
            <tr>
                <td>{{$output->_id}}</td>
                <td>{{$output->result}}</td>
                <td>{{$temp_value->cid}}</td>
                <td>{{$temp_value->pid}}</td>
                <td>{{$temp_value->quantity}}</td>
                <td>{{$temp_value->amount}}</td>
                <td>{{$temp_value->price}}</td>
                <td>{{$temp_value->paymentype}}</td>
            </tr>
            <?php
            endforeach;
            ?>
        @endif
    </tbody>
</table>