function specView(id) {
      $(".spec_view").colorbox({
          inline:true, 
          href:"#spec_table_"+id,
          opacity: 0.5,
          maxWidth:"80%", 
          maxHeight:"80%",
          top: "10%"
      });
      return false;
}

function messView(id) {
      $(".mess_view").colorbox({
          inline:true, 
          href:"#mess_"+id,
          opacity: 0.5,
          maxWidth:"80%", 
          maxHheight:"80%",
          top: "10%"
      });
      return false;
}

function userView(id) {
      $(".user_view").colorbox({
          inline:true, 
          href:"#user_"+id,
          opacity: 0.5,
          maxWidth:"80%", 
          maxHheight:"80%",
          top: "10%"
      });
      return false;
}

$(document).ready(function(){
                    $(".t_prov_actions a").each(function(){
                        var title = $(this).attr('title');
                        $(this).attr('title','')
                        
                        var titlewrap = $("<div class='titlewrap'><span>"+title+"</span><i></i></div>").hide()
                        
                        $(this).append(titlewrap)
                        
                        $(this).hover(function(){
                            titlewrap.show();
                        },function(){
                            titlewrap.hide()
                        })
                    })
});