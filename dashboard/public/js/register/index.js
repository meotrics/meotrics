var laravel = new Vue({
    el: '#register',
    data : {
        info:{
            email: '',
            phone: '',
            website: '',
            job: 'Marketing Executive',
            agree: false
        },
        message: '',
        listcode: listphonecode,
        codephone: '+84'
    },
    methods:{

    },

    computed: {
        errors: function(){
            var _self = this;
            //for(var key in this.info){
            //    console.log(this.info[key]);
            //    if(! this.info[key]) return true;
            //}
            console.log(this.info.phone.toString().length);
            if(this.info.phone.toString().length >= 9 || !this.info.phone){
                _self.message = "";
                return false;
            }
            this.message= "Wrong type phone number";
            return true;
        }
    }

});

function onPageLoad(fn) {
    if (window.addEventListener)
        window.addEventListener('load', fn, false);
    else if (window.attachEvent)
        window.attachEvent('onload', fn);
}
function loading() {
    $('.signinbtn').removeClass('blue');
    $('.signinbtn').prop('type', 'button');
    $('.signinbtn').find('.label').html('Loading ...');
    $('.signinbtn').css('cursor', 'default');
}

function ready() {
    $('.signinbtn').addClass('blue');
    $('.signinbtn').prop('type', 'submit');
    $('.signinbtn').find('.label').html('Sign up');
    $('.signinbtn').css('cursor', 'pointer');
}

function error(err) {
    alert("Oh snap, Meotrics just crash, please send this 'JSON.stringify(err, undefined, 2)' to support@meotrics.com, thanks");
}

onPageLoad(function () {

    $('#newsite').prop('checked', true);
    $('#oldsite').removeAttr('checked');


    loading();
    gapi.load('auth2', function () {
        auth2 = gapi.auth2.init({
            client_id: '102248826764-hvb3ej6gj2cn04upgtfrs8eja7djb6bu.apps.googleusercontent.com',
            scope: 'profile'
        });

        auth2.then(function(){

            if (auth2.isSignedIn.get()) {
                onSignIn(auth2.currentUser.get());
                return;
            }
            else ready();

            auth2.attachClickHandler(document.getElementById('gsin'), {}, onRegisterGG, error);
        });
    })
});

function onRegisterGG(googleUser){
    var profile = googleUser.getBasicProfile();

    document.getElementById('ggmes').innerText = 'Loading, please wait, ' + googleUser.getBasicProfile().getName();
    $.post('/auth/googlesignin', {
        id: profile.getId(),
        id_token: googleUser.getAuthResponse().id_token,
        sitename: $('input[name="sitename"]').val(),
        siteurl: $('input[name="siteurl"]').val(),
        newsite: $('#newsite').prop('checked')
    }, function (url) {
        window.location.href = url;
    });
}
function onSignIn(googleUser) {
    var profile = googleUser.getBasicProfile();

    document.getElementById('ggmes').innerText = 'Signing in, please wait, ' + googleUser.getBasicProfile().getName();
    $.post('/auth/googlesignin', {
        id: profile.getId(),
        id_token: googleUser.getAuthResponse().id_token
    }, function (url) {
        window.location.href = url;
    });
}
