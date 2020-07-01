/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import $ from 'jquery';

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');


// Gestion des likes et dislikes
$("a.js-like-display").each(function(index, element) {
    $(element).click(function(e) {
        e.preventDefault();
        let url = this.href;
        let requestType;
        let idPublication = $(this).data("idpub");
        url.includes("dislike") ? requestType = "delete" : requestType = "post";
        $.ajax({
            url: url,
            type: requestType,
            data: {"id-pub": idPublication},
            success: function(response) {
                $(element).find("span.compteur").html(response.likes);
                $(element).attr("href", response.newRoute);
                if($(element).find("i").attr("class").includes("fas")) {
                    paramDisplay(element, "Liker", "fas fa-thumbs-up", "far fa-thumbs-up")
                } 
                else {
                    paramDisplay(element, "Vous avez liké", "far fa-thumbs-up", "fas fa-thumbs-up");
                }
            },
            error: function(xhr, message, status) {
                console.log(message);
            }
        })
    })
})

// Gestion de l'affichage "Qui a liké ?"
$("span.compteur").each(function(index, element) {
    $(element).hover(function(e) {
        let url = $(this).attr("href");
        $.ajax({
            url: url,
            success: function(response) {
                $(element).attr({"data-toggle": "tooltip", "data-placement": "bottom", title: response});
            }, error(xhr, message, status) {
                console.log(message);
            }
        })
    },function(e){}
    )
})

//Gestion de l'ajout de commentaires
$(".form-com").each(function(index, element) {
    $(element).submit(function(e){
        e.preventDefault();
        let data = $(this).serialize();
        let url = $(this).attr("action");
        let parent = $(this).prev();
        let val = $(element).children().val();
        if(val == "") {
            $(element).children().addClass("is-invalid");
            createDiv($(element), "Il faut 1 caractère au minimum.");
            $(element).children().focusout(function(e) {
                $("#tempdiv").remove();
                $(element).children().removeClass("is-invalid");
            })
        }
        else {
            $.post({
                url: url,
                data: data,
                success: function(response) {
                    let p = createNewCommentDisplay(response);
                    parent.append(p);
                    $(element).children().val("");
                },
                error: function(xhr, message, status) {
                    console.log(message);
                }
            })
        }
    })
})

function createDiv(selectedDiv, message) {
    let div=$('<div>').attr({'id':'tempdiv', 'class':'invalid-feedback'});
    div.html(message);
    $(selectedDiv).append(div);
};

//Gestion des notifs
$("#notifs-pub, #notifs-like, #notifs-com").click(function(e){
    let url = $(this).children().data("href"); // href dans data pour éviter le prevent.Default(), dropDown à afficher;
    let targetNotifsCounter = $(this).children().children("span");
    let targetNotifsIcon = $(this).children().children("i");
    $.post({
        url: url,
        data: {statut: 1},
        success: function(response) {
            if(response == 0) {
                targetNotifsCounter.remove();
                targetNotifsIcon.removeClass("text-white");
                targetNotifsIcon.addClass("text-dark");
            }
        },
        error: function(xhr, message, status) {
            console.log(message);
        }
    })
})

$(".del-pub").each(function(index, element) {
    $(element).click(function(e) {
        e.preventDefault();
        let url = $(this).attr("href");
        let idPub = $(this).data("idpub");
        $.ajax({
            url: url,
            type: "DELETE",
            data: {"id-pub": idPub},
            success: function(response) {
                console.log(response);
                if(response == "done") {
                    $(element).parent().parent().parent().parent().remove();
                }
            },
            error: function(xhr, message, status) {
                console.log(message);
            }
        })
    })
})

$('#form-pub').submit(function(e) {
    e.preventDefault();
    let url = $(this).parent().data('href');
    let data = $(this).serialize();
    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        success: function(response) {
            console.log(response)
        }, 
        error: function(xhr, message, status) {
            console.log(message);
        }
    })
})

// fonctions
function createNewCommentDisplay(response) {
    let p = $("<p>").attr({class: "small text-break"});
    let span1 = $("<span>").attr({style: "color: rgb(68, 85, 199); font-size: 11px; font-family: 'RalewayEB'"});
    span1.html(response.user + " ");
    let span2 = $("<span>").html(response.comment);
    p.append(span1);
    p.append(span2);
    return p;
}
 
function paramDisplay(element, statut, previousClass, newClass) {
    $(element).prev().html(statut);
    $(element).find("i").removeClass(previousClass);
    $(element).find("i").addClass(newClass);
}



