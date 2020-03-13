jQuery(document).ready(function ($) {
    var _SlideshowTransitions = [{
        $Duration: 1200,
        $Opacity: 2
    }];

    var options = {
        $StartIndex: <?php echo (!$_GET['ss']) ? 0 : $_GET['ss']; ?>,
        $FillMode: 0,
        $AutoPlay: true,
        $AutoPlayInterval: 5000,
        $PauseOnHover: 1,
        $ArrowKeyNavigation: true,
        $SlideEasing: $JssorEasing$.$EaseOutQuint,
        $SlideDuration: 1200,
        $MinDragOffsetToSlide: 20,
        $SlideWidth: 1280,
        $SlideHeight: 410,
        $SlideSpacing: 0,
        $DisplayPieces: 1,
        $ParkingPosition: 0,
        $UISearchMode: 1,
        $PlayOrientation: 1,
        $DragOrientation: 1,
        $SlideshowOptions: {
            $Class: $JssorSlideshowRunner$,
            $Transitions: _SlideshowTransitions,
            $TransitionsOrder: 1,
            $ShowLink: true
        },
        $BulletNavigatorOptions: {
            $Class: $JssorBulletNavigator$,
            $ChanceToShow: 2,
            $AutoCenter: 0,
            $Steps: 1,
            $Lanes: 1,
            $SpacingX: 8,
            $SpacingY: 8,
            $Orientation: 1
        },
        $ArrowNavigatorOptions: {
            $Class: $JssorArrowNavigator$,
            $ChanceToShow: 1,
            $AutoCenter: 0,
            $Steps: 1
        }
    };

    var jssor_slider1 = new $JssorSlider$("slider1", options);

    function ScaleSlider() {
        var bodyWidth = document.body.clientWidth;

        if (bodyWidth) {
            jssor_slider1.$ScaleWidth(Math.min(bodyWidth, 1280));
        } else {
            window.setTimeout(ScaleSlider, 30);
        }
    }

    ScaleSlider();

    $(window).bind("load", ScaleSlider);
    $(window).bind("resize", ScaleSlider);
    $(window).bind("orientationchange", ScaleSlider);

    $(".jssorloader").css("display", "none");
    $(".jssorslider").css("display", "block");
});
