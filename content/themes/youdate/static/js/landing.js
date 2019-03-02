$(document).ready(function() {

    var lpJoinAnimationDone = true,
        cssShow = 'fadeInDown',
        cssHide = 'fadeOutUp',
        firstStep = $('.landing-page-signup-form .step-1'),
        secondStep = $('.landing-page-signup-form .step-2');

    $('.landing-page-signup-form .btn-continue').on('click', function (event) {
        if (!lpJoinAnimationDone) {
            return false;
        }
        if (secondStep.hasClass('hide')) {
            secondStep.addClass('animated ' + cssShow);
            secondStep.removeClass('hide');
            firstStep.addClass('animated ' + cssHide);
            lpJoinAnimationDone = false;
            delay(function(){
                secondStep.removeClass('animated ' + cssShow);
                firstStep.removeClass('animated ' + cssHide).addClass('hide');
                lpJoinAnimationDone = true;
                secondStep.find('input[type=text]')[0].focus();
            }, 500);
        }
        event.preventDefault();
    });

    $('.landing-page-signup-form .btn-back').on('click', function (event) {
        if (!lpJoinAnimationDone) {
            return false;
        }
        if (firstStep.hasClass('hide')) {
            firstStep.addClass('animated ' + cssShow);
            firstStep.removeClass('hide');
            secondStep.addClass('animated ' + cssHide);
            lpJoinAnimationDone = false;
            delay(function(){
                firstStep.removeClass('animated ' + cssShow);
                secondStep.removeClass('animated ' + cssHide).addClass('hide');
                lpJoinAnimationDone = true;
            }, 500);
        }
        event.preventDefault();
    });

    $('.landing-page-signup-form .btn-sex').on('click', function (event) {
        $('.registration-sex').val($(this).data('sex')).trigger('change');
        $('.btn-sex').removeClass('active');
        $(this).toggleClass('active');
    });

    var $stepOneElements = $('.landing-page-signup-form .step-1 input, .landing-page-signup-form .step-1 select');
    $stepOneElements.on('change', function (event) {
        var emptyElements = $stepOneElements.filter(function() {
            return $(this).val() == "";
        });
        if (emptyElements.length == 0) {
            $('.btn-continue').removeClass('btn-disabled btn-secondary').addClass('btn-primary').attr('disabled', false);
        } else {
            $('.btn-continue').addClass('btn-disabled btn-secondary').removeClass('btn-primary').attr('disabled', 'disabled');
        }
    });
});
