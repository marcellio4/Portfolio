const jsonObject = obj => {
    try {
        JSON.parse(obj)
        return true;
    } catch (e) {
        console.log(e)
        return false;
    }
}

const showVal = (id, newValue) => $(id).text(newValue);
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

    //delete a href for story in administrative page
    $('#collapseOne').find('a.delete').remove();
    // Submit contact form
    const contactForm = $("#form").validate({
        rules: {
            title: {
                required: ($('#title').val() === '')
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
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 12
                },
                confirmPass: {
                    required: true,
                    minlength: 6,
                    maxlength: 12,
                    equalTo: "#password"
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    url: "index.php?page=reset",
                    type: form.method,
                    data: $(form).serialize(),
                    success: function (data) {
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
                let data = {};
                $(form).serializeArray().map(x => data[x.name] = x.value)
                const obj = {
                    projects: data,
                    action: 'add'
                }
                $.ajax({
                    url: 'index.php?page=action',
                    type: form.method,
                    data: obj,
                    success: data => {
                        console.log(data)
                        window.location.reload()
                    },
                    error: data => {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    // add Story
    $('body').on('click', '.addStory', () => {
        $('#addStoryForm').validate({
            submitHandler: function (form) {
                let fd = new FormData($(form)[0]);
                fd.append('action', 'add');
                $.ajax({
                    url: 'index.php?page=action',
                    type: form.method,
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: data => {
                        if (jsonObject(data)) {
                            display_errors(data);
                            return false;
                        }
                        window.location.reload();
                    },
                    error: data => {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    // add Skills
    $('body').on('click', '.addSkill', () => {
        $('#addSkillForm').validate({
            submitHandler: function (form) {
                let data = {};
                $(form).serializeArray().map(x => data[x.name] = x.value)
                const obj = {
                    skills: data,
                    action: 'add'
                }
                $.ajax({
                    url: 'index.php?page=action',
                    type: form.method,
                    data: obj,
                    success: data => {
                        if (jsonObject(data)) {
                            display_errors(data);
                            return false;
                        }
                        window.location.reload();
                    },
                    error: data => {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    // edit Project
    $('.edit').click(function () {
        let id = $(this).data('id');
        $('#editProjectModal').find('button.editProject').attr('data-id', id);
    })
    $('body').on('click', '.editProject', () => {
        $('#editProjectForm').validate({
            submitHandler: function (form) {
                let data = {};
                $(form).serializeArray().map(x => data[x.name] = x.value)
                const obj = {
                    projects: data,
                    id: $('#editProjectModal').find('button.editProject').attr('data-id'),
                    action: 'edit'
                }
                $.ajax({
                    url: 'index.php?page=action',
                    type: form.method,
                    data: obj,
                    success: data => {
                        console.log(data);
                        if (jsonObject(data)) {
                            display_errors(data);
                            return false;
                        }
                        window.location.reload();
                    },
                    error: data => {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    // edit Story
    $('.edit').click(function () {
        let id = $(this).data('id');
        $('#editStoryModal').find('button.editStory').attr('data-id', id);
    })
    $('body').on('click', '.editStory', () => {

        $('#editStoryForm').validate({
            submitHandler: function (form) {
                let id = $('#editStoryModal').find('button.editStory').attr('data-id'),
                fd = new FormData($(form)[0]);
                fd.append('action', 'edit');
                fd.append('id', id.toString());
                $.ajax({
                    url: 'index.php?page=action',
                    type: form.method,
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: data => {
                        if (jsonObject(data)) {
                            display_errors(data);
                            return false;
                        }
                        window.location.reload();
                    },
                    error: data => {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    // edit Skills
    $('.edit').click(function () {
        let id = $(this).data('id');
        $('#editSkillsModal').find('button.editSkill').attr('data-id', id);
    })
    $('body').on('click', '.editSkill', () => {
        $('#editSkillForm').validate({
            submitHandler: function (form) {
                let data = {};
                $(form).serializeArray().map(x => data[x.name] = x.value)
                const obj = {
                    skills: data,
                    id: $('#editSkillsModal').find('button.editSkill').attr('data-id'),
                    action: 'edit'
                }
                $.ajax({
                    url: 'index.php?page=action',
                    type: form.method,
                    data: obj,
                    success: data => {
                        if (jsonObject(data)) {
                            display_errors(data);
                            return false;
                        }
                        window.location.reload();
                    },
                    error: data => {
                        console.log(data)
                    }
                })
                return false;
            }
        });
    })

    // delete Project
    $('.delete').click(function () {
        let id = $(this).data('id');
        $('#deleteProjectModal').find('button.deleteProject').attr('data-id', id);
    })
    $('body').on('click', '.deleteProject', () => {
        const obj = {
            projects: 'delete',
            id: $('#deleteProjectModal').find('button.deleteProject').attr('data-id'),
            action: 'delete'
        }
        $.ajax({
            url: 'index.php?page=action',
            type: 'post',
            data: obj,
            success: data => {
                console.log(data)
                window.location.reload();
            },
            error: data => {
                console.log(data)
            }
        })
    })

    // delete Story
    // $('.delete').click(function () {
    //     let id = $(this).data('id');
    //     $('#deleteStoryModal').find('button.deleteStory').attr('data-id', id);
    // })
    // $('body').on('click', '.deleteStory', () => {
    //     let fd = {
    //         action: 'delete',
    //         story: 'story',
    //         id: $('#deleteStoryModal').find('button.deleteStory').attr('data-id'),
    //     };
    //     $.ajax({
    //         url: 'index.php?page=action',
    //         type: 'post',
    //         data: fd,
    //         success: data => {
    //             console.log(data)
    //             return false;
    //             if (jsonObject(data)) {
    //                 display_errors(data);
    //                 return false;
    //             }
    //             window.location.reload();
    //         },
    //         error: data => {
    //             console.log(data)
    //         }
    //     })
    // })

//delete Skills
    $('.delete').click(function () {
        let id = $(this).data('id');
        $('#deleteSkillModal').find('button.deleteSkill').attr('data-id', id);
    })
    $('body').on('click', '.deleteSkill', () => {
        const obj = {
            skills: 'delete',
            id: $('#deleteSkillModal').find('button.deleteSkill').attr('data-id'),
            action: 'delete'
        }
        $.ajax({
            url: 'index.php?page=action',
            type: 'post',
            data: obj,
            success: data => {
                window.location.reload();
            },
            error: data => {
                console.log(data)
            }
        })
    })

})// End of document listen