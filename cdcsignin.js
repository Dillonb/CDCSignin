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
    $("#modalSubmitSuccess").modal("toggle");
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

    var requiredFields = ["inputFirstName","inputLastName","inputContactPhone","inputEmail","inputDescription"];
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
                        $("#modalSubmitSuccess").closeTimeout = window.setTimeout(closeModal,5000);
                    },
            error: function(xhr, ajaxOptions, thrownError)
                    {
                        alert(thrownError);
                    }
        });
        $("#modalSubmitSuccess").modal();
    }
}
function processRequiredField(field)
{
    errors = false;
    f = $("#"+field);
    if (!f.val())
    {
        errors = true;
        f.parent().parent().addClass("has-error");
    }
    else
    {
        f.parent().parent().removeClass("has-error");
        errors = false;
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
