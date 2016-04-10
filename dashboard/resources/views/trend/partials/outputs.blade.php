<table class="table table-hover table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Result</th>
            <th>Temp</th>
        </tr>
    </thead>
    <tbody>
        @if($outputs)
            @foreach($outputs as $output)
            <tr>
                <td>{{$output->_id}}</td>
                <td>{{$output->result}}</td>
                <td>
                @if($output->temp && is_array($output->temp))
                    @foreach($output->temp as $key => $value)
                        {{$key}} (<code class="fmonospaced">{{$value}}</code>) <br/>
                    @endforeach
                @endif
                </td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>