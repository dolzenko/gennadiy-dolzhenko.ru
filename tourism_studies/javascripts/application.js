$(function() {
  $(".articles.index .item").hover(function() {
    $(this).find(".controls:first").toggle();
  });

  $("#container").prepend('<div class="tooltip">&nbsp;</div>');
  
  $(".article .illustration").each(function() {
    var img = $(this);
    
    img.css("cursor", "pointer");
    
    var title = img.attr("alt");
    if (title.length > 50)
      title = title.substr(0, 49) + "...";

    img.attr("alt", "");

    img.fancybox({ overlayOpacity: 0.7, overlayColor: "#111", href: img.attr("src"), title: title });

    img.attr("title", "Кликните по изображению чтобы увеличить");
    
    img.tooltip({
        tip: ".tooltip",
        effect: "fade",
        fadeOutSpeed: 100,
        predelay: 400,
        position: "top center",
        relative: true,
        offset: [50, 0]
    });
  });

  //,
  //    on_rewind: function() {
  //      location.href = $("a.next:first").attr("href"); // go to next slideshow
  //      return false; // cancel transition to the first frame
  //    }
  
  $("#slideshow").galleryView({
    panel_width: 720,					//INT - width of gallery panel (in pixels)
    panel_height: 520,					//INT - height of gallery panel (in pixels)
    frame_gap: 0
  });
});
