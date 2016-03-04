var fakedata = [
    {name:"Purchase", fields: ["Price","Amount","Pid","Uid"]},
    {name:"Pageview", fields: ["Url","Referal"]}
];

/**
 * element
 */
var segment_e = $("[name='segment']");
var action_e = $("[name='action']");
var field_e = $("[name='field']");

/**
 * gen data
 */


action_e.select2({

});

var action_data = [];
for(var i in fakedata){
    action_data.push({id: i, text: fakedata[i].name});
}

var field_data = {};
for(var i in fakedata){
    var fields = fakedata[i]['fields'];
    field_data[i] = [];
    for(var j in fields){
        field_data[i].push({id:j, text: fields[j]})
    }
}

//var action_data= [];
//for(i = 0; i < fakedata.length; i++){
//    action_data.id = i;
//    action_data.text = fakedata[i]['name'];
//    //alert(fakedata[i]['name']);
//
//}



//var field_data = [];

$(document).ready(function (){

    /**
     * init data to control
     */
    segment_e.select2({
        data : [
            { id : 0, text : "Old man" },
            { id : 1, text : "Teenager" }
        ]
    });

    action_e.select2({
        data : action_data
    });


    //field_e.prop("disabled", true);



    /**
     *
     */

    action_e.select2().on("select2:select", function() {
        field_e.select2().empty();

        field_e.select2({
            data : field_data[$(this).val()]
        });

        console.log(field_data[$(this).val()]);
    });



    /**
     * Bookmark
     */
    var bookmark = false;
    var bookmark_e = $("#bookmark");

    bookmark_e.click(function(){
        bookmark == false ? bookmark = true : bookmark = false;

        if(bookmark==true){
            $(this).addClass('bookmarked');
        }else {
            $(this).removeClass('bookmarked');
        }

        $(this).blur();
    });

});


