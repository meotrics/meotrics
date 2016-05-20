@extends('layout.master')

@section('style')
  <style>
    #segment_index select{
      width: auto;
      min-width: 150px;
    }
    #segment_index .selections > div > *{
      display: inline-block;
      vertical-align: middle;
      margin-right: 5px;
    }
    #segment_index .selections label, #segment_index .segment_filters label{
      width: 16.66666667%;
    }
    #addition_filter{
      margin-right: 0px;
    }
    #addition_filter .filter_wrapper{
      margin-bottom: 5px;
    }
    #addition_filter .filter_wrapper > * {
      width: auto;
      display: inline-block;
      margin-right: 10px;
    }
    #addition_filter .condition_value input{
      display: inline-block;
      vertical-align: middle;
      width: auto;
    }
    #addition_filter .condition_value input.string{
      width: 200px;
    }
    #addition_filter .condition_value input.range{
      width: 98px;
    }
    #addition_filter .remove-btn{
      padding: 10px;
    }
    .form-group{
      overflow: auto;
    }
  </style>
@endsection

@section('script')
  <script src="{{asset('js/cs.segmentop.js')}}"></script>
  <script src="{{asset('js/cs.segment.query.js')}}"></script>
  <script>

    $(document).ready(function(){

      var actions  = {!! json_encode($actions)  !!};
      var props    = {!! json_encode($props)    !!};

      var additionFilterElement = $('#addition_filter')

      var dispatchChangeEvent = function(el){
        // Dispatch change event
        var evt = document.createEvent('HTMLEvents');
        evt.initEvent('change', false, true);
        el.dispatchEvent(evt);
      }
      var onSelectSegment = function(){
        console.log('onSegment selected !');
      }
      var appendAdditionFilter = function(){
        // Filter type selection
        var wrapper = document.createElement('div');
        wrapper.setAttribute('class', 'filter_wrapper');
        var filterType = document.createElement('select');
        filterType.setAttribute('class', 'filter_type form-control');
        filterType.addEventListener('change', function(ev){
          // Refresh filter operators
          var typeCode = $(this).val();
          var type = _.find(props, function(prop){ return prop.code == typeCode });
          $(this).next().empty(); // Clear current options
          for(var i = 0; i < type.operators.length; i++){
            var op = document.createElement('option');
            op.setAttribute('value', type.operators[i].code);
            op.innerHTML = type.operators[i].name;
            $(this).next().append(op);
          }
          dispatchChangeEvent($(this).next()[0]);
        });
        for(var i = 0; i < props.length; i++){
          var option = document.createElement('option');
          option.setAttribute('value', props[i].code);
          option.innerHTML = props[i].name;
          filterType.appendChild(option);
        }
        wrapper.appendChild(filterType);
        // Filter Operator selection
        var filterOperator = document.createElement('select');
        filterOperator.setAttribute('class', 'filter_operator form-control');
        filterOperator.addEventListener('change', function(ev){
          // Refresh operator input
          var typeCode = $(this).prev().val();
          var type = _.find(props, function(prop){ return prop.code == typeCode });
          var operatorCode = $(this).val();
          var conditionValue = $(this).next();
          if(conditionValue.length > 0) conditionValue.remove(); // Remove old element
          conditionValue = document.createElement('div');
          conditionValue.setAttribute('class', 'condition_value');
          if(operatorCode == 'in'){
            // Range
            conditionValue.innerHTML = '\
              <input class="form-control range" name="fromValue" placeholder="From">\
              <input class="form-control range" name="toValue"  placeholder="To">\
            ';
          } else {
            // String
            conditionValue.innerHTML = '<input class="form-control string" name="conditionValue">';
          }
          $(this).parent().append(conditionValue);
        })
        wrapper.appendChild(filterOperator);
        additionFilterElement.append(wrapper);
        // Remove filter button
        var filterRemoveBtn = document.createElement('a');
        filterRemoveBtn.setAttribute('href', 'javascript:void(0)');
        filterRemoveBtn.setAttribute('class', 'remove-btn');
        filterRemoveBtn.innerHTML = '<i class="fa fa-trash"></i>';
        filterRemoveBtn.addEventListener('click', function(ev){
          $(wrapper).remove();
        });
        wrapper.insertBefore(filterRemoveBtn, filterType);

        dispatchChangeEvent(filterType);
      }
      var removeAdditionFilter = function(){

      }

      appendAdditionFilter();
      window.appendAdditionFilter = appendAdditionFilter;
    })

    // var sq;
    // var data = [];

    // for (var i in actions) {
    //  var action = actions[i];
    //  var fis = [];
    //  for (var f in action.fields)
    //    fis.push({name: action.fields[f].pname, code: action.fields[f].pcode});

    //  data.push({type: 'action', id: action._id, name: action.name, fields: fis});
    // }

    // for (var i in props) {
    //  var prop = props[i];
    //  data.push({type: 'prop', name: prop.name, dpname: prop.dpname});
    // }

    // onPageLoad(function () {
    //  sq = new SegmentQuery();

    //  sq.produce(function ($query) {
    //    $('.id_query').append($query);

    //  }, data);
    // });


    // $('.id_exebtn').click(function () {
    //  $(this).attr('disabled', true);


    //  var $field1 = $('.id_field1-43');
    //  var $field2 = $('.id_field1-54');

    //  $.get('/segment/execute', {
    //    id: -1,
    //    name: 'Draf',
    //    query: sq.query(),
    //    f1: $field1.val(),
    //    f2: $field2.val()
    //  }, function (data) {
    //    console.log(data);
    //  });

    // });


  </script>
@endsection

@section('content')
  <div class="card row" id="segment_create">
    <div class="content col-sm-12">
      <div class="form-group selections">
        <label class="text-muted uppercase"><b>Create new segment</b></label>
        <a href="{{ URL::to('/segment') }}" class="pull-right uppercase text-muted">
          <b><i class="fa fa-chevron-left"></i> Back</b>
        </a>
      </div>
      <div class="form-group segment_filters">
        <label class="text-muted uppercase col-lg-2"><b>Filter</b></label>
        <div class="col-lg-10" id="addition_filter">
          <!-- Segment addition filter -->
        </div>
      </div>
      <div class="form-group segment_filters">
        <label class="text-muted uppercase col-lg-2"><b></b></label>
        <div class="col-lg-10" id="addition_filter">
          <a class="" href="javascript:void(0);" onclick="appendAdditionFilter()">+ Add filter</a>
        </div>
      </div>
      <div class="form-group segment_buttons">
        <label class="text-muted uppercase col-lg-2">
          <button class="btn btn-fill btn-primary">Excute</button>
        </label>
      </div>
    </div>
  </div>
@endsection

