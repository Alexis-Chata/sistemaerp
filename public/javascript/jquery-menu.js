
 $(document).ready(function(){
  $("#accordion h3:first").addClass("active");
  $("#accordion div:not(:first)").hide();
  $("#accordion h3").click(function(){
    $(this).next("div").slideToggle("slow")
    .siblings("div:visible").slideUp("slow");
    $(this).toggleClass("active");
    $(this).siblings("h3").removeClass("active");
  });

  
  
  $("#acordeon h2:first").addClass("active");
  $("#acordeon div:not(:first)").hide();
  $("#acordeon h2").click(function(){
    $(this).next("div").slideToggle("slow")
    .siblings("div:visible").slideUp("slow");
    $(this).toggleClass("active");
    $(this).siblings("h2").removeClass("active");
  });


});
