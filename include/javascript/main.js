const jsonObject = obj => {
    try {
        JSON.parse(obj)
        return true;
    } catch (e) {
        console.log(e)
        return false;
    }
}

const showVal = (id,newValue) => $(id).text(newValue);
const removeErrors = () => $('label.error').remove();
const focus_first_ele = () => $('.error:first').focus();
const changeClass = (id, name, exchange) => {
    $(`#${id}`).removeClass(`${name}`);
    $(`#${id}`).addClass(`${exchange}`);
}
$('[data-toggle="tooltip"]').tooltip();

const display_errors = obj => {
    removeErrors();
    $.map(JSON.parse(obj), (value, key) => {
        let label = `<label id="${key}-error" class="error" for="${key}">${value}</label>`
        $(label).insertAfter(`#${key}`);
        changeClass(key, 'valid', 'error');
    });
    focus_first_ele();
}

$(document).ready(() => {

    // slide slow our navigation bar for mobiles
    $('body').on('.menu', 'click', () => {
        $('ul').slideToggle('slow', () => $('ul').toggleClass('active'))
    })

    // Submit contact form
    const contactForm = $("#form").validate({
        rules: {
            title: {
                required: ($('#title').val() === '')
            },
            fname: {
                required: true
            },
            sname: {
                required: true
            },
            sub: {
                required: true
            },
            msg: {
                required: true
            },
            email: {
                required: true,
                email: true
            }
        },
        submitHandler: function (form) {
            $.ajax({
                url: 'index.php?page=contact',
                type: 'POST',
                data: $(form).serialize(),
                success: function (data) {
                    if (jsonObject(data)) {
                        display_errors(data);
                        return false;
                    }
                    contactForm.destroy();
                    window.location.reload();
                },
                error: function (data) {
                    let paragraphElement = "<p class='message-error'></p>";
                    ($('.message-error') !== 'undefined') ? $('.message-error').append('</br> New message') : $('span.submit-message').append(paragraphElement);
                    console.log(data);
                }
            });
            return false;
        }
    })

    // Login credential and form
    $('body').on('click', '.login', () => {
       const loginForm = $('#loginForm').validate({
            rules: {
                mail: {
                    required: true
                },
                pass: {
                    required: true
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    url: "index.php?page=login",
                    type: "POST",
                    data: $(form).serialize(),
                    success: function (data) {
                        console.log(data)
                        if (jsonObject(data)) {
                            display_errors(data);
                            return false;
                        }
                        loginForm.destroy();
                        window.location.reload();
                    },
                    error: function (data) {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    // Reset your password
    $('body').on('click', '.resetPass', () => {
        $('#confirmPassword').validate({
            rules: {
                password : {
                    required: true,
                    minlength : 6,
                    maxlength: 12
                },
                confirmPass : {
                    required: true,
                    minlength : 6,
                    maxlength: 12,
                    equalTo : "#password"
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    url: "index.php?page=reset",
                    type: form.method,
                    data: $(form).serialize(),
                    success: function (data) {
                        console.log(data)
                        if (jsonObject(data)) {
                            display_errors(data);
                            return false;
                        }
                        window.location.reload();
                    },
                    error: function (data) {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    showVal('.knowledgeValue', $('#editKnowledge').val())
    showVal('.knowledgeValue', $('#knowledge').val())

    // add Project
    $('body').on('click', '.addProject', () => {
        $('#addProjectForm').validate({
            submitHandler: function (form) {
                console.log(form.serialize());
                return false;
            }
        });
    })

    // add Story
    $('body').on('click', '.addStory', () => {
        $('#addStoryForm').validate({
            submitHandler: function (form) {
                console.log(form.serialize());
                return false;
            }
        });
    })

    // add Skills
    $('body').on('click', '.addSkill', () => {
        $('#addSkillForm').validate({
            submitHandler: function (form) {
                console.log(form.serialize());
                return false;
            }
        });
    })
})// End of document listen