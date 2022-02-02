(function($) {
    $(function() {
        $(".slug").keyup(function(){
            if (this.value.search(/^[a-z0-9-]+$/) == -1)
            { 
                //$(".slug_div").html('Only lowercase characters, digits, dash allowed.');
                return false;
            }
            $.ajax({
                method: "GET",
                url: BASE_URL + 'course/get-slug',
                data: { slug: $("#slug").val() },
                success:function(result) {
                    //$(".page_content_div").html(result);
                    var res = JSON.parse(result);
                    var newres = res.result[0];
                    if(newres != null && newres !=''){
                        $(".slug_div").html(res.result.config+'#/sign-in/'+newres.slug+ '  already exists.');
                    }else{
                        $(".slug_div").html(res.result.config+'#/sign-in/'+$("#slug").val()+ ' is your new URL.')    
                    }
                    
                }
            });
        });
    });
})(jQuery);