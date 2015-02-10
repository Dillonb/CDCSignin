function initialize()
{
    $('#modalSubmitSuccess').on('hidden.bs.modal', function ()
    {
        $(".signinform").trigger("reset");
        window.clearTimeout($("#modalSubmitSuccess").closeTimeout);
    });
}
function closeModal()
{
    $("#modalSubmitSuccess").modal("hide");
    // Ensure that everything is all set for real
    $('body').removeClass("modal-open");
    $('.modal-backdrop').remove();
}
function processForm()
{
    $(".formerrors").css("display","none");
    var has_errors = false;

    var netid = $("#inputNetid");
    var firstname = $("#inputFirstName");
    var lastname = $("#inputLastName");
    var phonenumber = $("#inputContactPhone");
    var email = $("#inputEmail");
    var description = $("#inputDescription");

    var requiredFields = ["inputFirstName","inputLastName","inputContactPhone","inputEmail","inputDescription","inputTosAgree"];
    var errorFields = []
    for (var i = 0; i < requiredFields.length; i++)
    {
        response = processRequiredField(requiredFields[i]);
        if (response)
        {
            has_errors = true;
            errorFields.push(response);
        }
    }
    if (has_errors)
    {
        $(".formerrors").text("Please ensure that all required fields are filled out.");
        $(".formerrors").css("display","block");
    }
    else
    {
        var formData = $(".signinform").serialize();
        $.ajax({
            type:"POST",
            url:"process_signin.php",
            data:formData,
            success: function(data)
                    {
                        $("#btnClose").css("display","block");
                        $("#btnCloseLoading").css("display","none");
                        $("#modalSubmitSuccess").closeTimeout = window.setTimeout(closeModal,5000);
                    },
            error: function(xhr, ajaxOptions, thrownError)
                    {
                        alert("ERROR: " + thrownError);
                    }
        });
        $("#btnClose").css("display","none");
        $("#btnCloseLoading").css("display","block");
        $("#modalSubmitSuccess").modal();
    }
}
function processRequiredField(field)
{
    errors = false;
    f = $("#"+field);

    if (field == "inputTosAgree") {
        if (!f.is(':checked')) {
            errors = true;
        }
    }
    else {
        if (!f.val()) {
            errors = true;
        }
        else {
            errors = false;
        }
    }

    if (errors)
    {
        f.parent().parent().addClass("has-error");
    }
    else
    {
        f.parent().parent().removeClass("has-error");
    }
    // Do special error checking for certain fields
    // Phone number (regex?)
    // Email address (regex.)

    // Finally, return the name of the failed field.
    if (errors)
    {
        // Get the textual name of the failed field
        return f.parent().parent().first().first().text();
    }
}
